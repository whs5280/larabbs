<?php


namespace App\Sign\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response as HttpResponse;

class ApiController extends Controller
{
    /**
     * @param string $message
     * @param string $code  [业务状态码]
     * @param int $status
     * @param array $data
     * @param int $httpCode [HTTP状态码]
     * @param array $headers
     * @param int $option
     * @return \Illuminate\Http\JsonResponse|JsonResource
     */
    public function renderSuccess($message = '', $code = '', $status = 1 ,$data = [], $httpCode = HttpResponse::HTTP_OK, array $headers = [], $option = 0)
    {
        $message = (!$message && isset(HttpResponse::$statusTexts[$httpCode])) ? HttpResponse::$statusTexts[$httpCode] : 'OK';
        $additionalData = [
            'message' => $message,
            'status'  => $status,
            'code'    => $code,
        ];

        if ($data instanceof JsonResource) {
            return $data->additional($additionalData);
        }

        return response()->json(array_merge($additionalData, ['data' => $data ?: (object) $data]), $httpCode, $headers, $option);
    }
}
