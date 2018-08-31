<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicePlansTable extends Migration
{
    public function up()
    {
        Schema::create('service_plans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('services_id');
            $table->string('started_at', 5);
            $table->string('stopped_at', 5);
            $table->integer('weight');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_plans');
    }
}
