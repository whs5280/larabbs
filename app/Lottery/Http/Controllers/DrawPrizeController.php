<?php

namespace App\Lottery\Http\Controllers;

use App\Lottery\Models\Prize;
use App\Lottery\Support\CacheKey;
use App\Lottery\Support\RedisList;
use App\Lottery\Traits\ApiHelper;
use Illuminate\Routing\Controller;

class DrawPrizeController extends Controller
{
    use ApiHelper;

    public function lottery(Prize $prize): \Illuminate\Http\JsonResponse
    {
        $cacheKey = sprintf("%s%s", CacheKey::PRIZE_STOCK, $prize->getKey());

        $res = app(RedisList::class, ['key' => $cacheKey])->pop();
        if (!$res) {
            return $this->error(trans('lottery::msg.E401001'), 400, 401001);
        }

        $prize->decrement('stock');

        return response()->json([
            'code' => 401000,
            'message' => trans('lottery::msg.E401000'),
            'data' => [
                'prize' => $prize->name,
            ],
        ]);
    }
}
