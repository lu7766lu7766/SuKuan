<?php

use Illuminate\Database\Capsule\Manager as DB;
use lib\ReturnMessage;
use Tightenco\Collect\Support\Collection;

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

  public function updateConcurrentCallsAmp($req)
  {
    ["post" => $post, "session" => $session] = $req;
    ReturnMessage::success(
      $this->buildUserWhere($session["choice"])->update([
        "ConcurrentCallsAmp" => $post["ConcurrentCallsAmp"]
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

  public function buildNumberListWhere($CallOutID)
  {
    return DB::table("NumberList")->select("CalledNumber")->where("CallOutID", $CallOutID);
  }

  public function numberList($req)
  {
    ["post" => $post] = $req;
    try {
      ReturnMessage::success(
        Collection($this->buildNumberListWhere($post["CallOutID"])->get())->pluck("CalledNumber")->toArray()
      );
    } catch (Exception $err) {
      ReturnMessage::error($err->getMessage());
    }
  }

  public function waitCall($req)
  {
    ["post" => $post] = $req;
    try {
      ReturnMessage::success(
        Collection(
          $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", 0)->get()
        )->pluck("CalledNumber")->toArray()
      );
    } catch (Exception $err) {
      ReturnMessage::error($err->getMessage());
    }
  }

  public function callOut($req)
  {
    ["post" => $post] = $req;
    try {
      ReturnMessage::success(
        Collection(
          $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", "<>", 0)->get()
        )->pluck("CalledNumber")->toArray()
      );
    } catch (Exception $err) {
      ReturnMessage::error($err->getMessage());
    }
  }

  public function callCon($req)
  {
    ["post" => $post] = $req;
    try {
      ReturnMessage::success(
        Collection(
          $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", 3)->get()
        )->pluck("CalledNumber")->toArray()
      );
    } catch (Exception $err) {
      ReturnMessage::error($err->getMessage());
    }
  }

  public function callFaild($req)
  {
    ["post" => $post] = $req;
    try {
      ReturnMessage::success(
        Collection(
          $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", 1)->get()
        )->pluck("CalledNumber")->toArray()
      );
    } catch (Exception $err) {
      ReturnMessage::error($err->getMessage());
    }
  }

  public function callMissed($req)
  {
    ["post" => $post] = $req;
    try {
      ReturnMessage::success(
        Collection(
          $this->buildNumberListWhere($post["CallOutID"])->where("CallResult", 2)->get()
        )->pluck("CalledNumber")->toArray()
      );
    } catch (Exception $err) {
      ReturnMessage::error($err->getMessage());
    }
  }

  public function callStatistics()
  {
    ReturnMessage::success(
      DB::table("CallState")->select(DB::raw("UserID, count(1) as Count"))->groupBy("UserID")->get()
    );
  }
}
