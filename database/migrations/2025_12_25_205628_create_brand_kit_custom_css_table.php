<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("brand_kit_custom_css", function (Blueprint $table) {
            $table->id();

            $table->string("scope_key", 50)->default("global");
            $table->string("theme_slug", 50)->default("default");

            $table->string("name", 100);
            $table->mediumText("css_content"); // ate ~16MB

            $table->string("target", 20)->default("admin"); // 'admin', 'login', 'both' (validacao no Model)

            $table->boolean("is_enabled")->default(false); // seguranca: off por padrao
            $table->integer("priority")->default(100);

            $table->unsignedBigInteger("created_by")->nullable();
            $table->index("created_by", "bkc_created_by"); // index para auditoria

            $table->timestamps();

            $table->index(
                ["scope_key", "theme_slug", "target", "is_enabled"],
                "bkc_lookup",
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("brand_kit_custom_css");
    }
};
