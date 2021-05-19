<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ServerResponse
{
    /**
     * @param int $code Status code
     * @param string $message Success | Error message
     * @param null $data Data if any
     * @return JsonResponse
     */
    public function res(int $code = 200, string $message = '', $data = null): JsonResponse
    {
        $msgType = $code>= 400 ? 'error' : 'message';
        $response = array('code' => $code, $msgType => $message, 'data' => $data);

        return response()->json($response, $code);
    }
}
