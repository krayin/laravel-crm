<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The import forms submitted "Skip errors" as `skip-erros`, which the validation rule now
     * rejects. Rows saved that way already behaved as skip-errors (only `stop-on-errors` is checked
     * for), so renaming them keeps their behaviour and lets the edit form select the option again.
     */
    public function up(): void
    {
        DB::table('imports')
            ->where('validation_strategy', 'skip-erros')
            ->update(['validation_strategy' => 'skip-errors']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
