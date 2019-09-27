<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('white_engine_id');
            $table->unsignedBigInteger('black_engine_id');
            $table->string('serial_number');
            $table->string('bomb_number');
            $table->unsignedInteger('white_seed');
            $table->text('white_random');
            $table->unsignedInteger('black_seed');
            $table->text('black_random');
            $table->integer('num_moves');
            $table->tinyInteger('winner')->nullable();
            $table->json('moves_json');
            $table->json('boards_json');
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
        Schema::dropIfExists('games');
    }
}
