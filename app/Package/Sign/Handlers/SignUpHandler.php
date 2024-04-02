<?php


namespace App\Package\Sign\Handlers;

use App\Package\Sign\Models\UserSignLog;
use App\Package\Sign\Support\RedisBitMap;
use Closure;

class SignUpHandler implements HandlerInterface
{
    /**
     * @param $user
     * @param Closure $next
     * @return mixed|void
     * @throws \Throwable
     */
    public function handle($user, Closure $next)
    {
        $day = date('j');
        $cacheKey = sprintf('%s%s:%s', date('Ym'), 'user-sign-bit', $user->getKey());
        $redis = new RedisBitMap($cacheKey);

        throw_if($redis->get($day), new \Exception('今日已签到'));

        $redis->set($day);
        UserSignLog::generateLog($user->getKey());

        $next($user);
    }
}
