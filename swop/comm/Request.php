<?php

namespace comm;

class Request
{
  static private $instance;

  static public function getInstance(): Request
  {
    if (!isset(self::$instance)) {
      self::$instance = new Request();
    }
    return self::$instance;
  }

  public function input(string $key, $defaultValue = null)
  {
    return $_GET[$key] ?? $_POST[$key] ?? $defaultValue;
  }

  public function only(array $keys): array
  {
    return collect($keys)->reduce(function ($result, $key) {
      $result[$key] = $this->input($key);
      return $result;
    }, []);
  }

  public function all(): array
  {
    return collect($_GET)->merge($_POST)->all();
  }

  public function file(string $key): FileUploader
  {
    return new FileUploader($key);
  }

  public function setParams($params): void
  {
    $this->params = $params;
  }

  public function param($key)
  {
    return $this->params[$key];
  }
}
