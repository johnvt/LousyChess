<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEnginesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('engines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code');
            $table->string('name');
            $table->json('goals_json');
            $table->integer('elo_rating');
            $table->timestamps();
        });

        Schema::table('games', function (Blueprint $table) {
            $table->foreign('white_engine_id')->references('id')->on('engines');
            $table->foreign('black_engine_id')->references('id')->on('engines');
        });

        $engines = [
            [
                'code' => 'B',
                'name' => 'Black tiles are lava',
                'goals_json' => json_encode(['CaptureKingGoal', 'MoveFromBlackToWhiteTileGoal', 'StayOnWhiteTileGoal']),
            ],
            [
                'code' => 'K',
                'name' => 'The king must die',
                'goals_json' => json_encode(['CaptureKingGoal', 'MoveCloserToKingGoal']),
            ],
            [
                'code' => 'M',
                'name' => 'Mirror, mirror',
                'goals_json' => json_encode(['CaptureKingGoal', 'MirrorLastMoveGoal', 'MoveSamePieceGoal']),
            ],
            [
                'code' => 'S',
                'name' => 'Let’s switch sides',
                'goals_json' => json_encode(['CaptureKingGoal', 'MoveCloserToEnemySetupGoal']),
            ],
            [
                'code' => 'W',
                'name' => 'White tiles are lava',
                'goals_json' => json_encode(['CaptureKingGoal', 'MoveFromWhiteToBlackTileGoal', 'StayOnBlackTileGoal']),
            ],
        ];

        foreach ($engines as $engine) {
            DB::table('engines')->insert(array_merge($engine, [
                'elo_rating' => 1000,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]));
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign(['white_engine_id']);
            $table->dropForeign(['black_engine_id']);
        });

        Schema::dropIfExists('engines');
    }
}
