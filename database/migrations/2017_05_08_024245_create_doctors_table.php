<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsTable extends Migration
{
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('users_id');
            $table->string('number', 16)->nullable();
            $table->integer('treatment_type')->default(1);
            $table->string('title', 32)->nullable();
            $table->integer('experience_year')->default(0);
            $table->string('experience')->nullable();
            $table->string('specialty')->nullable();
            $table->string('education')->nullable();
            $table->string('license')->nullable();
            $table->integer('education_bonus')->default(0);
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->date('due_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('doctors');
    }
}
