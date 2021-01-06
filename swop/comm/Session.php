<?php

namespace comm;

class Session
{
  static function get(string $key)
  {
    return $_SESSION[config("folder")][$key];
  }

  static function set(string $key, $val)
  {
    $_SESSION[config("folder")][$key] = $val;
  }

  static function clear()
  {
    $_SESSION[config("folder")] = null;
  }
}
