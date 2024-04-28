<?php

namespace App\Package\Mission\Models;

use App\Models\BaseModel;
use App\Package\Mission\Contracts\MissionAcceptable;
use App\Package\Mission\Contracts\Mission as MissionContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|MissionRecord receive()
 * @method static \Illuminate\Database\Eloquent\Builder|MissionRecord finish()
 * @method static \Illuminate\Database\Eloquent\Builder|MissionRecord reward()
 * @method static \Illuminate\Database\Eloquent\Builder|MissionRecord missionGroup(int $groupId)
 * @method static \Illuminate\Database\Eloquent\Builder|MissionRecord acceptor(MissionAcceptable $acceptable)
 * @method static \Illuminate\Database\Eloquent\Builder|MissionRecord missionFrom(MissionContract $mission)
 */
class MissionRecord extends BaseModel
{
    use HasFactory;

    const CREATED_AT = 'created_at';

    const STATUS_RECEIVE = 1;
    const STATUS_FINISH = 2;
    const STATUS_REWARD = 3;

    public function scopeReceive($query)
    {
        return $query->where('status', self::STATUS_RECEIVE);
    }

    public function scopeFinish($query)
    {
        return $query->where('status', self::STATUS_FINISH);
    }

    public function scopeReward($query)
    {
        return $query->where('status', self::STATUS_REWARD);
    }

    public function scopeMissionGroup($query, int $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * 限定接受者
     *
     * @param $query
     * @param MissionAcceptable $acceptable
     * @return mixed
     */
    public function scopeAcceptor($query, MissionAcceptable $acceptable)
    {
        return $query->where('acceptable_id', $acceptable->getMissionAcceptableKey())
            ->where('acceptable_type', $acceptable->getMissionAcceptableType());
    }

    /**
     * 限定任务
     *
     * @param $query
     * @param MissionContract $mission
     * @return mixed
     */
    public function scopeMissionFrom($query, MissionContract $mission)
    {
        return $query->where('mission_id', $mission->getKey());
    }

    public function mission(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Mission::class, 'mission_id');
    }

    /**
     * @return MorphTo
     */
    public function acceptable(): MorphTo
    {
        return $this->morphTo('acceptable', 'acceptable_type', 'acceptable_id');
    }
}
