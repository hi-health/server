<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * 
     */
    public function up()
    {
        Schema::table('subscription', function ($table) {
            $table->dropColumn('due date');
            $table->date('due_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * 
     */
    public function down()
    {
        Schema::table('subscription', function ($table) {
            $table->dropColumn('due_date');
            $table->date('due date');
        });
    }
}
