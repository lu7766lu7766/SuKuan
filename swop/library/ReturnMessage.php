<?php

namespace lib;

use Illuminate\Database\Capsule\Manager as DB;

class ReturnMessage
{
    static function success($data, $config = JSON_PRETTY_PRINT)
    {
        self::print(0, $data, $config);
    }

    static function error($data, $config = JSON_PRETTY_PRINT)
    {
        self::print(-1, $data, $config);
    }

    static function print($code, $data, $config)
    {
        echo json_encode([
            "query" => getenv2("ENV") == "development" ? DB::getQueryLog() : null,
            "code" => $code,
            "data" => $data
        ], $config);
    }
}
