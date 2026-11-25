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
        Schema::create('firma_signature_requests', function (Blueprint $table) {
            $table->id();

            /**
             * Null while the send is in flight or when its outcome is unknown.
             */
            $table->string('firma_id', 64)->nullable()->unique();

            /**
             * One key per send dialog, written before the request to Firma, so a repeated
             * submission of the same dialog never sends twice.
             */
            $table->string('idempotency_key', 64)->unique();
            $table->unsignedInteger('lead_id');
            $table->unsignedInteger('quote_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('name');
            $table->string('source', 16);
            $table->string('template_id', 64)->nullable();
            $table->string('template_name')->nullable();
            $table->string('recipient_name');
            $table->string('recipient_email');
            $table->string('status', 16)->index();
            $table->boolean('no_invitation_email')->default(false);
            $table->unsignedInteger('signed_activity_id')->nullable();
            $table->boolean('fetch_failed')->default(false);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('leads')->cascadeOnDelete();
            $table->foreign('quote_id')->references('id')->on('quotes')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('signed_activity_id')->references('id')->on('activities')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firma_signature_requests');
    }
};
