<?php

use Illuminate\Database\Capsule\Manager as DB;
use lib\ReturnMessage;
use Rakit\Validation\Validator;

class ManualUserRouteController extends JController
{
	public function list($req)
	{
		$db = DB::table("UserRouting");
		["session" => $session] = $req;
		if (!$session["isRoot"]) {
			$db->where("UserID", $session["choice"]);
		}
		ReturnMessage::success($db->get());
	}

	private function validate($post)
	{
		$validator = new Validator();
		$validation = $validator->validate($post, [
			'UserID'                 => 'required',
			'PrefixCode'             => 'required',
			'TrunkPort'              => 'required|min:1|max:65535',
		]);
		if ($validation->fails()) {
			ReturnMessage::error("驗證失敗，請確認欄位");
		}
	}

	public function create($req)
	{
		["post" => $post] = $req;
		$this->validate($post);

		if (
			DB::table("AsRouting")->where("UserID", $post["UserID"])->where("PrefixCode", $post["PrefixCode"])->count()
		) {
			ReturnMessage::error("用戶與前置碼重複，無法新增。");
		}
		ReturnMessage::success(
			DB::table("UserRouting")->insert([
				"UserID" => $post["UserID"],
				"PrefixCode" => $post["PrefixCode"],
				"AddPrefix" => $post["AddPrefix"],
				"RouteCLI" => $post["RouteCLI"],
				"TrunkIP" => $post["TrunkIP"],
				"TrunkPort" => $post["TrunkIP"],
				"RouteName" => $post["RouteName"],
				"SubNum" => $post["SubNum"]
			])
		);
	}

	public function update($req)
	{
		["post" => $post] = $req;
		$this->validate($post);
		
		ReturnMessage::success(
			DB::table("UserRouting")
				->where("UserID", $post["UserID"])
				->where("PrefixCode", $post["PrefixCode"])
				->update([
					"AddPrefix" => $post["AddPrefix"],
					"RouteCLI" => $post["RouteCLI"],
					"TrunkIP" => $post["TrunkIP"],
					"TrunkPort" => $post["TrunkIP"],
					"RouteName" => $post["RouteName"],
					"SubNum" => $post["SubNum"]
				])
		);
	}

	public function delete($req)
	{
		["post" => $post] = $req;
		ReturnMessage::success(
			DB::table("UserRouting")
				->where("UserID", $post["UserID"])
				->where("PrefixCode", $post["PrefixCode"])
				->delete()
		);
	}
}
