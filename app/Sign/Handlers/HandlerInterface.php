<?php


namespace App\Sign\Handlers;

use Closure;

interface HandlerInterface
{
    /**
     * @param $user
     * @param Closure $next
     * @return mixed
     */
    public function handle($user, Closure $next);
}
