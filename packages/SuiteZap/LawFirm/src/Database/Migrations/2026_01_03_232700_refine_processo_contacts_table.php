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
        Schema::table('processos', function (Blueprint $table) {
            if (Schema::hasColumn('processos', 'advogado_whatsapp')) {
                $table->renameColumn('advogado_whatsapp', 'whatsapp_advogado_contrario');
            } elseif (!Schema::hasColumn('processos', 'whatsapp_advogado_contrario')) {
                $table->string('whatsapp_advogado_contrario', 20)->nullable();
            }
        });

        Schema::table('processos', function (Blueprint $table) {
            if (!Schema::hasColumn('processos', 'email_advogado_contrario')) {
                // Remove 'after' if it causes issues, or keep it now that we are in a separate block
                // However, separate blocks in same migration method might still be transactionally wrapped?
                // DDL usually auto-commits in MySQL. So this should be fine.
                // But to be absolutely safe and since 'after' is cosmetic, I will remove 'after' or check existence strictly.
                // The error was mostly about timing in the same Blueprint instance.
                $table->string('email_advogado_contrario')->nullable()->after('whatsapp_advogado_contrario');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            if (Schema::hasColumn('processos', 'whatsapp_advogado_contrario')) {
                $table->renameColumn('whatsapp_advogado_contrario', 'advogado_whatsapp');
            }

            if (Schema::hasColumn('processos', 'email_advogado_contrario')) {
                $table->dropColumn('email_advogado_contrario');
            }
        });
    }
};
