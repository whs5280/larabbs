<?php

namespace App\Package\Mission\Contracts;

/**
 * 任务组接口
 */
interface MissionGroup
{
    public function getKey();

    public function getMissionGroupTag();

    public function getMissionGroupName();

    public function getMissionGroupExpiredAt();
}
