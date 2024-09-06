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
        Schema::create('product_inventories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('in_stock')->default(0);
            $table->integer('allocated')->default(0);

            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');

            $table->integer('warehouse_id')->unsigned()->nullable();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');

            $table->integer('warehouse_location_id')->unsigned()->nullable();
            $table->foreign('warehouse_location_id')->references('id')->on('warehouse_locations')->onDelete('SET NULL');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_inventories');
    }
};
