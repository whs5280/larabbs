<?php

namespace App\Package\Mission\Periods;

use App\Package\Mission\Contracts\Mission;
use App\Package\Mission\Contracts\MissionAcceptable;
use App\Package\Mission\Contracts\MissionPeriod;
use App\Package\Mission\Models\MissionRecord;

class Yearly implements MissionPeriod
{
    public function scope($query, MissionAcceptable $acceptable, Mission $mission)
    {
        return $query->whereDate(MissionRecord::CREATED_AT, '>=', now()->startOfYear()->toDateString());
    }

    public function cacheTtl(MissionAcceptable $acceptable, Mission $mission)
    {
        return now()->endOfYear();
    }
}
