<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExitItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exit_item', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('stock_store_id')->unsigned();
            $table->bigInteger('users_id')->unsigned();
            $table->dateTime('out_date_exit_item');
            $table->bigInteger('stock_exit_item');
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('stock_store_id')->references('id')->on('stock_store')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exit_item');
    }
}
