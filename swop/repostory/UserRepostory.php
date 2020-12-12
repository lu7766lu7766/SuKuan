<?php

namespace repostory;

use Illuminate\Database\Capsule\Manager as DB;

class UserRepostory
{
  /**
   * 取得使用者單筆資訊
   */
  public function getDetail($userID)
  {
    return DB::table("SysUser")->where("UserID", $userID)->first();
  }

  public function checkExists($userID)
  {
    return DB::table("SysUser")->where("UserID", $userID)->count();
  }

  public function create(
    $UserID,
    $UseState,
    $UserName,
    $NoteText,
    $RateGroupID,
    $Balance,
    $StartTime,
    $StopTime,
    $CallWaitingTime,
    $ParentID,
    $MenuList,
    $MaxRoutingCalls,
    $MaxCalls,
    $UserInfo,
    $Distributor,
    $AutoStartTime,
    $AutoStopTime,
    $Overdraft,
    $SearchStartTime,
    $SearchAutoStartTime,
    $SearchAutoStopTime,
    $SearchStopTime,
    $MaxSearchCalls,
    $MaxRegularCalls,
    $PermissionControl,
    $UserID2,
    $CanSwitchExtension
  ) {
    return DB::table("SysUser")->insert([
      "UserID" => $UserID,
      "UseState" => $UseState,
      "UserName" => $UserName,
      "NoteText" => $NoteText,
      "RateGroupID" => $RateGroupID,
      "Balance" => $Balance,
      "StartTime" => $StartTime,
      "StopTime" => $StopTime,
      "CallWaitingTime" => $CallWaitingTime,
      "ParentID" => $ParentID,
      "MenuList" => $MenuList,
      "MaxRoutingCalls" => $MaxRoutingCalls,
      "MaxCalls" => $MaxCalls,
      "UserInfo" => $UserInfo,
      "Distributor" => $Distributor,
      "AutoStartTime" => $AutoStartTime,
      "AutoStopTime" => $AutoStopTime,
      "Overdraft" => $Overdraft,
      "SearchStartTime" => $SearchStartTime,
      "SearchAutoStartTime" => $SearchAutoStartTime,
      "SearchAutoStopTime" => $SearchAutoStopTime,
      "SearchStopTime" => $SearchStopTime,
      "MaxSearchCalls" => $MaxSearchCalls,
      "MaxRegularCalls" => $MaxRegularCalls,
      "PermissionControl" => $PermissionControl,
      "UserID2" => $UserID2,
      "CanSwitchExtension" => $CanSwitchExtension
    ]);
  }

  public function update(
    $UserID,
    $UseState,
    $UserName,
    $NoteText,
    $RateGroupID,
    $Balance,
    $StartTime,
    $StopTime,
    $CallWaitingTime,
    $ParentID,
    $MenuList,
    $MaxRoutingCalls,
    $MaxCalls,
    $UserInfo,
    $Distributor,
    $AutoStartTime,
    $AutoStopTime,
    $Overdraft,
    $SearchStartTime,
    $SearchAutoStartTime,
    $SearchAutoStopTime,
    $SearchStopTime,
    $MaxSearchCalls,
    $MaxRegularCalls,
    $PermissionControl,
    $UserID2,
    $CanSwitchExtension
  ) {
    return DB::table("SysUser")
      ->where("UserID", $UserID)
      ->update([
        "UseState" => $UseState,
        "UserName" => $UserName,
        "NoteText" => $NoteText,
        "RateGroupID" => $RateGroupID,
        "Balance" => $Balance,
        "StartTime" => $StartTime,
        "StopTime" => $StopTime,
        "CallWaitingTime" => $CallWaitingTime,
        "ParentID" => $ParentID,
        "MenuList" => $MenuList,
        "MaxRoutingCalls" => $MaxRoutingCalls,
        "MaxCalls" => $MaxCalls,
        "UserInfo" => $UserInfo,
        "Distributor" => $Distributor,
        "AutoStartTime" => $AutoStartTime,
        "AutoStopTime" => $AutoStopTime,
        "Overdraft" => $Overdraft,
        "SearchStartTime" => $SearchStartTime,
        "SearchAutoStartTime" => $SearchAutoStartTime,
        "SearchAutoStopTime" => $SearchAutoStopTime,
        "SearchStopTime" => $SearchStopTime,
        "MaxSearchCalls" => $MaxSearchCalls,
        "MaxRegularCalls" => $MaxRegularCalls,
        "PermissionControl" => $PermissionControl,
        "UserID2" => $UserID2,
        "CanSwitchExtension" => $CanSwitchExtension
      ]);
  }

  public function createChargeLog($UserID, $AddBalance, $AddTime, $SaverID)
  {
    return DB::table("RechargeLog")->insert([
      "UserID" => $UserID,
      "AddValue" => $AddBalance,
      "AddTime" => $AddTime,
      "SaveUserID" => $SaverID
    ]);
  }

  public function updateMenuList($UserID, $MenuList)
  {
    return DB::table("SysUser")->where("UserID", $UserID)->update(["MenuList" => $MenuList]);
  }
}
