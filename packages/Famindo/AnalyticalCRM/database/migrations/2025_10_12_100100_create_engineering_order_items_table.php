<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('engineering_order_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();

            $table->string('item_code', 191)->nullable()->index();
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 15, 2)->default(0);

            $table->timestamps();

            $table->foreign('order_id')
                ->references('id')->on('engineering_orders')
                ->onDelete('cascade');

            // Optional FK to products table if exists
            // $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engineering_order_items');
    }
};

