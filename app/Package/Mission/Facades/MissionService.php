<?php

namespace App\Package\Mission\Facades;

use App\Package\Mission\Contracts\MissionAcceptable;
use App\Package\Mission\Models\Mission;
use Illuminate\Support\Facades\Facade;

/**
 * @package App\Mission\Facades
 * @method static index(int $groupId, string $tag, MissionAcceptable $acceptable)   任务列表
 * @method static receive(Mission $mission, MissionAcceptable $acceptable)   接受任务
 * @method static reward(Mission $mission, MissionAcceptable $acceptable)   领取奖励
 */
class MissionService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'mission-service';
    }
}
