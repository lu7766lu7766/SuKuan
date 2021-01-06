<?php

namespace comm;

class Middleware
{
  public function __construct()
  {
    $this->next = function () {
    };
  }

  private $map = [
    "api" => \middleware\Api::class
  ];

  function use($key): void
  {
    if ($key instanceof \Closure) {
      $this->next = $key;
    } else {
      $next = $this->next;
      $this->next = function () use ($key, $next) {
        return (new $this->map[$key])->handle($next);
      };
    }
  }

  function go()
  {
    return ($this->next)();
  }
}
