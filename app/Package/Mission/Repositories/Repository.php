<?php

namespace App\Package\Mission\Repositories;

/**
 * 任务仓库
 */
class Repository
{
    /**
     * @var MissionGroupRepository
     */
    protected $groupRepo;

    /**
     * @var MissionRecordRepository
     */
    protected $recordRepo;

    public function __construct()
    {
        $this->setGroupRepo();
        $this->setRecordRepo();
    }

    /**
     * @param $repo
     * @return void
     */
    public function setGroupRepo($repo = null)
    {
        $this->groupRepo = $repo ?? new MissionGroupRepository();
    }

    /**
     * @param $repo
     * @return void
     */
    public function setRecordRepo($repo = null)
    {
        $this->recordRepo = $repo ?? new MissionRecordRepository();
    }

    /**
     * @return MissionGroupRepository
     */
    public function groupRepo(): MissionGroupRepository
    {
        return $this->groupRepo;
    }

    /**
     * @return MissionRecordRepository
     */
    public function recordRepo(): MissionRecordRepository
    {
        return $this->recordRepo;
    }
}
