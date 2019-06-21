<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::defaultStringLength(191);
        
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('account')->unique();
            $table->string('password');
            $table->string('email');
            $table->string('login_type');
            $table->string('facebook_id')->nullable();
            $table->string('facebook_token')->nullable();
            $table->string('avatar')->nullable();
            $table->integer('male');
            $table->date('birthday');
            $table->integer('city_id');
            $table->integer('district_id');
            $table->integer('mrs')->default(0);
            $table->integer('treatment_type')->nullable();
            $table->integer('treatment_kind')->nullable();
            $table->date('onset_date')->nullable();
            $table->integer('onset_part')->nullable();
            $table->boolean('online')->default(true);
            $table->boolean('status')->default(true);
            $table->rememberToken();
            $table->timestamp('online_at');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('role');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
