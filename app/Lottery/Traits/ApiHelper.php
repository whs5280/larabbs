<?php

namespace App\Lottery\Traits;

trait ApiHelper
{
    public function error($message, $httpCode = 400, $statusCode = 422)
    {
        return response()->setStatusCode($httpCode)->json([
            'message'    => $message,
            'statusCode' => $statusCode
        ]);
    }
}
