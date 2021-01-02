<?php

namespace comm;

use \lib\ReturnMessage;
use \Exception;

class Middleware
{
  static function api($next)
  {
    try {
      return ReturnMessage::success($next());
    } catch (Exception $err) {
      return ReturnMessage::error($err->getMessage());
    };
  }

  static function wrap($method, $next) 
  {
    return function () use($method, $next) {
      return self::$method($next);
    };
  }
}
