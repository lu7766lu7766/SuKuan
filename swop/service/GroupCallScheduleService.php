<?php

namespace service;

use Exception;
use Illuminate\Database\Capsule\Manager as DB;

class GroupCallScheduleService
{


  function __construct()
  {
    $this->PHONE_LIMIT = getenv2("GROUP_CALL_PHONE_LIMIT", 200000);
    $this->LIST_LIMIT = getenv2("GROUP_CALL_LIST_LIMIT", 99999);
  }

  public function valideCallPlanMaxLimit($UserID)
  {
    $count = DB::table('CallPlan')->where('UserID', $UserID)->count();
    if ($count + 1 > $this->LIST_LIMIT) {
      throw new Exception("排程不得超過{$this->LIST_LIMIT}筆");
    }
  }

  public function valideCallOnceLimit($CalledCount)
  {
    if ($CalledCount > $this->PHONE_LIMIT) {
      throw new Exception("筆數不得超過{$this->PHONE_LIMIT}筆");
    }
  }

  public function getListAndValide()
  {
    $result = request()->file("list")->readList();
    $len = count($result);
    if (!$len) {
      throw new Exception("筆數異常，請重新檢查檔案");
    }
    if (!strlen($result[0])) {
      throw new Exception("起始電話異常，請重新檢查檔案");
    }
    return $result;
  }

  public function buildRangeNumberList($CallOutID, $StartCalledNumber,  $CalledCount)
  {
    $phone_len = strlen($StartCalledNumber);
    return collect(range(0, $CalledCount - 1))->map(function ($add) use ($CallOutID, $StartCalledNumber, $phone_len) {
      return [
        'CallOutID'    => $CallOutID,
        'CalledNumber' => str_pad($StartCalledNumber + $add, $phone_len, '0', STR_PAD_LEFT)
      ];
    });
  }

  public function buildListNumberList($CallOutID, $list)
  {
    return collect($list)->reverse()->map(function ($x) use ($CallOutID) {
      return [
        'CallOutID'    => $CallOutID,
        'CalledNumber' => $x
      ];
    });
  }

  public function buildSameNumberList($CallOutID, $StartCalledNumber,  $CalledCount)
  {
    return collect(range(1, $CalledCount))->map(function () use ($CallOutID, $StartCalledNumber) {
      return [
        'CallOutID'    => $CallOutID,
        'CalledNumber' => $StartCalledNumber
      ];
    });
  }

  public function getValidListAndValide($StartCalledNumber,  $CalledCount)
  {
    return collect(
      DB::connection("validDB")
        ->table("AllCdrList")
        ->distinct()
        ->select("OrgCalledId")
        ->where("OrgCalledId", ">", $StartCalledNumber)
        ->orderBy("OrgCalledId")
        ->take($CalledCount)
        ->get()
    )->pluck("OrgCalledId");
  }
}
