<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTokenTable extends Migration
{
    public function up()
    {
        Schema::create('user_device_token', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('users_id');
            $table->string('device_arn');
            $table->string('device_token');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_device_token');
    }
}
