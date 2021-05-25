<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailThreadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_threads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('source');
            $table->string('type');
            $table->string('user_type');
            $table->string('name')->nullable();
            $table->json('reply_to')->nullable();
            $table->json('cc')->nullable();
            $table->json('bcc')->nullable();
            $table->string('message_id')->unique();
            $table->text('reply')->nullable();

            $table->integer('email_id')->unsigned();
            $table->foreign('email_id')->references('id')->on('emails')->onDelete('cascade');

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->integer('person_id')->unsigned()->nullable();
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_threads');
    }
}
