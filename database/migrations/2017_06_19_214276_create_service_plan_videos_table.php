<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServicePlanVideosTable extends Migration
{
    public function up()
    {
        Schema::create('service_plan_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_plans_id');
            $table->string('video');
            $table->string('thumbnail');
            $table->string('description')->nullable();
            $table->json('movement_template_data')->nullable();
            $table->integer('weight');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_plan_videos');
    }
}
