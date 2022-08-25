<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('users_id')->unsigned();
            $table->string('name_profile', 200);
            $table->enum('gender_profile', ['P', 'L'])->nullable();
            $table->string('telephone_profile', 50)->nullable();
            $table->text('address_profile')->nullable();
            $table->string('picture_profile', 200)->nullable();
            $table->enum('status_profile', ['complete', 'not complete'])->default('not complete');
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile');
    }
}
