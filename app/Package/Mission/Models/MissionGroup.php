<?php

namespace App\Package\Mission\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|MissionGroup groupId(int $groupId)
 * @method static \Illuminate\Database\Eloquent\Builder|MissionGroup tag(string $tag)
 */
class MissionGroup extends BaseModel implements \App\Package\Mission\Contracts\MissionGroup
{
    use HasFactory;

    public function scopeGroupId($query, int $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    public function scopeTag($query, string $tag)
    {
        return $query->where('tag', $tag);
    }

    public function findActiveMissions()
    {
        return $this->missions()->active()->orderBy('sort')->get();
    }

    /**
     * @return HasMany
     */
    public function missions(): HasMany
    {
        return $this->hasMany(Mission::class, 'group_id', 'group_id');
    }


    /********** MissionGroup 接口实现 **********/

    public function getMissionGroupTag()
    {
        return $this->getAttribute('tag');
    }

    public function getMissionGroupName()
    {
        return $this->getAttribute('name');
    }

    public function getMissionGroupExpiredAt()
    {
        return $this->getAttribute('expired_at');
    }
}
