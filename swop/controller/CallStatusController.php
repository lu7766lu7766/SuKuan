<?php

use Illuminate\Database\Capsule\Manager as DB;
use Tightenco\Collect\Support\Collection;

class CallStatusController extends JController
{
  public function base($req)
  {
    ["session" => $session] = $req;
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

  public function calloutGroupID($req)
  {
    ["post" => $post, "session" => $session] = $req;
    return $this->buildCallPlanWhere($session["choice"], $post["CallOutID"])->update([
      "CalloutGroupID" => $post["CalloutGroupID"]
    ]);
  }

  public function deleteCallPlan($req)
  {
    ["post" => $post, "session" => $session] = $req;
    DB::transaction(function () use ($session, $post) {
      $this->buildCallPlanWhere($session["choice"], $post["CallOutID"])->delete();
      DB::table("NumberList")->where("CallOutID", $post["CallOutID"])->delete();
    });
    return true;
  }

  public function useState($req)
  {
    ["post" => $post, "session" => $session] = $req;
    return $this->buildCallPlanWhere($session["choice"], $post["CallOutID"])->update([
      "UseState" => $post["UseState"]
    ]);
  }

  private function buildUserWhere($userID)
  {
    return DB::table("SysUser")->where("UserID", $userID);
  }

  public function switchSuspend($req)
  {
    ["session" => $session] = $req;
    return $this->buildUserWhere($session["choice"])->update([
      "Suspend" => DB::raw("abs(Suspend-1)")
    ]);
  }

  public function updateMaxRoutingCalls($req)
  {
    ["post" => $post, "session" => $session] = $req;
    return $this->buildUserWhere($session["choice"])->update([
      "MaxRoutingCalls" => $post["MaxRoutingCalls"]
    ]);
  }

  public function updateConcurrentCallsAmp($req)
  {
    ["post" => $post, "session" => $session] = $req;
    return $this->buildUserWhere($session["choice"])->update([
      "ConcurrentCallsAmp" => $post["ConcurrentCallsAmp"]
    ]);
  }

  public function updateCallWaitingTime($req)
  {
    ["post" => $post, "session" => $session] = $req;
    return $this->buildUserWhere($session["choice"])->update([
      "CallWaitingTime" => $post["CallWaitingTime"]
    ]);
  }

  public function updatePlanDistribution($req)
  {
    ["post" => $post, "session" => $session] = $req;
    return $this->buildUserWhere($session["choice"])->update([
      "PlanDistribution" => $post["PlanDistribution"]
    ]);
  }

  public function callRelease($req)
  {
    ["post" => $post] = $req;
    $url = "http://127.0.0.1:60/CallRelease.atp?Seat=" . $post["Seat"] . "&CalledID=" . $post["CalledID"];
    comm\Http::get($url);
    return true;
  }

  public function buildNumberListWhere($CallOutID)
  {
    return DB::table("NumberList")->select("CalledNumber")->where("CallOutID", $CallOutID);
  }

  public function numberList($req)
  {
    ["post" => $post] = $req;
    return Collection(
      $this->buildNumberListWhere($post["CallOutID"])->get()
    )
      ->pluck("CalledNumber");
  }

  public function waitCall($req)
  {
    ["post" => $post] = $req;
    return Collection(
      $this->buildNumberListWhere($post["CallOutID"])
        ->where("CallResult", 0)
        ->get()
    )
      ->pluck("CalledNumber");
  }

  public function callOut($req)
  {
    ["post" => $post] = $req;
    return Collection(
      $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", "<>", 0)->get()
    )->pluck("CalledNumber");
  }

  public function callCon($req)
  {
    ["post" => $post] = $req;
    return Collection(
      $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", 3)->get()
    )->pluck("CalledNumber");
  }

  public function callFaild($req)
  {
    ["post" => $post] = $req;
    return Collection(
      $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", 1)->get()
    )->pluck("CalledNumber")->toArray();
  }

  public function callMissed($req)
  {
    ["post" => $post] = $req;
    return Collection(
      $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", 2)->get()
    )->pluck("CalledNumber");
  }

  public function callStatistics()
  {
    return DB::table("CallState")->select(DB::raw("UserID, count(1) as Count"))->groupBy("UserID")->get();
  }
}
