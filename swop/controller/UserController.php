<?php

use \lib\ReturnMessage;
use Illuminate\Database\Capsule\Manager as DB;
use repostory\UserRepostory;
use setting\Menu2;
use Tightenco\Collect\Support\Collection;

class UserController extends JController
{
	public function echoPassword($req)
	{
		$get = $req["get"];
		$user = $get["user"];
		$password = \lib\Hash::encode($get["password"]);

		ReturnMessage::success([
			"user" => $user,
			"password" => $password
		], JSON_UNESCAPED_SLASHES);
		// ReturnMessage::success(DB::table('SysUser')->limit(1)->get());
	}

	/**
	 * 使用者列表
	 */
	public function list($req)
	{
		ReturnMessage::success(
			DB::table("SysUser")
				->select(
					"UserID",
					"UseState",
					"RateGroupID",
					"Balance",
					"UserName",
					"Distributor",
					"NoteText",
					DB::raw("(select count(1) from CustomerLists where UserID = SysUser.UserID) as ExtensionCount")
				)
				->whereIn("UserID", $req["session"]["current_sub_emp"])
				->get()
		);
	}

	/**
	 * delete users
	 */
	public function delete($req)
	{
		ReturnMessage::success(
			DB::table("SysUser")
				->whereIn("UserID", $req["post"]["deleteUserID"])
				->delete()
		);
	}

	public function detail($req)
	{
		$userRepostory = new UserRepostory();
		$user = $userRepostory->getDetail($req["post"]["userID"]);
		$user->UserPassword = \lib\Hash::decode($user->UserPassword);
		$user->MenuList = explode(",", $user->MenuList);

		ReturnMessage::success(
			$user
		);
	}

	public function create($req)
	{
		$repo = new UserRepostory();
		$post = $req["post"];
		$session = $req["session"];
		if ($repo->checkExists([$post["UserID"]])) {
			ReturnMessage::error("登入帳號已存在，無法新增。");
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
			ReturnMessage::success(true);
		}
	}

	public function createBatch($req)
	{
		["post" => $post, "session" => $session] = $req;
		$repo = new UserRepostory();
		if ($repo->checkExists(array_column($post["datas"], "UserID"))) {
			ReturnMessage::error("帳號有重複，無法新增。");
		} else {
			try {
				DB::transaction(function () use ($post, $session) {
					DB::table("SysUser")->insert(
						Collection($post["datas"])->map(function ($x) {
							return [
								"UserID" => $x["UserID"],
								"UserName" => $x["UserName"],
								"UseState" => $x["UseState"],
								"RateGroupID" => $x["RateGroupID"],
								"Balance" => $x["Balance"],
								"NoteText" => $x["NoteText"],
							];
						})->toArray()
					);
					DB::table("RechargeLog")->insert(Collection($post["datas"])->map(function ($x) use ($session) {
						return [
							"UserID" => $x["UserID"],
							"AddValue" => $x["Balance"],
							"AddTime" => date("Y-m-d H:i:s", time()),
							"SaveUserID" => $session["choice"]
						];
					})->toArray());
				});
				ReturnMessage::success(true);
			} catch (Exception $err) {
				ReturnMessage::error($err->getMessage());
			}
		}
	}

	public function update($req)
	{
		$repo = new UserRepostory();
		$post = $req["post"];
		$session = $req["session"];
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
				$userInfoModel = new UserInfo_Model($this->base);
				$userInfoModel->setUpdatePermission($post["UserID"], join(",", $post["MenuList"]), $post["PermissionControl"] ? 1 : 0);
			});
		} else {
			$repo->updateMenuList($post["UserID"], join(",", $post["MenuList"]));
		}
		ReturnMessage::success(true);
	}

	public function menus($req)
	{
		if ($req["session"]["isRoot"]) {
			ReturnMessage::success(Menu2::getAllMenus());
		} else if ($req["session"]["choice"] == $req["session"]["login"]["UserID"]) {
			$result = $req["session"]["login"]["MenuList"];
		} else {
			$result = EmpHelper::getMenuList($req["session"]["sub_emp"], $req["session"]["choice"]);
		}

		ReturnMessage::success(
			Collection(explode(",", $result))
				->map(function ($key) {
					return [
						"value" => $key,
						"name" => Menu2::findNameByKey($key, Menu2::$menus)
					];
				})
				->filter(function ($x) {
					return $x["name"];
				})
		);
	}
}
