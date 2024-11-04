<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('subject');
            $table->boolean('status')->default(0);
            $table->string('type');
            $table->string('mail_to');
            $table->string('spooling')->nullable();
            $table->unsignedInteger('marketing_template_id')->nullable();
            $table->unsignedInteger('marketing_event_id')->nullable();
            $table->timestamps();

            $table->foreign('marketing_template_id')->references('id')->on('email_templates')->onDelete('set null');
            $table->foreign('marketing_event_id')->references('id')->on('marketing_events')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_campaigns');
    }
};
