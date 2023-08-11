<?php

namespace App\Helpers;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse
{
    public static function createSuccessResponse($data)
    {
        return new JsonResponse([
            'success' => true,
            'message' =>  'SUCCESS',
            'data' =>  $data
        ], 200);
    }

    public static function createFailedResponse($error, $code = 400, $message = 'FAILED')
    {
        return new JsonResponse([
            'success' => false,
            'message' =>  $message,
            'error' =>  $error
        ], $code);
    }
}
