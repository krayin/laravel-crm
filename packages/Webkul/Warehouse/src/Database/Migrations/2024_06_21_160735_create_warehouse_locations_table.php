<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('aisle');
            $table->string('bay', 50);
            $table->string('shelf', 50);
            $table->string('bin', 50);
            
            $table->integer('warehouse_id')->unsigned();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');

            $table->unique(['warehouse_id', 'aisle', 'bay', 'shelf', 'bin'], 'warehouse_location_unique');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_locations');
    }
};
