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
        Schema::create('attributes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->string('type');
            $table->string('lookup_type')->nullable();
            $table->string('entity_type');
            $table->integer('sort_order')->nullable();
            $table->string('validation')->nullable();
            $table->boolean('is_required')->default(0);
            $table->boolean('is_unique')->default(0);
            $table->boolean('quick_add')->default(0);
            $table->boolean('is_user_defined')->default(1);
            $table->unique(['code', 'entity_type']);
            $table->timestamps();
            $table->unsignedInteger('tenant_id')->nullable();
            $table->foreign('tenant_id')->references('id')->on('tenants')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attributes');
    }
};
