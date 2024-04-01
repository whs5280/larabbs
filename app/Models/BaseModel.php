<?php

namespace App\Models;

class BaseModel extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];

    public function scopeRecent($query)
    {
        return $query->orderBy('id', 'desc');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'desc');
    }
}
