<?php

namespace comm;

use \lib\ReturnMessage;
use \Exception;

class Middleware
{
  function api($next)
  {
    try {
      return ReturnMessage::success($next());
    } catch (Exception $err) {
      return ReturnMessage::error($err->getMessage());
    };
  }

  function use($fn)
  {
    if (!$this->next) {
      $this->next = function () {
      };
    }
    $next = $this->next;
    $this->next = function () use ($fn, $next) {
      return $fn($next);
    };
  }

  function go()
  {
    if ($this->next) {
      return ($this->next)();
    }
  }
}
