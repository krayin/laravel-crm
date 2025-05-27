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
        Schema::table('persons', function (Blueprint $table) {
            $table->string('partner_2')->nullable()->after('name');  // or appropriate type
            $table->string('partner_3')->nullable()->after('partner_2');
            $table->string('local_agent')->nullable()->after('partner_3');
            sponsor
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persons', function (Blueprint $table) {
            $table->dropColumn(['partner_2', 'partner_3', 'local_agent']);
        });
    }
};
