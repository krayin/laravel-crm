<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('apriori_rules', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->json('lhs');
            $table->json('rhs');

            $table->decimal('support', 10, 6)->default(0)->index();
            $table->decimal('confidence', 10, 6)->default(0)->index();
            $table->decimal('lift', 10, 6)->default(0)->index();

            $table->dateTime('period_start')->nullable()->index();
            $table->dateTime('period_end')->nullable()->index();

            $table->json('params_json')->nullable();

            $table->unsignedInteger('created_by')->nullable()->index();

            $table->timestamps();

            $table->foreign('created_by')
                ->references('id')->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apriori_rules');
    }
};

