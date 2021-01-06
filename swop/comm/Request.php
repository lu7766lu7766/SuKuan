<?php

namespace comm;

class Request
{
  static private $instance;

  static public function getInstance()
  {
    if (!isset(self::$instance)) {
      self::$instance = new Request();
    }
    return self::$instance;
  }

  public function input($key, $defaultValue = null)
  {
    return $_GET[$key] ?? $_POST[$key] ?? $defaultValue;
  }

  public function only($keys)
  {
    return collect($keys)->map(function ($key) {
      return $this->input($key);
    })->toArray();
  }

  public function all()
  {
    return collect($_GET)->merge($_POST)->all();
  }

  public function file($key)
  {
    return new FileUploader($key);
  }
}
