<?php

use comm\Request;
use Illuminate\Database\Capsule\Manager as DB;

class ExtensionManageController extends JController
{
	private function buildWhere($db, $request)
	{
		if (!empty($request->input("UserID"))) {
			$db->where("CustomerLists.UserID", $request->input("UserID"));
		}
		if (!empty($request->input("SearchContent"))) {
			$db->where(function ($sdb) use ($request) {
				return $sdb
					->orWhere("CustomerLists.UserID", $request->input("SearchContent"))
					->orWhere("CustomerLists.ExtensionNo", $request->input("SearchContent"));
			});
		}
		return $db->whereIn("CustomerLists.UserID", session("current_sub_emp"));
	}

	public function list(Request $request)
	{
		return
			$this->buildWhere(DB::table("CustomerLists"), $request)
			->select(
				'CustomerLists.UserID',
				'CustomerLists.ExtName',
				'CustomerLists.ExtensionNo',
				'RegisteredLogs.HostInfo',
				'CustomerLists.StartRecorder',
				'ExtensionGroup.CalloutGroupID',
				'CustomerLists.Suspend',
				'RegisteredLogs.ETime',
				'RegisteredLogs.PingTime',
				'RegisteredLogs.Received',
				'CustomerLists.UseState',
				'CustomerLists.OffNetCli'
			)
			->orderBy("CustomerLists.UserID")
			->leftJoin("ExtensionGroup", [["CustomerLists.UserID", "ExtensionGroup.UserID"], ["CustomerLists.ExtensionNo", "ExtensionGroup.CustomerNO"]])
			->leftJoin("RegisteredLogs", "CustomerLists.ExtensionNo", "=", "RegisteredLogs.CustomerNO")->get();
	}

	public function total(Request $request)
	{
		return $this->buildWhere(DB::table("CustomerLists"), $request)->count();
	}

	public function delete(Request $request)
	{
		if (!count($request->input("datas"))) {
			throw new Exception("無資料");
		}
		DB::transaction(function () use ($request) {
			$db1 = DB::table("ExtensionGroup");
			$db2 = DB::table("CustomerLists");
			foreach ($request->input("datas") as $data) {
				$where = function ($sdb) use ($data) {
					return $sdb->where([
						["UserID", $data["UserID"]],
						["CustomerNO", $data["ExtensionNo"]]
					]);
				};
				$db1->orWhere($where);
				$db2->orWhere($where);
			}
			$db1->delete();
			$db2->delete();
		});
		return true;
	}

	public function detail(Request $request)
	{
		return
			DB::table("CustomerLists as a")
			->select("a.UserID", "a.ExtensionNo", "a.ExtName", "a.CustomerPwd", "a.StartRecorder", "a.Suspend", "a.UseState", "b.CalloutGroupID", "a.OffNetCli")
			->leftJoin("ExtensionGroup as b", [["a.UserID", "b.UserID"], ["a.CustomerNO", "b.CustomerNO"]])
			->where("a.UserID", $request->input("UserID"))
			->where("a.ExtensionNo", $request->input("ExtensionNo"))
			->first();
	}

	public function create(Request $request)
	{
		$extensions = range($request->input("ExtensionNo"), $request->input("ExtensionNos", $request->input("ExtensionNo")));
		$insertBody1 = collect($extensions)
			->map(function ($x) use ($request) {
				return [
					"CustomerNO" => $x,
					"UserID" => $request->input("UserID"),
					"ExtensionNo" => $x,
					"UserName" => $x,
					"ExtName" => $request->input("ExtName"),
					"OffNetCli" => $request->input("OffNetCli"),
					"CustomerPwd" => $request->input("CustomerPwd"),
				];
			});
		$db =	DB::table("CustomerLists")->select("CustomerNO")->whereIn("CustomerNO", $insertBody1->pluck("CustomerNO")->toArray())->get();
		if ($db->count()) {
			throw new Exception($db->pluck("CustomerNO")->join(",") . "分機已存在，請避開這些分機");
		}
		$insertBody2 = collect($extensions)
			->map(function ($x) use ($request) {
				return [
					"UserID" => $request->input("UserID"),
					"CalloutGroupID" => $request->input("CalloutGroupID"),
					"CustomerNO" => $x,
				];
			});
		DB::transaction(function () use ($insertBody1, $insertBody2) {
			DB::table("CustomerLists")->insert($insertBody1->toArray());
			DB::table("ExtensionGroup")->insert($insertBody2->toArray());
		});
		return true;
	}

	public function update(Request $request)
	{
		$updateBody = $request->only([
			"ExtName", "CustomerPwd", "StartRecorder", "Suspend", "UseState"
		]);
		if (session("isRoot")) {
			$updateBody["OffNetCli"] = $request->input("OffNetCli");
		}
		DB::transaction(function () use ($request, $updateBody) {
			DB::table("CustomerLists")
				->where("UserID", $request->input("UserID"))
				->where("ExtensionNo", $request->input("ExtensionNo"))
				->update($updateBody);
			DB::table("ExtensionGroup")
				->where("UserID", $request->input("UserID"))
				->where("CustomerNO", $request->input("ExtensionNo"))
				->update([
					"CalloutGroupID" => $request->input("CalloutGroupID")
				]);
		});
		return true;
	}
}
