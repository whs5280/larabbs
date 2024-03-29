<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XssDeep
{
    /**
     * [作用] 全局过滤 $request 防止xss
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $data = xss_safe_filter($request->all());
        foreach ($data as $key => $value) {
            $request->request->set($key, $value);
        }
        return $next($request);
    }
}
