<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRegistrationApi extends Migration
{
    /**
     * Run the migrations.
     * register users that is registered on api service
     * @return void
     */
    public function up()
    {
        Schema::create('user_registration_api', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unique()->nullable();           
            $table->string('password')->nullable();                     
            $table->string('usersname')->nullable();                  
            $table->string('currency')->nullable();                  
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
        Schema::dropIfExists('user_registration_api');
    }
}
