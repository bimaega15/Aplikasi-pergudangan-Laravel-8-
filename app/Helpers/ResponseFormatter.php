<?php

namespace App\Helpers;

/**
 * Format response.
 */
class ResponseFormatter
{
    /**
     * API Response
     *
     * @var array
     */
    protected static $response = [
        'status' => 200,
        'message' => 'success',
        'result' => [],
        'token' => ''
    ];

    /**
     * Give success response.
     */
    public static function success($response)
    {
        self::$response['status'] = $response['status'];
        self::$response['message'] = $response['message'];
        if (isset($response['result'])) {
            if ($response['result'] != null) {
                self::$response['result'] = $response['result'];
            }
        }
        if (isset($response['token'])) {
            if ($response['token'] != null) {
                self::$response['token'] = $response['token'];
            }
        }

        return response()->json($response, self::$response['status']);
    }

    /**
     * Give error response.
     */
    public static function error($response)
    {
        self::$response['status'] = $response['status'];
        self::$response['message'] = $response['message'];
        if (isset($response['result'])) {
            if ($response['result'] != null) {
                self::$response['result'] = $response['result'];
            }
        }
        return response()->json($response, self::$response['status']);
    }
}
