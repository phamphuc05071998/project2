<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockEntryTempsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_entry_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_entry_id');


            $table->string('action'); // 'edit' or 'delete'
            $table->timestamps();

            $table->foreign('stock_entry_id')->references('id')->on('stock_entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_entry_temps');
    }
}
