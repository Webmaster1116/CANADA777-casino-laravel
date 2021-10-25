<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserGamesLog extends Migration
{
    /**
     * Run the migrations.
     * register balance of user that play games from api
     * @return void
     */
    public function up()
    {
        Schema::create('user_games_log', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();           
            $table->string('session_id')->nullable();                     
            $table->string('game_id')->nullable();  
            $table->integer('amount')->nullable();           
            $table->integer('no_money_left')->nullable();           
            $table->integer('there_was_money')->nullable();           
            $table->string('remote_id')->nullable();           
            $table->string('action')->nullable();           
            $table->string('provider')->nullable();           
            $table->string('original_session_id')->nullable();           
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
        Schema::dropIfExists('user_games_log');
    }
}
