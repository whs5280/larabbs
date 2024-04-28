<?php

namespace App\Package\Mission\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @package App\Mission\Facades
 * @method static void extend(string $name, \Closure $creator) 扩展任务处理器
 * @method static \App\Package\Mission\Contracts\MissionPeriod period(string $name) 获取任务处理器
 */
class MissionPeriod extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'mission-period';
    }
}
