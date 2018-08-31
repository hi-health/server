<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicePlanDailyTable extends Migration
{
    public function up()
    {
        Schema::create('service_plan_daily', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('services_id');
            $table->integer('service_plans_id');
            $table->integer('service_plan_videos_id');
            $table->json('movement_test_data');
            $table->integer('score');
            $table->date('scored_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_plan_daily');
    }
}
