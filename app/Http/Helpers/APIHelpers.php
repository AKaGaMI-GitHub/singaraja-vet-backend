<?php

namespace App\Http\Helper;

class APIHelpers
{
    public static function responseAPI($data = [], $code, $timeInSeconds = null)
    {
        $timeInSeconds = $timeInSeconds ?? microtime(true) - LARAVEL_START;

        return response()->json([
            'queryTimes' => round($timeInSeconds, 3),
            'status' => Self::StatusCode($code),
            'datas' => $data
        ], $code);
    }

    private static function StatusCode($code)
    {
        $statusCodes = [
            200 => true,
        ];

        return $statusCodes[$code] ?? false;
    }
}
