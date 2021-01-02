<?php

use Illuminate\Database\Capsule\Manager as DB;
use Tightenco\Collect\Support\Collection;

class CallStatusController extends JController
{
  public function base($ctx)
  {
    ["session" => $session] = $ctx;
    $user = DB::table("SysUser")->where("UserID", $session["choice"])->first();
    $user->isSuspend = $user->Suspend != "1";
    return $user;
  }

  private function buildCallPlanWhere($userID, $callOutID)
  {
    return DB::table("CallPlan")
      ->where("UserID", $userID)
      ->where("CallOutID", $callOutID);
  }

  public function calloutGroupID($ctx)
  {
    ["post" => $post, "session" => $session] = $ctx;
    return $this->buildCallPlanWhere($session["choice"], $post["CallOutID"])->update([
      "CalloutGroupID" => $post["CalloutGroupID"]
    ]);
  }

  public function deleteCallPlan($ctx)
  {
    ["post" => $post, "session" => $session] = $ctx;
    DB::transaction(function () use ($session, $post) {
      $this->buildCallPlanWhere($session["choice"], $post["CallOutID"])->delete();
      DB::table("NumberList")->where("CallOutID", $post["CallOutID"])->delete();
    });
    return true;
  }

  public function useState($ctx)
  {
    ["post" => $post, "session" => $session] = $ctx;
    return $this->buildCallPlanWhere($session["choice"], $post["CallOutID"])->update([
      "UseState" => $post["UseState"]
    ]);
  }

  private function buildUserWhere($userID)
  {
    return DB::table("SysUser")->where("UserID", $userID);
  }

  public function switchSuspend($ctx)
  {
    ["session" => $session] = $ctx;
    return $this->buildUserWhere($session["choice"])->update([
      "Suspend" => DB::raw("abs(Suspend-1)")
    ]);
  }

  public function updateMaxRoutingCalls($ctx)
  {
    ["post" => $post, "session" => $session] = $ctx;
    return $this->buildUserWhere($session["choice"])->update([
      "MaxRoutingCalls" => $post["MaxRoutingCalls"]
    ]);
  }

  public function updateConcurrentCallsAmp($ctx)
  {
    ["post" => $post, "session" => $session] = $ctx;
    return $this->buildUserWhere($session["choice"])->update([
      "ConcurrentCallsAmp" => $post["ConcurrentCallsAmp"]
    ]);
  }

  public function updateCallWaitingTime($ctx)
  {
    ["post" => $post, "session" => $session] = $ctx;
    return $this->buildUserWhere($session["choice"])->update([
      "CallWaitingTime" => $post["CallWaitingTime"]
    ]);
  }

  public function updatePlanDistribution($ctx)
  {
    ["post" => $post, "session" => $session] = $ctx;
    return $this->buildUserWhere($session["choice"])->update([
      "PlanDistribution" => $post["PlanDistribution"]
    ]);
  }

  public function callRelease($ctx)
  {
    ["post" => $post] = $ctx;
    $url = "http://127.0.0.1:60/CallRelease.atp?Seat=" . $post["Seat"] . "&CalledID=" . $post["CalledID"];
    comm\Http::get($url);
    return true;
  }

  public function buildNumberListWhere($CallOutID)
  {
    return DB::table("NumberList")->select("CalledNumber")->where("CallOutID", $CallOutID);
  }

  public function numberList($ctx)
  {
    ["post" => $post] = $ctx;
    return collect(
      $this->buildNumberListWhere($post["CallOutID"])->get()
    )
      ->pluck("CalledNumber");
  }

  public function waitCall($ctx)
  {
    ["post" => $post] = $ctx;
    return collect(
      $this->buildNumberListWhere($post["CallOutID"])
        ->where("CallResult", 0)
        ->get()
    )
      ->pluck("CalledNumber");
  }

  public function callOut($ctx)
  {
    ["post" => $post] = $ctx;
    return collect(
      $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", "<>", 0)->get()
    )->pluck("CalledNumber");
  }

  public function callCon($ctx)
  {
    ["post" => $post] = $ctx;
    return collect(
      $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", 3)->get()
    )->pluck("CalledNumber");
  }

  public function callFaild($ctx)
  {
    ["post" => $post] = $ctx;
    return collect(
      $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", 1)->get()
    )->pluck("CalledNumber")->toArray();
  }

  public function callMissed($ctx)
  {
    ["post" => $post] = $ctx;
    return collect(
      $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", 2)->get()
    )->pluck("CalledNumber");
  }

  public function callStatistics()
  {
    return DB::table("CallState")->select(DB::raw("UserID, count(1) as Count"))->groupBy("UserID")->get();
  }
}
