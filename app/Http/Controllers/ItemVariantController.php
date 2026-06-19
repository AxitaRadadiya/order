<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\ItemVariant;
use App\Models\InventoryLog;

class ItemVariantController extends Controller
{
    public function sizesByColor(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'integer', 'exists:items,id'],
            'color_id' => ['required', 'integer', 'exists:colors,id'],
        ]);

        // Eager load the size relationship.
        $variants = \App\Models\ItemVariant::with('size')
            ->where('item_id', $validated['item_id'])
            ->where('color_id', $validated['color_id'])
            ->get();

        $sizes = $variants
            ->groupBy('size_id')
            ->map(function ($group) {
                $size = $group->first()->size;
                $available = $group->sum(function ($variant) {
                    return $variant->current_stock;
                });

                return [
                    'size_id' => (int) $group->first()->size_id,
                    'label' => (string) ($size->name ?? ''),
                    'available_qty' => (int) $available,
                ];
            })
            ->filter(function ($row) {
                return $row['label'] !== '';
            })
            ->values()
            ->sortBy('label')
            ->values();

        return response()->json([
            'sizes' => $sizes->values()->all(),
        ]);
    }

    public function restock(Request $request)
    {
        $validated = $request->validate([
            'item_variant_id' => ['required', 'integer', 'exists:item_variants,id'],
            'qty' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $variant = ItemVariant::query()->findOrFail($validated['item_variant_id']);
        $addQty = (int) $validated['qty'];

        DB::transaction(function () use ($variant, $addQty, $validated) {
            InventoryLog::create([
                'item_variant_id' => $variant->id,
                'order_master_id' => null,
                'type' => 'restock',
                'qty' => $addQty,
                'note' => $validated['note'] ?? null,
                'created_by' => auth()->id(),
            ]);
        });

        return response()->json([
            'success' => true,
            'new_qty' => $variant->fresh()->current_stock,
        ]);
    }
}