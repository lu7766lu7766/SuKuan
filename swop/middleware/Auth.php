<?php

namespace middleware;

use \lib\ReturnMessage;

class Auth
{
  function handle($next)
  {
    if (session("login")) {
      return $next();
    } else {
      return ReturnMessage::error("您已被登出，請重新登入");
    }
  }
}
