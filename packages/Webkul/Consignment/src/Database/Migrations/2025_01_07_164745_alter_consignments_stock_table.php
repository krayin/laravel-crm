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
        Schema::table('consignments_stock', function (Blueprint $table) {
            $table->string('consignment_id')->nullable()->after('id');
            $table->string('product_id')->nullable()->after('consignment_id');
            $table->integer('quantity')->nullable()->after('product_id');
            $table->integer('amount')->nullable()->after('quantity');
            $table->date('date')->nullable()->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consignments_stock', function (Blueprint $table) {
            $table->dropColumn('consignment_id');
            $table->dropColumn('product_id');
            $table->dropColumn('quantity');
            $table->dropColumn('amount');
            $table->dropColumn('date');
        });
    }
};
