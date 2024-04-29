<?php

namespace App\Package\Mission\Events;

use App\Package\Mission\Models\MissionRecord;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MissionFinish
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var MissionRecord
     */
    protected $missionRecord;

    public function __construct(MissionRecord $missionRecord)
    {
        $this->missionRecord = $missionRecord;
    }

    public function getMissionRecord(): MissionRecord
    {
        return $this->missionRecord;
    }
}
