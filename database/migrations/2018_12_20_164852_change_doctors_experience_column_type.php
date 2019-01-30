<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDoctorsExperienceColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doctors', function ($table) {
            $table->json('experience')->customSchemaOptions(['collation' => ''])->change(); //Mysql: LONGTEXT
	    $table->json('specialty')->customSchemaOptions(['collation' => ''])->change(); //Mysql: LONGTEXT
	    $table->json('education')->customSchemaOptions(['collation' => ''])->change(); //Mysql: LONGTEXT
	    $table->json('license')->customSchemaOptions(['collation' => ''])->change(); //Mysql: LONGTEXT
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doctors', function ($table) {
            $table->string('experience')->change();
            $table->string('specialty')->change();
            $table->string('education')->change();
            $table->string('license')->change();
        });
    }
}

