<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->increments('nt_id');
            $table->string('note');
            $table->integer('operator')->unsigned();
            $table->integer('vendor')->unsigned();
            $table->timestamps();
            $table->foreign('operator')->references('op_id')->on('operators');
            $table->foreign('vendor')->references('vn_id')->on('vendors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notes');
    }
}
