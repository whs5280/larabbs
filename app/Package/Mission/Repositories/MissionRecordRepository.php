<?php

namespace App\Package\Mission\Repositories;

use App\Package\Mission\Contracts\Mission;
use App\Package\Mission\Contracts\MissionAcceptable;
use App\Package\Mission\Facades\MissionPeriod;
use App\Package\Mission\Models\MissionRecord;

class MissionRecordRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new MissionRecord();
    }

    /**
     * 静态类`create` 返回的是 model
     * 链式类`create` 返回的是 bool
     * @param array $attribute
     * @return mixed
     */
    public function create(array $attribute)
    {
        return MissionRecord::create($attribute);
    }

    /**
     * 是否已领取任务
     *
     * @param MissionAcceptable $acceptable
     * @param Mission $mission
     * @return bool
     */
    public function isReceiveMission(MissionAcceptable $acceptable, Mission $mission): bool
    {
        return $this->basePeriodQuery($acceptable, $mission)->select(['mission_id'])
            ->missionGroup($mission->getMissionGroupId())
            ->receive()
            ->exists();
    }

    /**
     * 是否完成任务
     *
     * @param MissionAcceptable $acceptable
     * @param Mission $mission
     * @return bool
     */
    public function isFinishMission(MissionAcceptable $acceptable, Mission $mission): bool
    {
        return $this->basePeriodQuery($acceptable, $mission)->select(['mission_id'])
            ->missionGroup($mission->getMissionGroupId())
            ->finish()
            ->exists();
    }

    /**
     * 是否已领取奖励
     *
     * @param MissionAcceptable $acceptable
     * @param Mission $mission
     * @return bool
     */
    public function isRewardMission(MissionAcceptable $acceptable, Mission $mission): bool
    {
        return $this->basePeriodQuery($acceptable, $mission)->select(['mission_id'])
            ->missionGroup($mission->getMissionGroupId())
            ->reward()
            ->exists();
    }

    public function basePeriodQuery(MissionAcceptable $acceptable, Mission $mission)
    {
        return $this->model::acceptor($acceptable)
            ->missionFrom($mission)
            ->where(function ($query) use ($acceptable, $mission) {
                // 任务周期条件
                return MissionPeriod::period($mission->getMissionPeriodType())->scope($query, $acceptable, $mission);
            });
    }
}
