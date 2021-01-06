<?php

namespace lib;

class ArrayUtils
{
  static public function toArray($obj)
  {
    return json_decode(json_encode($obj), true);
  }

  static public function toObject($arr)
  {
    return json_decode(json_encode($arr));
  }
}
