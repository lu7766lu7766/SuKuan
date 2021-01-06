<?php

use Illuminate\Database\Capsule\Manager as DB;

class ExtensionManageController extends JController
{
	private function buildWhere($db, $ctx)
	{
		["post" => $post] = $ctx;
		if (!empty($post["UserID"])) {
			$db->where("CustomerLists.UserID", $post["UserID"]);
		}
		if (!empty($post["SearchContent"])) {
			$db->where(function ($sdb) use ($post) {
				return $sdb
					->orWhere("CustomerLists.UserID", $post["SearchContent"])
					->orWhere("CustomerLists.ExtensionNo", $post["SearchContent"]);
			});
		}
		return $db->whereIn("CustomerLists.UserID", session("current_sub_emp"));
	}

	public function list($ctx)
	{
		return
			$this->buildWhere(DB::table("CustomerLists"), $ctx)
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

	public function total($ctx)
	{
		return $this->buildWhere(DB::table("CustomerLists"), $ctx)->count();
	}

	public function delete($ctx)
	{
		["post" => $post] = $ctx;
		if (!count($post["datas"])) {
			throw new Exception("無資料");
		}
		DB::transaction(function () use ($post) {
			$db1 = DB::table("ExtensionGroup");
			$db2 = DB::table("CustomerLists");
			foreach ($post["datas"] as $data) {
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

	public function detail($ctx)
	{
		["post" => $post] = $ctx;
		return
			DB::table("CustomerLists as a")
			->select("a.UserID", "a.ExtensionNo", "a.ExtName", "a.CustomerPwd", "a.StartRecorder", "a.Suspend", "a.UseState", "b.CalloutGroupID", "a.OffNetCli")
			->leftJoin("ExtensionGroup as b", [["a.UserID", "b.UserID"], ["a.CustomerNO", "b.CustomerNO"]])
			->where("a.UserID", $post["UserID"])
			->where("a.ExtensionNo", $post["ExtensionNo"])
			->first();
	}

	public function create($ctx)
	{
		["post" => $post] = $ctx;
		$extensions = range($post["ExtensionNo"], $post["ExtensionNos"] ?? $post["ExtensionNo"]);
		$insertBody1 = collect($extensions)
			->map(function ($x) use ($post) {
				return [
					"CustomerNO" => $x,
					"UserID" => $post["UserID"],
					"ExtensionNo" => $x,
					"UserName" => $x,
					"ExtName" => $post["ExtName"],
					"OffNetCli" => $post["OffNetCli"],
					"CustomerPwd" => $post["CustomerPwd"],
				];
			});
		$db =	DB::table("CustomerLists")->select("CustomerNO")->whereIn("CustomerNO", $insertBody1->pluck("CustomerNO")->toArray())->get();
		if ($db->count()) {
			throw new Exception($db->pluck("CustomerNO")->join(",") . "分機已存在，請避開這些分機");
		}
		$insertBody2 = collect($extensions)
			->map(function ($x) use ($post) {
				return [
					"UserID" => $post["UserID"],
					"CalloutGroupID" => $post["CalloutGroupID"],
					"CustomerNO" => $x,
				];
			});
		DB::transaction(function () use ($insertBody1, $insertBody2) {
			DB::table("CustomerLists")->insert($insertBody1->toArray());
			DB::table("ExtensionGroup")->insert($insertBody2->toArray());
		});
		return true;
	}

	public function update($ctx)
	{
		["post" => $post] = $ctx;
		$updateBody = [
			"ExtName" => $post["ExtName"],
			"CustomerPwd" => $post["CustomerPwd"],
			"StartRecorder" => $post["StartRecorder"],
			"Suspend" => $post["Suspend"],
			"UseState" => $post["UseState"],
		];
		if (session("isRoot")) {
			$updateBody["OffNetCli"] = $post["OffNetCli"];
		}
		DB::transaction(function () use ($post, $updateBody) {
			DB::table("CustomerLists")
				->where("UserID", $post["UserID"])
				->where("ExtensionNo", $post["ExtensionNo"])
				->update($updateBody);
			DB::table("ExtensionGroup")
				->where("UserID", $post["UserID"])
				->where("CustomerNO", $post["ExtensionNo"])
				->update([
					"CalloutGroupID" => $post["CalloutGroupID"]
				]);
		});
		return true;
	}
}
