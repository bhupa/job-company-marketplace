<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    public function success($result, $message = '')
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $result,
        ];

        return response()->json($response, 200);
    }

    public function errors($message, $code)
    {
        $response = [
            'success' => false,
            'errors' => $message,
        ];

        return response()->json($response, $code);
    }
}
