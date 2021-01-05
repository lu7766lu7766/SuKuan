<?php

namespace lib;

use Illuminate\Database\Capsule\Manager as DB;

class ReturnMessage
{
    static function success($data, $config = JSON_PRETTY_PRINT)
    {
        return self::format(0, $data, $config);
    }

    static function error($data, $config = JSON_PRETTY_PRINT)
    {
        return self::format(-1, $data, $config);
    }

    static function format($code, $data, $config)
    {
        return json_encode([
            "query" => isDev() ? DB::getQueryLog() : null,
            "code" => $code,
            "data" => $data
        ], $config);
    }
}
