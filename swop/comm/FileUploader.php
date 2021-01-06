<?php

namespace comm;

use Exception;

class FileUploader
{
  private $fileInfo;

  public function __construct($key)
  {
    $this->fileInfo = $_FILES[$key];
  }

  public function getFile()
  {
    return $this->fileInfo["tmp_name"];
  }

  public function exists()
  {
    return file_exists($this->fileInfo['tmp_name']);
  }

  public function getErrorCode()
  {
    return $this->fileInfo["error"];
  }

  public function hasError()
  {
    return !!$this->getErrorCode();
  }

  public function read()
  {
    if ($this->hasError()) {
      throw new Exception("upload error code: {$this->getErrorCode()}!");
    }
    if (!$this->exists()) {
      throw new Exception("file not found!");
    }
    return file_get_contents($this->getFile());
  }

  public function readList()
  {
    $content = $this->read();
    return collect(preg_split("/\\r\\n|\\r|\\n/", $content))
      ->map(function ($x) {
        return trim($x);
      })->filter(function ($x) {
        return $x;
      })->toArray();
  }
}
