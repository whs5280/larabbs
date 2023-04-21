<?php

namespace App\Sign\Http\Controllers;

use App\Sign\Support\RedisBitMap;
use Illuminate\Support\Facades\Auth;

class SignController extends ApiController
{
    protected $cacheKey;

    public function __construct()
    {
        $userId = Auth::id();
        $this->cacheKey = sprintf('%s:%s:%s:bit', date('Ym'), 'user-sign', $userId);
    }


    public function signIn()
    {
        $day = date('j');

        $redis = new RedisBitMap($this->cacheKey);
        if ($redis->get($day)) {
            return $this->renderSuccess(trans('location::msg.E401002'), 401002, 0);
        }

        $redis->set($day);

        return $this->renderSuccess(trans('location::msg.E401001'), 401001, 1);
    }
}
