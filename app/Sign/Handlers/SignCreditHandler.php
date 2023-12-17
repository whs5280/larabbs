<?php


namespace App\Sign\Handlers;

use App\Sign\Models\UserIntegralLog;
use Closure;

class SignCreditHandler implements HandlerInterface
{
    public function handle($user, Closure $next)
    {
        UserIntegralLog::generateLog($user->getKey());
        $next($user);
    }
}
