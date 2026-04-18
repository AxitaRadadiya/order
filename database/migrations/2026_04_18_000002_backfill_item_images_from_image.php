<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('items')) {
            return;
        }

        if (!Schema::hasColumn('items', 'image') || !Schema::hasColumn('items', 'images')) {
            return;
        }

        // Backfill: for items with a legacy `image` but no `images` JSON, copy into images
        $rows = DB::table('items')
            ->whereNotNull('image')
            ->where(function ($q) {
                $q->whereNull('images')->orWhere('images', '[]');
            })
            ->select(['id', 'image'])
            ->get();

        foreach ($rows as $r) {
            if ($r->image) {
                DB::table('items')->where('id', $r->id)->update(['images' => json_encode([$r->image])]);
            }
        }
    }

    public function down(): void
    {
        // No-op: we don't remove images field content on rollback to avoid data loss.
    }
};
