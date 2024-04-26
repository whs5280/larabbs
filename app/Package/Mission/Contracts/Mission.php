<?php

namespace App\Package\Mission\Contracts;

/**
 * 任务接口
 */
interface Mission
{
    /**
     * 任务周期
     */
    const PERIOD_ONCE = 'once';
    const PERIOD_DAILY = 'daily';
    const PERIOD_WEEKLY = 'weekly';
    const PERIOD_MONTHLY = 'monthly';
    const PERIOD_YEARLY = 'yearly';

    public function getKey();

    /**
     * @return MissionGroup
     */
    public function getMissionGroup(): MissionGroup;

    public function getMissionGroupId();

    public function getMissionType();

    public function getMissionName();

    public function getMissionDescription();

    public function getMissionJumpLink();

    public function getMissionHandler();

    /**
     * 获取任务处理器
     *
     * @param MissionAcceptable $missionAcceptable
     * @return mixed
     */
    public function makeHandler(MissionAcceptable $missionAcceptable);

    public function getMissionPeriodType();

    public function getMissionRewardType();

    public function getMissionRewardCount();

    public function isActive();
}
