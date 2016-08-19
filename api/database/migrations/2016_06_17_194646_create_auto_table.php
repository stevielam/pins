<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAutoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('auto', function(Blueprint $table){
            $table->increments('id');
            $table->boolean('master_enable')->default(0);
            $table->boolean('monday_enable')->default(0);
            $table->boolean('tuesday_enable')->default(0);
            $table->boolean('wednesday_enable')->default(0);
            $table->boolean('thursday_enable')->default(0);
            $table->boolean('friday_enable')->default(0);
            $table->boolean('saturday_enable')->default(0);
            $table->boolean('sunday_enable')->default(0);
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('relay_id');
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
        Schema::drop('auto');
    }
}
