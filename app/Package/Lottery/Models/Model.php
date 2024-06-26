<?php

namespace App\Package\Lottery\Models;

class Model extends \Illuminate\Database\Eloquent\Model
{
    protected $guarded = ['id'];

    public function scopeOrder()
    {
        return $this->orderBy('id', 'desc');
    }
}
