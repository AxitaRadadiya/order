<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddRestoreToInventoryLogsTypeEnum extends Migration
{
    public function up()
    {
        // For MySQL, you need to change the ENUM definition
        DB::statement("ALTER TABLE inventory_logs MODIFY COLUMN type ENUM('restock', 'deduct', 'restore') NOT NULL");
    }

    public function down()
    {
        // Revert to original ENUM values (without 'restore')
        DB::statement("ALTER TABLE inventory_logs MODIFY COLUMN type ENUM('restock', 'deduct') NOT NULL");
    }
}