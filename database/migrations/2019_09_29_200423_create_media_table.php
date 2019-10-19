<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('media_type_id');
            $table->string('name');
            $table->text('short_description');
            $table->text('description');
            $table->string('trailer_url');
            $table->decimal('imdb_rating', 4, 1)->nullable();
            $table->timestamps();

            $table->foreign('media_type_id')->references('id')->on('media_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
}
