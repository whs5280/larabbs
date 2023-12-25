<?php

namespace App\Lottery\Http\Middleware;

use App\Lottery\Traits\ApiHelper;

class Lock
{
    use ApiHelper;

    /**
     * Handle an incoming request.
     *
     * @param $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, \Closure $next, $lockName = 'default', $lockTime = 3)
    {
        $user = $this->user();

        $cacheKey = sprintf("{$lockName}:u-%s", $user->id);
        $lock = \Cache::store('redis')->lock($cacheKey, $lockTime);
        if (!$lock->acquire()) {
            return $this->error('操作太过频繁！');
        }

        return $next($request);
    }
}
