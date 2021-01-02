<?php

namespace comm;

use \lib\ReturnMessage;
use \Exception;

class Middleware
{
  static function api($funcResult)
  {
    try {
      return ReturnMessage::success($funcResult);
    } catch (Exception $err) {
      return ReturnMessage::error($err->getMessage());
    };
  }
}
