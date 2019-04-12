<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemoPlanVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demo_plan_videos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('demo_plans_id');
            $table->string('video');
            $table->string('thumbnail');
            $table->string('description')->nullable();
            $table->json('movement_template_data')->nullable();
	    $table->integer('session');
            $table->integer('repeat_time');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demo_plan_videos');
    }
}
