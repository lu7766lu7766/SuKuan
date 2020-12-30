<?php

use Illuminate\Database\Capsule\Manager as DB;
use service\PaginateService;

class CommunicationController
{
  public function common($req)
  {
    $post = $req["post"];
    $db = DB::table("CallOutCDR")
      ->select(
        DB::raw("count(1) as rows"),
        DB::raw("sum(CallDuration) as totalTime"),
        DB::raw("sum(cast(BillValue as float)) as totalMoney")
      );
    $db = $this->buildWhere($db, $req);
    $res = $db->first();
    return [
      "rows" => $res->rows,
      "totalTime" => $res->totalTime,
      "totalMoney" => $res->totalMoney
    ];
  }

  public function list($req)
  {
    $post = $req["post"];
    $db = DB::table("CallOutCDR");
    $db = $this->buildWhere($db, $req);
    $db = (new PaginateService())->proccess($db, $post["page"], $post["per_page"]);
    $db = $this->buildOrderBy($db, $post["sortKey"], $post["sortType"]);
    return $db->get();
  }

  public function delete($req)
  {
    $post = $req["post"];
    if (!is_array($post["id"]) || !count($post["id"])) {
      return false;
    } else {
      return DB::table("CallOutCDR")
        ->whereIn('LogID', $post["id"])
        ->update([
          "DeletedAt" => date('Y-m-d H:i:s', time())
        ]);
    }
  }

  private function buildWhere($db, $req)
  {
    $post = $req["post"];
    $session = $req["session"];
    if (!empty($post["userId"])) {
      $db->where("UserID", $post["userId"]);
    } else {
      $db->whereIn("UserID", $session["current_sub_emp"]);
    }
    if (!empty($post["callStartBillingDate"])) {
      $post["callStartBillingTime"] = $post["callStartBillingTime"] ?? '00:00:00';
      $db->whereRaw(
        "cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) > ?",
        ["{$post["callStartBillingDate"]} {$post["callStartBillingTime"]}"]
      );
    }
    if (!empty($post["callStopBillingDate"])) {
      $post["callStopBillingTime"] = $post["callStopBillingTime"] ?? '00:00:00';
      $db->whereRaw(
        "cast((CallStartBillingDate+' '+CallStartBillingTime) as datetime) < ?",
        ["{$post["callStopBillingDate"]} {$post["callStopBillingTime"]}"]
      );
    }
    if (!empty($post["extensionNo"])) {
      $db->where("ExtensionNo", $post["extensionNo"]);
    }
    if (!empty($post["orgCalledId"])) {
      $db->where("OrgCalledId", $post["orgCalledId"]);
    }
    if (!empty($post["customerLevel"])) {
      $db->where("CustomerLevel", $post["customerLevel"]);
    }
    if ($post["searchSec"] !== "") {
      $db->where("CallDuration", ">=", $post["searchSec"]);
    }
    if ($post["searchSec2"] !== "") {
      $db->where("CallDuration", "<=", $post["searchSec2"]);
    }
    if ($post["callType"] !== "") {
      $db->where("CallType", $post["callType"]);
    }
    if (!$session["isRoot"]) {
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
