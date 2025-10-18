<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use single ALTER per table to minimize rebuilds; keep NULL/DEFAULT semantics.

        // Leads
        DB::statement('ALTER TABLE `leads`
            MODIFY `lead_value` DECIMAL(16,2) NULL
        ');

        // Products
        DB::statement('ALTER TABLE `products`
            MODIFY `price` DECIMAL(16,2) NULL
        ');

        // Lead Products
        DB::statement('ALTER TABLE `lead_products`
            MODIFY `price` DECIMAL(16,2) NULL,
            MODIFY `amount` DECIMAL(16,2) NULL
        ');

        // Quotes (amount fields only; percent fields are left untouched)
        DB::statement('ALTER TABLE `quotes`
            MODIFY `discount_amount` DECIMAL(16,2) NULL,
            MODIFY `tax_amount` DECIMAL(16,2) NULL,
            MODIFY `adjustment_amount` DECIMAL(16,2) NULL,
            MODIFY `sub_total` DECIMAL(16,2) NULL,
            MODIFY `grand_total` DECIMAL(16,2) NULL
        ');

        // Quote Items (preserve defaults and nullability)
        DB::statement('ALTER TABLE `quote_items`
            MODIFY `price` DECIMAL(16,2) NOT NULL DEFAULT 0,
            MODIFY `discount_amount` DECIMAL(16,2) NULL DEFAULT 0,
            MODIFY `tax_amount` DECIMAL(16,2) NULL DEFAULT 0,
            MODIFY `total` DECIMAL(16,2) NOT NULL DEFAULT 0
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert each table to original definitions from core migrations (DECIMAL(12,4)).

        // Leads
        DB::statement('ALTER TABLE `leads`
            MODIFY `lead_value` DECIMAL(12,4) NULL
        ');

        // Products
        DB::statement('ALTER TABLE `products`
            MODIFY `price` DECIMAL(12,4) NULL
        ');

        // Lead Products
        DB::statement('ALTER TABLE `lead_products`
            MODIFY `price` DECIMAL(12,4) NULL,
            MODIFY `amount` DECIMAL(12,4) NULL
        ');

        // Quotes
        DB::statement('ALTER TABLE `quotes`
            MODIFY `discount_amount` DECIMAL(12,4) NULL,
            MODIFY `tax_amount` DECIMAL(12,4) NULL,
            MODIFY `adjustment_amount` DECIMAL(12,4) NULL,
            MODIFY `sub_total` DECIMAL(12,4) NULL,
            MODIFY `grand_total` DECIMAL(12,4) NULL
        ');

        // Quote Items
        DB::statement('ALTER TABLE `quote_items`
            MODIFY `price` DECIMAL(12,4) NOT NULL DEFAULT 0,
            MODIFY `discount_amount` DECIMAL(12,4) NULL DEFAULT 0,
            MODIFY `tax_amount` DECIMAL(12,4) NULL DEFAULT 0,
            MODIFY `total` DECIMAL(12,4) NOT NULL DEFAULT 0
        ');
    }
};
