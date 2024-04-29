<?php

namespace App\Package\Mission\Models;

use App\Models\BaseModel;
use App\Package\Mission\Contracts\MissionAcceptable;
use App\Package\Mission\Contracts\MissionGroup;
use App\Package\Mission\Facades\MissionHandler;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|Mission active()
 * @property \App\Package\Mission\Models\MissionGroup $group
 */
class Mission extends BaseModel implements \App\Package\Mission\Contracts\Mission
{
    use HasFactory;

    const ACTIVE = 1;

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function group(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MissionGroup::class, 'group_id', 'group_id');
    }

    /**
     * @inheritDoc
     */
    public function makeHandler(MissionAcceptable $missionAcceptable)
    {
        return MissionHandler::handler($this->getMissionHandler())
            ->setMission($this)
            ->setMissionAcceptable($missionAcceptable);
    }


    /********** Mission 接口实现 **********/

    public function getMissionGroup(): MissionGroup
    {
        return $this->group;
    }

    public function getMissionGroupId()
    {
        return $this->getAttribute('group_id');
    }

    public function getMissionType()
    {
        return $this->getAttribute('type');
    }

    public function getMissionName()
    {
        return $this->getAttribute('name');
    }

    public function getMissionDescription()
    {
        return $this->getAttribute('description');
    }

    public function getMissionJumpLink()
    {
        return $this->getAttribute('link');
    }

    public function getMissionHandler()
    {
        return $this->getAttribute('handler');
    }

    public function getMissionPeriodType()
    {
        return $this->getAttribute('period_type');
    }

    public function getMissionRewardType()
    {
        return $this->getAttribute('reward_type');
    }

    public function getMissionRewardCount()
    {
        return $this->getAttribute('reward_count');
    }

    public function isActive(): bool
    {
        return $this->getAttribute('is_active') == self::ACTIVE;
    }
}
