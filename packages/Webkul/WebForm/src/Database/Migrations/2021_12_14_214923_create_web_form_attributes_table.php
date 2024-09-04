<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('web_form_attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('placeholder')->nullable();
            $table->boolean('is_required')->default(0);
            $table->boolean('is_hidden')->default(0);
            $table->integer('sort_order')->nullable();

            $table->integer('attribute_id')->unsigned();
            $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');

            $table->integer('web_form_id')->unsigned();
            $table->foreign('web_form_id')->references('id')->on('web_forms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('web_form_attributes');
    }
};
