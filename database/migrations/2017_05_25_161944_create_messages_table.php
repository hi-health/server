<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('members_id')->nullable();
            $table->integer('doctors_id')->nullable();
            $table->integer('member_requests_id')->nullable();
            $table->boolean('visible')->default(true);
            $table->text('message');
            $table->string('source');
            $table->timestamp('member_readed_at')->nullable();
            $table->timestamp('doctor_readed_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
