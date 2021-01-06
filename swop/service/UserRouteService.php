<?php

namespace service;

use comm\Request;
use Illuminate\Database\Capsule\Manager as DB;
use Rakit\Validation\Validator;
use Exception;
use Tightenco\Collect\Support\Collection;

class UserRouteService
{
  private $tableName = "";

  function __construct($tableName)
  {
    $this->tableName = $tableName;
  }

  public function list(Request $request)
  {
    $db = DB::table($this->tableName);
    if (!session("isRoot")) {
      $db->where("UserID", session("choice"));
    }
    return $db->get();
  }

  private function validate($request)
  {
    $validator = new Validator();
    $validation = $validator->validate($request->all(), [
      'UserID'                 => 'required',
      'PrefixCode'             => 'required',
      'TrunkPort'              => 'required|min:1|max:65535',
    ]);
    if ($validation->fails()) {
      throw new Exception("驗證失敗，請確認欄位");
    }
  }

  public function create(Request $request)
  {

    $this->validate($request);

    if (
      DB::table($this->tableName)->where("UserID", $request->input("UserID"))->where("PrefixCode", $request->input("PrefixCode"))->count()
    ) {
      throw new Exception("用戶與前置碼重複，無法新增。");
    }
    return DB::table($this->tableName)->insert([
      "UserID" => $request->input("UserID"),
      "PrefixCode" => $request->input("PrefixCode"),
      "AddPrefix" => $request->input("AddPrefix"),
      "RouteCLI" => $request->input("RouteCLI"),
      "TrunkIP" => $request->input("TrunkIP"),
      "TrunkPort" => $request->input("TrunkPort"),
      "RouteName" => $request->input("RouteName"),
      "SubNum" => $request->input("SubNum")
    ]);
  }

  public function update(Request $request)
  {

    $this->validate($request);

    return DB::table($this->tableName)
      ->where("UserID", $request->input("UserID"))
      ->where("PrefixCode", $request->input("PrefixCode"))
      ->update([
        "AddPrefix" => $request->input("AddPrefix"),
        "RouteCLI" => $request->input("RouteCLI"),
        "TrunkIP" => $request->input("TrunkIP"),
        "TrunkPort" => $request->input("TrunkPort"),
        "RouteName" => $request->input("RouteName"),
        "SubNum" => $request->input("SubNum")
      ]);
  }

  public function delete(Request $request)
  {

    return DB::table($this->tableName)
      ->where("UserID", $request->input("UserID"))
      ->where("PrefixCode", $request->input("PrefixCode"))
      ->delete();
  }

  public function createBatch(Request $request)
  {

    return DB::table($this->tableName)->insert(
      collect($request->input("datas"))->map(function ($x) {
        return [
          "UserID" => $x["UserID"],
          "PrefixCode" => $x["PrefixCode"],
          "AddPrefix" => $x["AddPrefix"],
          "RouteCLI" => $x["RouteCLI"],
          "TrunkIP" => $x["TrunkIP"],
          "TrunkPort" => $x["TrunkPort"],
          "RouteName" => $x["RouteName"],
          "SubNum" => $x["SubNum"]
        ];
      })->toArray()
    );
  }
}
