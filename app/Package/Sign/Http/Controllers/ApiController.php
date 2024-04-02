<?php


namespace App\Package\Sign\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    /**
     * @param string $message
     * @param string $code
     * @param int $status
     * @return JsonResponse
     */
    public function renderSuccess(string $message = '', string $code = '', int $status = 1): JsonResponse
    {
        $additionalData = [
            'message' => $message,
            'status'  => $status,
            'code'    => $code,
        ];

        return response()->json($additionalData);
    }
}
