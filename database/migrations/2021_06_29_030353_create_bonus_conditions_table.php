<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('name');           
            $table->integer('type')->default(0);                     
            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();
            $table->boolean('is_mon')->default(true);
            $table->boolean('is_tue')->default(true);
            $table->boolean('is_wed')->default(true);
            $table->boolean('is_thr')->default(true);
            $table->boolean('is_fri')->default(true);
            $table->boolean('is_sat')->default(true);
            $table->boolean('is_sun')->default(true);
            $table->integer('deposit_min')->nullable();
            $table->integer('deposit_max')->nullable();
            $table->integer('match_win')->nullable();
            $table->string('code')->nullable();  
            $table->integer('wagering')->nullable();     
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('bonus_conditions');
    }
}
