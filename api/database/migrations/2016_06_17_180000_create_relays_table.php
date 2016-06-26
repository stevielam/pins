<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelaysTable extends Migration
{
    public function up()
    {
        //
        Schema::create('relays', function(Blueprint $table){
            $table->increments('id');
            $table->integer('number')->default(-1);
            $table->string('name');
            $table->boolean('is_output')->default(1);
            $table->string('mode')->default('auto');
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
        Schema::drop('relays');
    }
}
