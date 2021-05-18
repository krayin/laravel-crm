<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lead_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('path');

            $table->integer('lead_activity_id')->unsigned();
            $table->foreign('lead_activity_id')->references('id')->on('lead_activities')->onDelete('cascade');
            
            $table->integer('lead_id')->unsigned();
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');

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
        Schema::dropIfExists('lead_files');
    }
}
