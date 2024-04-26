<?php

namespace App\Package\Mission\Periods;

use App\Package\Mission\Contracts\Mission;
use App\Package\Mission\Contracts\MissionAcceptable;
use App\Package\Mission\Contracts\MissionPeriod;

class Once implements MissionPeriod
{
    public function scope($query, MissionAcceptable $acceptable, Mission $mission)
    {
        return $query;
    }

    public function cacheTtl(MissionAcceptable $acceptable, Mission $mission)
    {
        return $mission->getMissionGroup()->getMissionGroupExpiredAt();
    }
}
