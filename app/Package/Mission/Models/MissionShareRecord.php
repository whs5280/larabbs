<?php

namespace App\Package\Mission\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|MissionShareRecord sharer($userId, $userType)
 * @method static \Illuminate\Database\Eloquent\Builder|MissionShareRecord missionFrom($missionId)
 */
class MissionShareRecord extends BaseModel implements \App\Package\Mission\Contracts\MissionShare
{
    use HasFactory;

    const CREATED_AT = 'created_at';

    public function scopeSharer($query, $userId, $userType)
    {
        return $query->where('share_user_id', $userId)->where('share_user_type', $userType);
    }

    public function scopeMissionFrom($query, $missionId)
    {
        return $query->where('mission_id', $missionId);
    }


    /********** MissionShare 接口实现 **********/

    public function getMissionId()
    {
        return $this->getAttribute('mission_id');
    }

    public function getUserId()
    {
        return $this->getAttribute('user_id');
    }

    public function getShareUserId()
    {
        return $this->getAttribute('share_user_id');
    }

    public function getShareUserType()
    {
        return $this->getAttribute('share_user_type');
    }
}
