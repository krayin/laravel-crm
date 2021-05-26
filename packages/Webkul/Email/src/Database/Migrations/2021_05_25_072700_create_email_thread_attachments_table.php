<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailThreadAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_thread_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('path');
            $table->integer('size')->nullable();
            $table->string('content_type')->nullable();
            $table->string('content_id')->nullable();

            $table->integer('email_thread_id')->unsigned();
            $table->foreign('email_thread_id')->references('id')->on('email_threads')->onDelete('cascade');

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
        Schema::dropIfExists('email_thread_attachments');
    }
}
