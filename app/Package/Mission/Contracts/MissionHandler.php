<?php

namespace App\Package\Mission\Contracts;

/**
 * 任务处理器接口
 */
interface MissionHandler
{
    /**
     * @param Mission $mission
     * @return mixed
     */
    public function setMission(Mission $mission);

    /**
     * @param MissionAcceptable $missionAcceptable
     * @return mixed
     */
    public function setMissionAcceptable(MissionAcceptable $missionAcceptable);

    public function getMission();

    public function getMissionAcceptable();

    public function isReceived();

    public function isFinish();

    public function isReward();

    /**
     * 处理任务
     *
     * @return mixed
     */
    public function handle();

    /**
     * 接受任务
     *
     * @return mixed
     */
    public function receive();

    /**
     * 完成任务
     *
     * @return mixed
     */
    public function finish();

    /**
     * 领取奖励
     *
     * @return mixed
     */
    public function reward();
}
