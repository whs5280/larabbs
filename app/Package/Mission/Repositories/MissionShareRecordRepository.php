<?php

namespace App\Package\Mission\Repositories;

use App\Package\Mission\Contracts\MissionShare;
use App\Package\Mission\Models\MissionShareRecord;

class MissionShareRecordRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new MissionShareRecord();
    }

    public function create(MissionShare $sharer)
    {
        return MissionShareRecord::create([
            'mission_id' => $sharer->getMissionId(),
            'user_id'    => $sharer->getUserId(),
            'share_user_id'   => $sharer->getShareUserId(),
            'share_user_type' => $sharer->getShareUserType(),
        ]);
    }

    /**
     * 是否分享过记录
     *
     * @param MissionShare $sharer
     * @param $startTime
     * @param $endTime
     * @return bool
     */
    public function hasShareRecord(MissionShare $sharer, $startTime = null, $endTime = null): bool
    {
        $query = $this->model->sharer($sharer->getShareUserId(), $sharer->getShareUserType())
            ->missionFrom($sharer->getMissionId());

        if (!is_null($startTime)) {
            $query->where(MissionShareRecord::CREATED_AT, '>=', $startTime);
        }
        if (!is_null($endTime)) {
            $query->where(MissionShareRecord::CREATED_AT, '<=', $endTime);
        }

        return $query->exists();
    }
}
