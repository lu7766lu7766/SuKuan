<?php

namespace service;

class PaginateService
{
  public function proccess($db, $page, $per_page)
  {
    if ($page > 0) {
      $offset = ($page - 1) * $per_page;
      return $db->skip($offset)->take($per_page);
    } else {
      return $db;
    }
  }
}
