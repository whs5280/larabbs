<?php

namespace App\Package\Mission\Facades;

use App\Package\Mission\Repositories\MissionGroupRepository;
use App\Package\Mission\Repositories\MissionRecordRepository;
use Illuminate\Support\Facades\Facade;

/**
 * @package App\Mission\Facades
 * @method static MissionGroupRepository|mixed groupRepo()   任务组仓库
 * @method static MissionRecordRepository|mixed recordRepo()  任务记录仓库
 */
class MissionRepo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'mission-repo';
    }
}
