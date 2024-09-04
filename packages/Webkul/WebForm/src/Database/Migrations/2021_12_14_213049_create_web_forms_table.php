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
        Schema::create('web_forms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('form_id')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('submit_button_label');
            $table->string('submit_success_action');
            $table->string('submit_success_content');
            $table->boolean('create_lead')->default(0);
            $table->string('background_color')->nullable();
            $table->string('form_background_color')->nullable();
            $table->string('form_title_color')->nullable();
            $table->string('form_submit_button_color')->nullable();
            $table->string('attribute_label_color')->nullable();
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
        Schema::dropIfExists('web_forms');
    }
};
