<?php

namespace App\Package\Sign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserIntegralLog extends Model
{
    use HasFactory;

    CONST SIGN_IN = 10;
    CONST CONTINUOUS_SIGN_IN = 20;
    CONST SUPPLEMENT_SIGN_IN = 30;

    public static function generateLog($userId)
    {
        self::query()->create([
            'user_id'       => $userId,
            'integral_type' => self::SIGN_IN,
            'integral' => true,
            'desc'     => '今日签到',
        ]);
    }
}
