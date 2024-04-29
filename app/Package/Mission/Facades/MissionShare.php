<?php

namespace App\Package\Mission\Facades;

use Illuminate\Support\Facades\Facade;
use App\Package\Mission\Contracts\MissionShare as Shareable;
/**
 * @package App\Mission\Facades
 * @method static share(Shareable $sharer)
 * @method static hasShareRecord(Shareable $sharer, $startTime = null, $endTime = null)
 */
class MissionShare extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'mission-share';
    }
}
