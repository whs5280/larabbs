<?php

namespace App\Sign\Http\Controllers;

use App\Sign\Handlers\SignCreditHandler;
use App\Sign\Handlers\SignLogHandler;
use App\Sign\Handlers\SignUpHandler;
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
            // 每日签到
            SignUpHandler::class,
            // 写入日志
            SignLogHandler::class,
            // 积分调整
            SignCreditHandler::class
        ];

        return app(Pipeline::class)
            ->send($request->user())
            ->through($pipes)
            ->then(function () {
                try {
                    return $this->renderSuccess(trans('location::msg.E401001'), 401001, 1);
                } catch (\Exception $e) {
                    return $this->renderSuccess(trans('location::msg.E401002'), 401002, 0);
                }
            });
    }
}
