<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatPointproducesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pointproduce', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('users_id');
            $table->integer('point');
            $table->integer('pointconsume_id')->nullable();
            $table->integer('service_plan_daily_id')->nullable();
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
        Schema::dropIfExists('pointproduce');
    }
}
