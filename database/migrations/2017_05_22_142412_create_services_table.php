<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_number')->unique();
            $table->integer('members_id')->nullable();
            $table->integer('doctors_id');
            $table->string('treatment_type', 1)->default(1);
            $table->integer('charge_amount');
            $table->string('payment_method', 1)->default(0);
            $table->string('payment_status', 1)->default(0);
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services');
    }
}
