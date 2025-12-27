<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("brand_kit_overrides", function (Blueprint $table) {
            $table->id();

            $table->string("scope_key", 50)->default("global"); // multi-tenant ready
            $table->string("theme_slug", 50)->default("default");

            $table->string("override_key", 100); // ex: 'color_primary' (renomeado de 'key')

            $table->text("value")->nullable(); // TEXT simples (MVP)

            $table->boolean("is_active")->default(true);

            $table->unsignedBigInteger("updated_by")->nullable();
            $table->index("updated_by", "bko_updated_by"); // index para auditoria

            $table->timestamps();

            // Evita duplicatas por scope + theme + override_key
            $table->unique(
                ["scope_key", "theme_slug", "override_key"],
                "bko_unique",
            );

            // Lookup rapido
            $table->index(
                ["scope_key", "theme_slug", "is_active"],
                "bko_lookup",
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("brand_kit_overrides");
    }
};
