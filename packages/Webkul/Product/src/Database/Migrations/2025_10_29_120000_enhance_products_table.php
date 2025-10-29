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
        Schema::table('products', function (Blueprint $table) {
            // Product Classification
            $table->enum('type', ['product', 'service', 'digital'])->default('product')->after('name');
            $table->string('reference')->nullable()->after('sku'); // Internal reference
            $table->string('barcode')->nullable()->after('reference'); // EAN13/UPC
            
            // Financial Information  
            $table->decimal('cost', 15, 4)->nullable()->after('price'); // Purchase cost
            
            // Physical Properties
            $table->decimal('volume', 15, 4)->nullable()->after('cost'); // m³
            $table->decimal('weight', 15, 4)->nullable()->after('volume'); // kg
            
            // Business Rules
            $table->boolean('enable_sales')->default(true)->after('weight');
            $table->boolean('enable_purchase')->default(true)->after('enable_sales');
            $table->boolean('is_favorite')->default(false)->after('enable_purchase');
            
            // Content & Media
            $table->json('images')->nullable()->after('is_favorite'); // ["image1.jpg", "image2.jpg"]
            $table->text('description_purchase')->nullable()->after('description');
            $table->text('description_sale')->nullable()->after('description_purchase');
            
            // Status Management
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active')->after('description_sale');
            
            // Indexes for performance
            $table->index(['type', 'status']);
            $table->index(['enable_sales', 'status']);
            $table->index(['enable_purchase', 'status']);
            $table->index('is_favorite');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['type', 'status']);
            $table->dropIndex(['enable_sales', 'status']);
            $table->dropIndex(['enable_purchase', 'status']);
            $table->dropIndex(['is_favorite']);
            
            // Drop columns
            $table->dropColumn([
                'type', 'reference', 'barcode', 'cost', 'volume', 'weight',
                'enable_sales', 'enable_purchase', 'is_favorite', 'images',
                'description_purchase', 'description_sale', 'status'
            ]);
        });
    }
};