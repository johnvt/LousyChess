<?php

namespace App\Models\Engines;

use App\Models\Engine;
use App\Models\Goal;
use App\Models\Goals\CaptureKingGoal;
use App\Models\Goals\MoveFromBlackToWhiteTileGoal;
use App\Models\Goals\StayOnWhiteTileGoal;

class BlackTilesAreLava extends Engine
{
    public $name = 'Black tiles are lava';

    /**
     * @return Goal[]
     */
    public function getGoals()
    {
        return [
            new CaptureKingGoal,
            new MoveFromBlackToWhiteTileGoal,
            new StayOnWhiteTileGoal
        ];
    }
}
