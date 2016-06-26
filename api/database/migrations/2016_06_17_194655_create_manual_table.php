<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManualTable extends Migration
{
    public function up()
    {
        //
        Schema::create('manual', function(Blueprint $table){
            $table->increments('id');
            $table->string('mode')->default('on');
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
        Schema::drop('manual');
    }
}
