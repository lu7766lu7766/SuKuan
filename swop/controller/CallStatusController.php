<?php

use comm\Request;
use Illuminate\Database\Capsule\Manager as DB;

class CallStatusController extends JController
{
  public function base()
  {
    $user = DB::table("SysUser")->where("UserID", session("choice"))->first();
    $user->isSuspend = $user->Suspend != "1";
    return $user;
  }

  private function buildCallPlanWhere($userID, $callOutID)
  {
    return DB::table("CallPlan")
      ->where("UserID", $userID)
      ->where("CallOutID", $callOutID);
  }

  public function calloutGroupID(Request $request)
  {
    return $this->buildCallPlanWhere(session("choice"), $request->input("CallOutID"))->update([
      "CalloutGroupID" => $request->input("CalloutGroupID")
    ]);
  }

  public function deleteCallPlan(Request $request)
  {
    DB::transaction(function () use ($request) {
      $this->buildCallPlanWhere(session("choice"), $request->input("CallOutID"))->delete();
      DB::table("NumberList")->where("CallOutID", $request->input("CallOutID"))->delete();
    });
    return true;
  }

  public function useState(Request $request)
  {
    return $this->buildCallPlanWhere(session("choice"), $request->input("CallOutID"))->update([
      "UseState" => $request->input("UseState")
    ]);
  }

  private function buildUserWhere($userID)
  {
    return DB::table("SysUser")->where("UserID", $userID);
  }

  public function switchSuspend()
  {
    return $this->buildUserWhere(session("choice"))->update([
      "Suspend" => DB::raw("abs(Suspend-1)")
    ]);
  }

  public function updateMaxRoutingCalls(Request $request)
  {
    return $this->buildUserWhere(session("choice"))->update([
      "MaxRoutingCalls" => $request->input("MaxRoutingCalls")
    ]);
  }

  public function updateMaxCalls(Request $request)
  {
    return $this->buildUserWhere(session("choice"))->update([
      "MaxCalls" => $request->input("MaxCalls")
    ]);
  }

  public function updateConcurrentCallsAmp(Request $request)
  {
    return $this->buildUserWhere(session("choice"))->update([
      "ConcurrentCallsAmp" => $request->input("ConcurrentCallsAmp")
    ]);
  }

  public function updateCallWaitingTime(Request $request)
  {
    return $this->buildUserWhere(session("choice"))->update([
      "CallWaitingTime" => $request->input("CallWaitingTime")
    ]);
  }

  public function updatePlanDistribution(Request $request)
  {
    return $this->buildUserWhere(session("choice"))->update([
      "PlanDistribution" => $request->input("PlanDistribution")
    ]);
  }

  public function callRelease(Request $request)
  {
    $url = "http://127.0.0.1:60/CallRelease.atp?Seat=" . $request->input("Seat") . "&CalledID=" . $request->input("CalledID");
    comm\Http::get($url);
    return true;
  }

  public function buildNumberListWhere($CallOutID)
  {
    return DB::table("NumberList")->select("CalledNumber")->where("CallOutID", $CallOutID)->orderBy("OrderKey");
  }

  public function numberList(Request $request)
  {
    return collect(
      $this->buildNumberListWhere($request->input("CallOutID"))->get()
    )
      ->pluck("CalledNumber");
  }

  public function waitCall(Request $request)
  {
    return collect(
      $this->buildNumberListWhere($request->input("CallOutID"))
        ->where("CallResult", 0)
        ->get()
    )
      ->pluck("CalledNumber");
  }

  public function callOut(Request $request)
  {
    return collect(
      $this->buildNumberListWhere($request->input("CallOutID"))->where("CallResult", "<>", 0)->get()
    )->pluck("CalledNumber");
  }

  public function callCon(Request $request)
  {
    return collect(
      $this->buildNumberListWhere($request->input("CallOutID"))->where("CallResult", 3)->get()
    )->pluck("CalledNumber");
  }

  public function callFaild(Request $request)
  {
    return collect(
      $this->buildNumberListWhere($request->input("CallOutID"))->where("CallResult", 1)->get()
    )->pluck("CalledNumber")->toArray();
  }

  public function callMissed(Request $request)
  {
    return collect(
      $this->buildNumberListWhere($request->input("CallOutID"))->where("CallResult", 2)->get()
    )->pluck("CalledNumber");
  }

  public function callStatistics()
  {
    return DB::table("CallState")->select(DB::raw("UserID, count(1) as Count, (select count(1) from CustomerLists where Suspend = 0 and UserID=CallState.UserID) as StatusCount"))->groupBy("UserID")->get();
  }
}
