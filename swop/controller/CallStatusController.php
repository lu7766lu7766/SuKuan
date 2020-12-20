<?php

use Illuminate\Database\Capsule\Manager as DB;
use lib\ReturnMessage;

class CallStatusController extends JController
{
  public function base($req)
  {
    ["session" => $session] = $req;
    $user = DB::table("SysUser")->where("UserID", $session["choice"])->first();
    $user->isSuspend = $user->Suspend != "1";
    ReturnMessage::success($user);
  }

  private function buildCallPlanWhere($userID, $callOutID)
  {
    return DB::table("CallPlan")
      ->where("UserID", $userID)
      ->where("CallOutID", $callOutID);
  }

  public function concurrentCalls($req)
  {
    ["post" => $post, "session" => $session] = $req;
    ReturnMessage::success(
      $this->buildCallPlanWhere($session["choice"], $post["CallOutID"])->update([
        "ConcurrentCalls" => $post["ConcurrentCalls"]
      ])
    );
  }

  public function calloutGroupID($req)
  {
    ["post" => $post, "session" => $session] = $req;
    ReturnMessage::success(
      $this->buildCallPlanWhere($session["choice"], $post["CallOutID"])->update([
        "CalloutGroupID" => $post["CalloutGroupID"]
      ])
    );
  }

  public function deleteCallPlan($req)
  {
    ["post" => $post, "session" => $session] = $req;
    try {
      DB::transaction(function () use ($session, $post) {
        $this->buildCallPlanWhere($session["choice"], $post["CallOutID"])->delete();
        DB::table("NumberList")->where("CallOutID", $post["CallOutID"])->delete();
      });
      ReturnMessage::success(true);
    } catch (Exception $e) {
      ReturnMessage::error("刪除失敗");
    }
  }

  public function useState($req)
  {
    ["post" => $post, "session" => $session] = $req;
    ReturnMessage::success(
      $this->buildCallPlanWhere($session["choice"], $post["CallOutID"])->update([
        "UseState" => $post["UseState"]
      ])
    );
  }

  private function buildUserWhere($userID)
  {
    return DB::table("SysUser")->where("UserID", $userID);
  }

  public function switchSuspend($req)
  {
    ["session" => $session] = $req;
    ReturnMessage::success(
      $this->buildUserWhere($session["choice"])->update([
        "Suspend" => DB::raw("abs(Suspend-1)")
      ])
    );
  }

  public function updateMaxRoutingCalls($req)
  {
    ["post" => $post, "session" => $session] = $req;
    ReturnMessage::success(
      $this->buildUserWhere($session["choice"])->update([
        "MaxRoutingCalls" => $post["MaxRoutingCalls"]
      ])
    );
  }

  public function updateMaxCalls($req)
  {
    ["post" => $post, "session" => $session] = $req;
    ReturnMessage::success(
      $this->buildUserWhere($session["choice"])->update([
        "MaxCalls" => $post["MaxCalls"]
      ])
    );
  }

  public function updateCallWaitingTime($req)
  {
    ["post" => $post, "session" => $session] = $req;
    ReturnMessage::success(
      $this->buildUserWhere($session["choice"])->update([
        "CallWaitingTime" => $post["CallWaitingTime"]
      ])
    );
  }

  public function updatePlanDistribution($req)
  {
    ["post" => $post, "session" => $session] = $req;
    ReturnMessage::success(
      $this->buildUserWhere($session["choice"])->update([
        "PlanDistribution" => $post["PlanDistribution"]
      ])
    );
  }

  public function callRelease($req)
  {
    ["post" => $post] = $req;
    $url = "http://127.0.0.1:60/CallRelease.atp?Seat=" . $post["Seat"] . "&CalledID=" . $post["CalledID"];
    comm\Http::get($url);
    ReturnMessage::success(true);
  }
}
