<?php


namespace App\Sign\Handlers;

use Closure;

class SignLogHandler implements HandlerInterface
{
    public function handle($user, Closure $next)
    {
        logger()->info(sprintf('user: %s sign log success', $user->getKey()));
        $next($user);
    }
}
