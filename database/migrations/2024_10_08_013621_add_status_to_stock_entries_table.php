<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_status_to_stock_entries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToStockEntriesTable extends Migration
{
    public function up()
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->string('status')->default('pending');
        });
    }

    public function down()
    {
        Schema::table('stock_entries', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
