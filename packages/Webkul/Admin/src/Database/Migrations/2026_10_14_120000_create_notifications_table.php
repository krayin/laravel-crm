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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            /**
             * What happened, as `<entity>.<action>` — `lead.created`, `quote.updated`. The entity
             * and action are kept apart from the record so a notification survives the record's
             * deletion and can still be listed.
             */
            $table->string('entity_type');
            $table->string('action');
            $table->unsignedBigInteger('entity_id')->nullable();

            /**
             * The notification is addressed to a user rather than broadcast to a role, so the list
             * can be read back per user without re-deriving who was entitled to see it.
             */
            $table->unsignedInteger('user_id');

            /**
             * Who caused it, kept so the list can say "Jane added a lead" and so a user's own
             * actions can be left out of their feed.
             */
            $table->unsignedInteger('causer_id')->nullable();

            $table->string('title');
            $table->text('description')->nullable();

            /**
             * Where the bell sends the reader. Stored rather than derived, because the route for a
             * record depends on its entity and is not reconstructable from the id alone.
             */
            $table->string('route')->nullable();

            $table->boolean('is_read')->default(false);

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('causer_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            /**
             * The bell's own query: this user's notifications, newest first, unread counted.
             */
            $table->index(['user_id', 'is_read', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
