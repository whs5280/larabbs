<?php

namespace App\Package\Mission\Repositories;

use App\Package\Mission\Models\MissionGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class MissionGroupRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new MissionGroup();
    }

    /**
     * DB 查询
     *
     * @param int $groupId
     * @param string $tag
     * @return MissionGroup|Builder|Model|object|null
     */
    public function firstByGroupIdAndTag(int $groupId, string $tag)
    {
        return $this->model->groupId($groupId)->tag($tag)->first();
    }

    /**
     * 缓存 查询
     *
     * @param int $groupId
     * @param string $tag
     * @return MissionGroup|Builder|Model|mixed|object|null
     */
    public function firstByGroupIdAndTagViaCache(int $groupId, string $tag)
    {
        $cacheKey = "mission_group:{$groupId}_{$tag}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        $group = $this->firstByGroupIdAndTag($groupId, $tag);
        if (!is_null($group)) {
            Cache::put($cacheKey, $group, now()->addHours(2));
        }
        return $group;
    }
}
