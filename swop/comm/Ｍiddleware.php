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

  static function use($fn, $next = null)
  {
    if (!$next) {
      $next = function () {
      };
    }
    return function () use ($fn, $next) {
      return $fn($next);
    };
  }

  static function go($next)
  {
    if ($next) {
      return ($next)();
    }
  }
}
