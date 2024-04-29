<?php

namespace App\Package\Mission\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @package App\Mission\Facades
 * @method static void extend(string $name, \Closure $creator) 扩展任务处理器
 * @method static \App\Package\Mission\Contracts\MissionHandler handler(string $name) 获取任务处理器
 */
class MissionHandler extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'mission-handler';
    }
}
