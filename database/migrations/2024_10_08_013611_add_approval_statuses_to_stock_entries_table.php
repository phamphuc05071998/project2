<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_approval_statuses_to_stock_entries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalStatusesToStockEntriesTable extends Migration
{
    public function up()
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->boolean('create_approved')->default(false);
            $table->boolean('update_approved')->default(false);
            $table->boolean('delete_approved')->default(false);
        });
    }

    public function down()
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->dropColumn(['create_approved', 'update_approved', 'delete_approved']);
        });
    }
}
