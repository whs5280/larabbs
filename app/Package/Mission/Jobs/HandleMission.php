<?php

namespace App\Package\Mission\Jobs;

use App\Package\Mission\Contracts\MissionHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleMission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var MissionHandler
     */
    protected $missionHandler;

    /**
     * @param MissionHandler $missionHandler
     */
    public function __construct(MissionHandler $missionHandler)
    {
        $this->missionHandler = $missionHandler;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $this->missionHandler->decrementDispatchCount();
        $this->missionHandler->handle();
    }
}
