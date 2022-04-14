<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSenders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('senders', function (Blueprint $table) {
            $table->increments('sn_id');
            $table->string('senderid');
            $table->string('content');
            $table->string('website');
            $table->string('note');
            $table->integer('operator')->unsigned();
            $table->integer('vendor')->unsigned();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamp('created_at')->nullable();

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
        Schema::dropIfExists('senders');
    }
}
