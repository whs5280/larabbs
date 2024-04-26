<?php

namespace App\Package\Mission\Contracts;

/**
 * 任务记录接口
 */
interface MissionRecord
{
    public function getMissionGroupId();

    public function getMissionAcceptableId();

    public function getMissionAcceptableType();

    public function getMissionId();

    public function getMissionRewardType();

    public function getMissionRewardCount();

    /**
     * 是否已领取
     *
     * @return mixed
     */
    public function isReceived();

    /**
     * 是否已完成
     *
     * @return mixed
     */
    public function isFinish();

    /**
     * 是否已领取奖励
     *
     * @return mixed
     */
    public function isReward();
}
