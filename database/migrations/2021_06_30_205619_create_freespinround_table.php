<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFreespinroundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freespinround', function (Blueprint $table) {
            $table->id();
            $table->string('title');           
            $table->string('players')->default('');                     
            $table->string('games')->default('');
            $table->string('apigames')->default('');
            $table->float('free_rounds')->default(0);
            $table->string('bet_type')->default('mid'); // min, mid, max
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();      
            $table->integer('notify')->default(0); // 1: allow notify, 0: not allow notify 
            $table->integer('active')->default(1); //1 : active, 0: unactive
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
        Schema::dropIfExists('freespinround');
    }
}
