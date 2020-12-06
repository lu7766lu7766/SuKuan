<?php

namespace lib;

class ReturnMessage
{
    static function success($data)
    {
        self::print(0, $data);
    }

    static function error($data)
    {
        self::print(-1, $data);
    }

    static function print($code, $data)
    {
        echo json_encode([
            "code" => $code,
            "data" => $data
        ]);
    }
}
