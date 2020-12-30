<?php

use Illuminate\Database\Capsule\Manager as DB;
use lib\ReturnMessage;
use Rakit\Validation\Rules\Extension;

class ExtensionManageController extends JController
{
	private function buildWhere($db, $req)
	{
		["session" => $session, "post" => $post] = $req;
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
		return $db->whereIn("CustomerLists.UserID", $session["current_sub_emp"]);
	}

	public function list($req)
	{
		ReturnMessage::success(
			$this->buildWhere(DB::table("CustomerLists"), $req)
				->distinct()
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
				->leftJoin("ExtensionGroup", "CustomerLists.UserID", "=", "ExtensionGroup.UserID", "CustomerLists.ExtensionNo", "=", "ExtensionGroup.CustomerNO")
				->leftJoin("RegisteredLogs", "CustomerLists.ExtensionNo", "=", "RegisteredLogs.CustomerNO")->get()
		);
	}

	public function total($req)
	{
		ReturnMessage::success($this->buildWhere(DB::table("CustomerLists"), $req)->count());
	}

	public function delete($req)
	{
		["post" => $post] = $req;
		try {
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
			ReturnMessage::success(true);
		} catch (Extension $err) {
			ReturnMessage::error($err->getMessage());
		}
	}
}
