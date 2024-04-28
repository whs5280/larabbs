<?php

namespace App\Package\Mission\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static \Illuminate\Database\Eloquent\Builder|MissionGroup groupId(int $groupId)
 * @method static \Illuminate\Database\Eloquent\Builder|MissionGroup tag(string $tag)
 */
class MissionGroup extends BaseModel
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
}
