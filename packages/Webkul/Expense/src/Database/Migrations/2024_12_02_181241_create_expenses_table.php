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
        Schema::create('expenses', function (Blueprint $table) {

            $table->id();
            $table->string('expense_type')->nullable();
            $table->string('expense_head')->nullable();
            $table->date('expense_date')->nullable();
            $table->text('description')->nullable();
            $table->string('attachments')->nullable();
            $table->string('mode')->nullable();
            $table->string('expense_by')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('is_reimburse')->nullable();
            $table->boolean('is_verified')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
