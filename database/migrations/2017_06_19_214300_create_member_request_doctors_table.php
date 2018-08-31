<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberRequestDoctorsTable extends Migration
{
    public function up()
    {
        Schema::create('member_request_doctors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_requests_id');
            $table->integer('doctors_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('member_request_doctors');
    }
}
