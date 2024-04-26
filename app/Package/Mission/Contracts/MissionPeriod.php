<?php

namespace App\Package\Mission\Contracts;

interface MissionPeriod
{
    /**
     * 任务记录查询作用域
     *
     * @param $query
     * @param MissionAcceptable $acceptable
     * @param Mission $mission
     * @return mixed
     */
    public function scope($query, MissionAcceptable $acceptable, Mission $mission);

    /**
     * 缓存时间
     *
     * @param MissionAcceptable $acceptable
     * @param Mission $mission
     * @return mixed
     */
    public function cacheTtl(MissionAcceptable $acceptable, Mission $mission);
}
