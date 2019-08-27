<?php

namespace App\Models\Engines;

use App\Models\Engine;
use App\Models\Goal;
use App\Models\Goals\CaptureKingGoal;
use App\Models\Goals\MoveFromWhiteToBlackTileGoal;
use App\Models\Goals\StayOnBlackTileGoal;

class WhiteTilesAreLava extends Engine
{
    public $name = 'White tiles are lava';

    /**
     * @return Goal[]
     */
    public function getGoals()
    {
        return [
            new CaptureKingGoal,
            new MoveFromWhiteToBlackTileGoal,
            new StayOnBlackTileGoal
        ];
    }
}
