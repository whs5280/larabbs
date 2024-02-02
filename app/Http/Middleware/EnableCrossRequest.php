<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnableCrossRequest
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        $origin = $_SERVER['HTTP_ORIGIN'] ?? config('app.url');
        $allowOrigin = [
            config('app.url'),
            'http://localhost:8080',
        ];
        if (in_array($origin, $allowOrigin) || app()->environment() == 'local' || app()->environment() == 'testing') {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set(
                'Access-Control-Allow-Headers',
                'Origin, Content-Type, Cookie, X-CSRF-TOKEN, Accept, Authorization, X-XSRF-TOKEN'
            );
            $response->headers->set('Access-Control-Expose-Headers', 'Authorization');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }
        return $response;
    }
}
