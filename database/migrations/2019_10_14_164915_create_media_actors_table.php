<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaActorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_actors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('media_id');
            $table->unsignedBigInteger('actor_id');

            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
            $table->foreign('actor_id')->references('id')->on('actors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_files');
    }
}
