<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

/**
 * 统一响应规范
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 成功响应
     * @param $data
     * @param string $message
     * @param int $code
     * @param array $headers
     * @param array $option
     * @return JsonResponse|JsonResource
     */
    public function success($data = null, string $message = '', int $code = Response::HTTP_OK, array $headers = [], array $option = [])
    {
        $message = (!$message && isset(Response::$statusTexts[$code])) ? Response::$statusTexts[$code] : 'OK';
        $additionalData = [
            'status' => 'success',
            'code'   => $code,
            'message' => $message,
            'extra'  => $option
        ];

        if ($data instanceof JsonResource) {
            return $data->additional($additionalData);
        }

        return response()->json(array_merge($additionalData, ['data' => $data ?: (object) $data]), $code, $headers);
    }


    /**
     * 创建响应
     * @param $data
     * @param string $message
     * @param string $location
     * @return JsonResponse|JsonResource
     */
    public function created($data = null, string $message = 'Created', string $location = '')
    {
        $response = $this->success($data, $message, Response::HTTP_CREATED);
        if ($location) {
            $response->header('Location', $location);
        }

        return $response;
    }


    /**
     * 错误响应
     * @param string $message
     * @param int $code [HTTP码]
     * @param $statusCode [业务状态码]
     * @return JsonResponse|object
     */
    public function responseError(string $message = '操作失败', int $code = 500, $statusCode = null)
    {
        $data = [
            "message" => $message,
            "code"    => $code
        ];

        if ($statusCode) {
            $data['status_code'] = $statusCode;
        }

        return (new JsonResponse())->setStatusCode($code)->setData($data);
    }
}
