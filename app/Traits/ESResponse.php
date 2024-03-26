<?php

namespace App\Traits;

trait ESResponse
{
    /**
     * 格式化ES查询结果
     * @return array
     */
    public function getResultHits(): array
    {
        return collect($this->result['hits']['hits'])->pluck('_source')->all();
    }

    /**
     * group by 结果集
     * @return array
     */
    public function getResultAggs(): array
    {
        return $this->result['aggregations']['group_by_count']['buckets'];
    }
}
