<?php

namespace lib;

class JsonUtils
{
  static public function objectToArray($obj)
  {
    return json_decode(json_encode($obj), true);
  }

  static public function arrayToObject($arr)
  {
    return json_decode(json_encode($arr));
  }

  static public function encode($obj, $config)
  {
    return json_encode($obj, $config);
  }
}
