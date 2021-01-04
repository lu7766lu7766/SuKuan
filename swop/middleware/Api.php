<?php

namespace middleware;

use \lib\ReturnMessage;
use \Exception;

class Api
{
  function handle($next)
  {
    try {
      return ReturnMessage::success($next());
    } catch (Exception $err) {
      return ReturnMessage::error($err->getMessage());
    };
  }
}
