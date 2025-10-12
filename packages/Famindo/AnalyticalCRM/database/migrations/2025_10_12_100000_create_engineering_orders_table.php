<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('engineering_orders', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('organization_id')->nullable()->index();

            $table->date('order_date')->nullable()->index();
            $table->string('status', 50)->default('draft')->index();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Optional: if you have foreign tables `persons`/`organizations`
            // $table->foreign('customer_id')->references('id')->on('persons')->nullOnDelete();
            // $table->foreign('organization_id')->references('id')->on('organizations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('engineering_orders');
    }
};

