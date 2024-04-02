<?php

namespace App\Package\Sign\Http\Controllers;

use App\Models\User;
use App\Package\Sign\Handlers\SignCreditHandler;
use App\Package\Sign\Handlers\SignLogHandler;
use App\Package\Sign\Handlers\SignUpHandler;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

class SignController extends ApiController
{
    /**
     * 签到服务
     * @param Request $request
     * @return mixed
     */
    public function signIn(Request $request)
    {
        $pipes = [
            SignUpHandler::class,       // 每日签到
            SignLogHandler::class,      // 写入日志
            SignCreditHandler::class    // 积分调整
        ];

        try {
            app(Pipeline::class)
                ->send($request->user())
                ->through($pipes)
                ->then(function ($user) {   // 注：send 方法返回值会传递给 then 方法
                    return $user;
                });
            return $this->renderSuccess(trans('sign::msg.E401001'), 401001, 1);
        } catch (\Exception $e) {
            return $this->renderSuccess(trans('sign::msg.E401002'), 401002, 0);
        }
    }
}
