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
        Schema::table('webhooks', function (Blueprint $table) {
            $table->dropColumn([
                'method',
                'payload',
                'raw_payload_type',
                'payload_type',
                'headers',
                'query_params',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('webhooks', function (Blueprint $table) {
            $table->string('method');
            $table->json('query_params')->nullable();
            $table->json('headers')->nullable();
            $table->string('payload_type');
            $table->string('raw_payload_type');
            $table->json('payload')->nullable();
        });
    }
};
