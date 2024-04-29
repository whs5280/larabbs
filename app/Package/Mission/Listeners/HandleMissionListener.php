<?php

namespace App\Package\Mission\Listeners;

use App\Package\Mission\Events\MissionFinish;

class HandleMissionListener
{
    /**
     * @param MissionFinish $event
     * @return void
     */
    public function handle(MissionFinish $event): void
    {
        logger()->info('------handle mission finish------', ['data' => $event->getMissionRecord()->toArray()]);
    }
}
