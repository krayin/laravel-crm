<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_kit_snapshots', function (Blueprint $table) {
            $table->id();

            $table->string('scope_key', 50)->default('global');
            $table->string('theme_slug', 50);

            $table->string('name', 100);

            $table->unsignedSmallInteger('snapshot_version')->default(1); // facilita migracao de formato

            // Snapshot completo do estado
            $table->json('overrides_data');
            $table->json('custom_css_data')->nullable();

            $table->boolean('is_auto')->default(false);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->index('created_by', 'bks_created_by'); // index para auditoria

            $table->timestamps();

            $table->index(
                ['scope_key', 'theme_slug', 'created_at'],
                'bks_lookup',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_kit_snapshots');
    }
};
