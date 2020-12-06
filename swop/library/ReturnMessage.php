<?php

namespace lib;

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
            "code" => $code,
            "data" => $data
        ], $config);
    }
}
