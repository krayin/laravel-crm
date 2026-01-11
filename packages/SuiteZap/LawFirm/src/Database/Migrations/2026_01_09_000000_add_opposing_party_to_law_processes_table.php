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
            $table->string('opposing_party_name')->nullable()->after('fase_processual');
            $table->string('opposing_party_type')->default('PF')->comment('PF or PJ')->after('opposing_party_name');
            $table->string('opposing_party_document')->nullable()->after('opposing_party_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn(['opposing_party_name', 'opposing_party_type', 'opposing_party_document']);
        });
    }
};
