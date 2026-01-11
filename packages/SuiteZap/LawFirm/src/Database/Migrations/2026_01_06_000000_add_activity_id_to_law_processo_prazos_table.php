<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Defensive check: only add if column doesn't exist
        if (!Schema::hasColumn('law_processo_prazos', 'activity_id')) {
            Schema::table('law_processo_prazos', function (Blueprint $table) {
                $table->unsignedInteger('activity_id')->nullable()->after('concluido_em');
                $table->foreign('activity_id')
                    ->references('id')
                    ->on('activities')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('law_processo_prazos', function (Blueprint $table) {
            $table->dropForeign(['activity_id']);
            $table->dropColumn('activity_id');
        });
    }
};
