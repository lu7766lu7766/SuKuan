<?php

use \lib\ReturnMessage;
use Illuminate\Database\Capsule\Manager as DB;
use Rakit\Validation\Validator;
use repostory\UserRepostory;
use setting\Menu;
use Tightenco\Collect\Support\Collection;

class UserController extends JController
{
	public function echoPassword($req)
	{
		$get = $req["get"];
		$user = $get["user"];
		$password = \lib\Hash::encode($get["password"]);

		echo ReturnMessage::success([
			"user" => $user,
			"password" => $password
		], JSON_UNESCAPED_SLASHES);
	}

	/**
	 * 使用者列表
	 */
	public function list($req)
	{
		["session" => $session] = $req;
		$db = DB::table("SysUser")
			->select(
				"UserID",
				"UseState",
				"RateGroupID",
				"Balance",
				"UserName",
				"Distributor",
				"NoteText",
				DB::raw("(select count(1) from CustomerLists where UserID = SysUser.UserID) as ExtensionCount")
			);
		if (!$session["isRoot"]) {
			$db = $db->whereIn("UserID", $req["session"]["current_sub_emp"]);
		}
		return $db->get();
	}

	/**
	 * delete users
	 */
	public function delete($req)
	{
		return DB::table("SysUser")
			->whereIn("UserID", $req["post"]["deleteUserID"])
			->delete();
	}

	public function detail($req)
	{
		$userRepostory = new UserRepostory();
		$user = $userRepostory->getDetail($req["post"]["userID"]);
		$user->UserPassword = \lib\Hash::decode($user->UserPassword);
		$user->MenuList = explode(",", $user->MenuList);

		return $user;
	}

	public function create($req)
	{
		["post" => $post, "session" => $session] = $req;
		$this->validate($post);
		$repo = new UserRepostory();
		if ($repo->checkExists([$post["UserID"]])) {
			throw new Exception("登入帳號已存在，無法新增。");
		} else {
			DB::transaction(function () use ($repo, $post, $session) {
				$repo->create(
					$post["UserID"],
					$post["UseState"] ? 1 : 0,
					$post["UserName"],
					$post["NoteText"],
					$post["RateGroupID"],
					$post["AddBalance"],
					$post["StartTime"],
					$post["StopTime"],
					$post["CallWaitingTime"],
					$post["ParentID"],
					join(",", $post["MenuList"]),
					$post["MaxRoutingCalls"],
					$post["MaxCalls"],
					$post["UserInfo"],
					$post["Distributor"],
					$post["AutoStartTime"],
					$post["AutoStopTime"],
					$post["Overdraft"],
					$post["SearchStartTime"],
					$post["SearchAutoStartTime"],
					$post["SearchAutoStopTime"],
					$post["SearchStopTime"],
					$post["MaxSearchCalls"],
					$post["MaxRegularCalls"],
					$post["PermissionControl"] ? 1 : 0,
					$post["UserID2"],
					$post["CanSwitchExtension"] ? 1 : 0
				);
				if ($post["AddBalance"] && $post["AddBalance"] != 0) {
					$repo->createChargeLog($post["UserID"], $post["AddBalance"], date("Y-m-d H:i:s", time()), $session["choice"]);
				}
			});
			return true;
		}
	}

	public function createBatch($req)
	{
		["post" => $post, "session" => $session] = $req;
		$repo = new UserRepostory();
		if ($repo->checkExists(array_column($post["datas"], "UserID"))) {
			throw new Exception("帳號有重複，無法新增。");
		}
		if (count($post["datas"]) > 100) {
			throw new Exception("新增比數超過100筆，請減少筆數。");
		}

		DB::transaction(function () use ($post, $session) {
			DB::table("SysUser")->insert(
				collect($post["datas"])->map(function ($x) use ($session) {
					return [
						"UserID" => $x["UserID"],
						"UserName" => $x["UserName"],
						"UseState" => $x["UseState"],
						"RateGroupID" => $x["RateGroupID"],
						"Balance" => $x["Balance"],
						"NoteText" => $x["NoteText"],
						"ParentID" => $session["choice"],
						"StartTime" => "08:00",
						"StopTime" => "21:00"
					];
				})->toArray()
			);
			DB::table("RechargeLog")->insert(collect($post["datas"])->map(function ($x) use ($session) {
				return [
					"UserID" => $x["UserID"],
					"AddValue" => $x["Balance"],
					"AddTime" => date("Y-m-d H:i:s", time()),
					"SaveUserID" => $session["choice"]
				];
			})->toArray());
		});
		return true;
	}

	public function update($req)
	{
		["post" => $post, "session" => $session] = $req;
		$this->validate($post);
		$repo = new UserRepostory();
		if ($session["isRoot"]) {
			DB::transaction(function () use ($repo, $post, $session) {
				$repo->update(
					$post["UserID"],
					$post["UseState"] ? 1 : 0,
					$post["UserName"],
					$post["NoteText"],
					$post["RateGroupID"],
					floor($post["Balance"]) + floor($post["AddBalance"]),
					$post["StartTime"],
					$post["StopTime"],
					$post["CallWaitingTime"],
					$post["ParentID"],
					join(",", $post["MenuList"]),
					$post["MaxRoutingCalls"],
					$post["MaxCalls"],
					$post["UserInfo"],
					$post["Distributor"],
					$post["AutoStartTime"],
					$post["AutoStopTime"],
					$post["Overdraft"],
					$post["SearchStartTime"],
					$post["SearchAutoStartTime"],
					$post["SearchAutoStopTime"],
					$post["SearchStopTime"],
					$post["MaxSearchCalls"],
					$post["MaxRegularCalls"],
					$post["PermissionControl"] ? 1 : 0,
					$post["UserID2"],
					$post["CanSwitchExtension"] ? 1 : 0
				);
				if ($post["AddBalance"] && $post["AddBalance"] != 0) {
					$repo->createChargeLog($post["UserID"], $post["AddBalance"], date("Y-m-d H:i:s", time()), $session["choice"]);
				}
			});
		} else {
			$repo->updateMenuList($post["UserID"], join(",", $post["MenuList"]));
		}
		return true;
	}

	private function validate($post)
	{
		$validator = new Validator();
		$validation = $validator->validate($post, [
			'UserID'                 => 'required',
			// 'StartTime'             => 'required',
			// 'StopTime'              => 'required',
		]);
		if ($validation->fails()) {
			throw new Exception("驗證失敗，請確認欄位");
		}
	}

	public function menus($req)
	{
		["session" => $session] = $req;
		if ($session["isRoot"]) {
			return Menu::getAllMenus();
		} else if ($session["choice"] == $session["login"]["UserID"]) {
			$result = $session["login"]["MenuList"];
		} else {
			$result = EmpHelper::getMenuList($session["sub_emp"], $session["choice"]);
		}

		return
			collect(explode(",", $result))
			->map(function ($key) {
				return [
					"value" => $key,
					"name" => Menu::findNameByKey($key, Menu::$menus)
				];
			})
			->filter(function ($x) {
				return $x["name"];
			});
	}
}
