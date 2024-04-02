<?php


namespace App\Package\Sign\Handlers;

use App\Package\Sign\Models\UserIntegralLog;
use Closure;

class SignCreditHandler implements HandlerInterface
{
    public function handle($user, Closure $next)
    {
        UserIntegralLog::generateLog($user->getKey());
        $next($user);
    }
}
