<?php

namespace App\Sign\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSignLog extends Model
{
    use HasFactory;

    /**
     * @param $userId
     * @return void
     */
    public static function generateLog($userId)
    {
        $where = [
            'user_id' => $userId,
            'month'   => now()->format('Ym'),
        ];
        $log = self::query()->where($where)->first();
        if (!$log) {
            $log = self::query()->create([
                'user_id' => $userId,
                'month' => now()->format('Ym'),
                'bit_log' => substr_replace(str_repeat(0, now()->daysInMonth), 1, now()->day - 1, 1)
            ]);
        }
        $log->bit_log = substr_replace($log->bit_log, 1, now()->day - 1, 1);
        $log->save();
    }
}
