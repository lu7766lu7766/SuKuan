<?php

use comm\Request;
use \lib\ReturnMessage;
use Illuminate\Database\Capsule\Manager as DB;
use Rakit\Validation\Validator;
use repostory\UserRepostory;
use setting\Menu;
use Tightenco\Collect\Support\Collection;

class UserController extends JController
{
	public function echoPassword(Request $request)
	{
		return ReturnMessage::success([
			"user" => $request->input("user"),
			"password" => \lib\Hash::encode($request->input("password"))
		], JSON_UNESCAPED_SLASHES);
	}

	/**
	 * 使用者列表
	 */
	public function list(Request $request)
	{
		$fields = [
			"SysUser.UserID",
			"SysUser.UseState",
			"SysUser.RateGroupID",
			"RateGroup.RateGroupName",
			"SysUser.Balance",
			"SysUser.UserName",
			"SysUser.Distributor",
			"SysUser.NoteText"
		];
		$db = DB::table("SysUser")
			->select(
				...collect($fields)
					->push(
						DB::raw("count(1) as ExtensionCount")
					)
					->toArray()
			)
			->leftJoin("RateGroup", "RateGroup.RateGroupID", "=", "SysUser.RateGroupID")
			->leftJoin("CustomerLists", "CustomerLists.UserID", "=", "SysUser.UserID")
			->groupBy(...$fields);
		if (!session("isRoot")) {
			$db = $db->whereIn("SysUser.UserID", session("current_sub_emp"));
		}
		return $db->get();
	}

	/**
	 * delete users
	 */
	public function delete(Request $request)
	{
		return DB::table("SysUser")
			->whereIn("UserID", $request->input("deleteUserID"))
			->delete();
	}

	public function detail(Request $request)
	{
		$userRepostory = new UserRepostory();
		$user = $userRepostory->getDetail($request->input("userID"));
		$user->UserPassword = \lib\Hash::decode($user->UserPassword);
		$user->MenuList = explode(",", $user->MenuList);

		return $user;
	}

	public function create(Request $request)
	{
		$this->validate($request);
		$repo = new UserRepostory();
		if ($repo->checkExists([$request->input("UserID")])) {
			throw new Exception("登入帳號已存在，無法新增。");
		} else {
			DB::transaction(function () use ($repo, $request) {
				$repo->create(
					$request->input("UserID"),
					$request->input("UseState") ? 1 : 0,
					$request->input("UserName"),
					$request->input("NoteText"),
					$request->input("RateGroupID"),
					$request->input("AddBalance"),
					$request->input("StartTime"),
					$request->input("StopTime"),
					$request->input("CallWaitingTime"),
					$request->input("ParentID"),
					join(",", $request->input("MenuList")),
					$request->input("MaxRoutingCalls"),
					$request->input("MaxCalls"),
					$request->input("UserInfo"),
					$request->input("Distributor"),
					$request->input("AutoStartTime"),
					$request->input("AutoStopTime"),
					$request->input("Overdraft"),
					$request->input("SearchStartTime"),
					$request->input("SearchAutoStartTime"),
					$request->input("SearchAutoStopTime"),
					$request->input("SearchStopTime"),
					$request->input("MaxSearchCalls"),
					$request->input("MaxRegularCalls"),
					$request->input("PermissionControl") ? 1 : 0,
					$request->input("UserID2"),
					$request->input("CanSwitchExtension") ? 1 : 0
				);
				if ($request->input("AddBalance") && $request->input("AddBalance") != 0) {
					$repo->createChargeLog($request->input("UserID"), $request->input("AddBalance"), date("Y-m-d H:i:s", time()), session("choice"));
				}
			});
			return true;
		}
	}

	public function createBatch(Request $request)
	{
		$repo = new UserRepostory();
		if ($repo->checkExists(array_column($request->input("datas"), "UserID"))) {
			throw new Exception("帳號有重複，無法新增。");
		}
		if (count($request->input("datas")) > 100) {
			throw new Exception("新增比數超過100筆，請減少筆數。");
		}

		DB::transaction(function () use ($request) {
			DB::table("SysUser")->insert(
				collect($request->input("datas"))->map(function ($x) {
					return [
						"UserID" => $x["UserID"],
						"UserName" => $x["UserName"],
						"UseState" => $x["UseState"],
						"RateGroupID" => $x["RateGroupID"],
						"Balance" => $x["Balance"],
						"NoteText" => $x["NoteText"],
						"ParentID" => session("choice"),
						"StartTime" => "08:00",
						"StopTime" => "21:00"
					];
				})->toArray()
			);
			DB::table("RechargeLog")->insert(collect($request->input("datas"))->map(function ($x) {
				return [
					"UserID" => $x["UserID"],
					"AddValue" => $x["Balance"],
					"AddTime" => date("Y-m-d H:i:s", time()),
					"SaveUserID" => session("choice")
				];
			})->toArray());
		});
		return true;
	}

	public function update(Request $request)
	{
		$this->validate($request);
		$repo = new UserRepostory();
		if (session("isRoot")) {
			DB::transaction(function () use ($repo, $request) {
				$repo->update(
					$request->input("UserID"),
					$request->input("UseState") ? 1 : 0,
					$request->input("UserName"),
					$request->input("NoteText"),
					$request->input("RateGroupID"),
					floor($request->input("Balance")) + floor($request->input("AddBalance")),
					$request->input("StartTime"),
					$request->input("StopTime"),
					$request->input("CallWaitingTime"),
					$request->input("ParentID"),
					join(",", $request->input("MenuList")),
					$request->input("MaxRoutingCalls"),
					$request->input("MaxCalls"),
					$request->input("UserInfo"),
					$request->input("Distributor"),
					$request->input("AutoStartTime"),
					$request->input("AutoStopTime"),
					$request->input("Overdraft"),
					$request->input("SearchStartTime"),
					$request->input("SearchAutoStartTime"),
					$request->input("SearchAutoStopTime"),
					$request->input("SearchStopTime"),
					$request->input("MaxSearchCalls"),
					$request->input("MaxRegularCalls"),
					$request->input("PermissionControl") ? 1 : 0,
					$request->input("UserID2"),
					$request->input("CanSwitchExtension") ? 1 : 0
				);
				if ($request->input("AddBalance") && $request->input("AddBalance") != 0) {
					$repo->createChargeLog($request->input("UserID"), $request->input("AddBalance"), date("Y-m-d H:i:s", time()), session("choice"));
				}
			});
		} else {
			$repo->updateMenuList($request->input("UserID"), join(",", $request->input("MenuList")));
		}
		return true;
	}

	private function validate($request)
	{
		$validator = new Validator();
		$validation = $validator->validate($request->all(), [
			'UserID'                 => 'required',
			// 'StartTime'             => 'required',
			// 'StopTime'              => 'required',
		]);
		if ($validation->fails()) {
			throw new Exception("驗證失敗，請確認欄位");
		}
	}

	public function menus(Request $request)
	{
		if (session("isRoot")) {
			return Menu::getAllMenus();
		} else if (session("choice") == session("login")["UserID"]) {
			$result = session("login")["MenuList"];
		} else {
			$result = EmpHelper::getMenuList(session("sub_emp"), session("choice"));
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
