<?php

namespace App\Models\Engines;

use App\Models\Engine;
use App\Models\Goal;
use App\Models\Goals\CaptureKingGoal;
use App\Models\Goals\MoveCloserToKingGoal;

class TheKingMustDieEngine extends Engine
{
    public $name = 'The king must die';

    /**
     * @return Goal[]
     */
    public function getGoals()
    {
        return [
            new CaptureKingGoal,
            new MoveCloserToKingGoal
        ];
    }
}
