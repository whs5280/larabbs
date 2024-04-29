<?php

namespace App\Package\Mission\Share;

use App\Package\Mission\Contracts\MissionShare;
use App\Package\Mission\Repositories\MissionShareRecordRepository;

class ShareManager
{
    /**
     * @var MissionShareRecordRepository
     */
    protected $shareRepo;

    /**
     * @param MissionShareRecordRepository $shareRepo
     */
    public function __construct(MissionShareRecordRepository $shareRepo)
    {
        $this->shareRepo = $shareRepo;
    }

    public function share(MissionShare $sharer)
    {
        return $this->shareRepo->create($sharer);
    }

    public function hasShareRecord(MissionShare $sharer, $startTime = null, $endTime = null): bool
    {
        return $this->shareRepo->hasShareRecord($sharer, $startTime, $endTime);
    }
}
