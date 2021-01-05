<?php

namespace comm;

class Session
{
  static function get($key)
  {
    return $_SESSION[config("folder")][$key];
  }

  static function set($key, $val)
  {
    $_SESSION[config("folder")][$key] = $val;
  }

  static function clear()
  {
    $_SESSION[config("folder")] = null;
  }
}
