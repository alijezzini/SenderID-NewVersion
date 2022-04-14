<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->increments('fl_id');
            $table->string('file_name');
            $table->string('file_url');
            $table->integer('vendor')->unsigned();
            $table->integer('operator')->unsigned();
            $table->timestamps();
            $table->foreign('vendor')->references('vn_id')->on('vendors')->ondelete('Cascade');
            $table->foreign('operator')->references('op_id')->on('operators')->ondelete('Cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
