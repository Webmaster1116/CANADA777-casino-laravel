<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('message')->default('');
            $table->string('image')->default('');
            $table->integer('campaign')->default(0); //0 : not campaign, 1: campaign
            $table->date('notify_date');
            $table->time('notify_time');
            $table->string('timezone')->default('');
            $table->string('frequency'); //0 : One time, 1: Once a Day
            $table->integer('active')->default(0);   //0: unactive,  1: active
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
        Schema::dropIfExists('notifications');
    }
}
