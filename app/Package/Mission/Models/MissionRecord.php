<?php

namespace App\Package\Mission\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MissionRecord extends BaseModel
{
    use HasFactory;

    const CREATED_AT = 'created_at';
}
