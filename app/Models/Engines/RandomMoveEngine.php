<?php

namespace App\Models\Engines;

use App\Models\Engine;
use App\Models\Goal;
use App\Models\Goals\CaptureKingGoal;

class RandomMoveEngine extends Engine
{
    public $name = 'Random move';

    /**
     * @return Goal[]
     */
    public function getGoals()
    {
        return [new CaptureKingGoal];
    }
}
