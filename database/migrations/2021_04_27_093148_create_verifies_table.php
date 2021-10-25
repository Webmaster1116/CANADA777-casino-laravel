<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verifies', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unique()->nullable();           ////user id
            $table->string('id_img')->nullable();                       ////verify image for id
            $table->string('address_img')->nullable();                  ////verify image for address
            $table->boolean('verified')->default(false);                ////is verified
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
        Schema::dropIfExists('verifies');
    }
}
