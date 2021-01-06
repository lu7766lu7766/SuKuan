<?php

use comm\Request;
use Illuminate\Database\Capsule\Manager as DB;
use service\PaginateService;

class CommunicationController
{
  public function common(Request $request)
  {
    $db = DB::table("CallOutCDR")
      ->select(
        DB::raw("count(1) as rows"),
        DB::raw("sum(CallDuration) as totalTime"),
        DB::raw("sum(cast(BillValue as float)) as totalMoney")
      );
    $db = $this->buildWhere($db, $request);
    $res = $db->first();
    return [
      "rows" => $res->rows,
      "totalTime" => $res->totalTime,
      "totalMoney" => $res->totalMoney
    ];
  }

  public function list(Request $request)
  {
    $db = DB::table("CallOutCDR");
    $db = $this->buildWhere($db, $request);
    $db = (new PaginateService())->proccess($db, $request->input("page"), $request->input("per_page"));
    $db = $this->buildOrderBy($db, $request->input("sortKey"), $request->input("sortType"));
    return $db->get();
  }

  public function delete(Request $request)
  {
    if (!is_array($request->input("id")) || !count($request->input("id"))) {
      return false;
    } else {
      return DB::table("CallOutCDR")
        ->whereIn('LogID', $request->input("id"))
        ->update([
          "DeletedAt" => date('Y-m-d H:i:s', time())
        ]);
    }
  }

  private function buildWhere($db, $request)
  {
    if (!empty($request->input("userId"))) {
      $db->where("UserID", $request->input("userId"));
    } else {
      $db->whereIn("UserID", session("current_sub_emp"));
    }
    if (!empty($request->input("callStartBillingDate"))) {
      $callStartBillingTime = $request->input("callStartBillingTime", "00:00:00");
      $db->whereRaw(
        "cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) > ?",
        ["{$request->input("callStartBillingDate")} {$callStartBillingTime }"]
      );
    }
    if (!empty($request->input("callStopBillingDate"))) {
      $callStopBillingTime = $request->input("callStopBillingTime", "00:00:00");
      $db->whereRaw(
        "cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) < ?",
        ["{$request->input("callStopBillingDate")} {$callStopBillingTime}"]
      );
    }
    if (!empty($request->input("extensionNo"))) {
      $db->where("ExtensionNo", $request->input("extensionNo"));
    }
    if (!empty($request->input("orgCalledId"))) {
      $db->where("OrgCalledId", $request->input("orgCalledId"));
    }
    if (!empty($request->input("customerLevel"))) {
      $db->where("CustomerLevel", $request->input("customerLevel"));
    }
    if ($request->input("searchSec") !== "") {
      $db->where("CallDuration", ">=", $request->input("searchSec"));
    }
    if ($request->input("searchSec2") !== "") {
      $db->where("CallDuration", "<=", $request->input("searchSec2"));
    }
    if ($request->input("callType") !== "") {
      $db->where("CallType", $request->input("callType"));
    }
    if (!session("isRoot")) {
      $db->whereNull("DeletedAt");
    }
    return $db;
  }

  private function buildOrderBy($db, $key, $type)
  {
    if ($key == "CallStartBillingDate") {
      return $db->orderBy("CallStartBillingDate", $type)->orderBy("CallStartBillingTime", $type);
    } else if ($key == "CallStopBillingDate") {
      return $db->orderBy("CallStopBillingDate", $type)->orderBy("CallStopBillingTime", $type);
    } else {
      return $db->orderBy($key, $type);
    }
  }
}
