<?php

namespace App\Package\Lottery\Traits;

trait ApiHelper
{
    public function error($message, $httpCode = 400, $statusCode = 422)
    {
        return response()->json([
            'message'    => $message,
            'statusCode' => $statusCode,
        ])->setStatusCode($httpCode);
    }
}
