<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemberRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('member_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('members_id');
            $table->integer('treatment_type')->nullable();
            $table->integer('treatment_kind')->nullable();
            $table->date('onset_date')->nullable();
            $table->integer('onset_part')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('member_requests');
    }
}
