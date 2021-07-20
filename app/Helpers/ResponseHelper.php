<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function build(array $response, int $status)
    {
        return response()->json($response, $status);
    }

    public static function success($response, $status = 200)
    {
        $buildableResponse = [ 'status' => 'success', 'data' => $response ];

        return static::build($buildableResponse, $status);
    }

    public static function fail($response, $status = 422)
    {
        return static::build([
            'status' => 'error', 
            'errors' => (array)$response
            ], $status
        );
    }
}