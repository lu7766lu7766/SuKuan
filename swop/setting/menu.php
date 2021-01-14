<?php

namespace setting;

class Menu
{
	public $currentName = "";

	static public $menus = array(
		"user_info" => array(
			"name" => "用戶資訊",
			"user_list" => array(
				"name" => "用戶列表",
				"icon" => "fas fa-user-friends",
				"url" => "userInfo/userList",
				"user" => "root",
				"sub_url" => array("userInfo/userDetail"),
				"user_must" => ["root"]
			),
			"rage_manage" => array(
				"name" => "費率管理",
				"icon" => "fas fa-money-check-alt",
				"url" => "userInfo/rateManage",
				"sub_url" => array("userInfo/userRatesModify")
			),
			"user_route" => array(
				"icon" => "fab fa-autoprefixer",
				"name" => "自動撥號路由",
				"url" => "userInfo/userRoute"
			),
			"manual_user_route" => array(
				"icon" => "fab fa-mandalorian",
				"name" => "手動撥號路由",
				"url" => "userInfo/manualUserRoute",
				"user_only" => ["root"]
			),
			"search_route" => array(
				"icon" => "fas fa-broom",
				"name" => "掃號路由",
				"url" => "userInfo/searchRoute",
				"user_only" => ["root"]
			),
			"route_search" => array(
				"icon" => "fas fa-route",
				"name" => "路由查詢",
				"url" => "userInfo/routeSearch"
			),
		),
		"extension_info" => array(
			"name" => "分機資訊",
			"add_extension" => array(
				"icon" => "fas fa-mobile-alt",
				"name" => "新增分機",
				"url" => "extensionInfo/addExtension"
			),
			"extension_manage" => array(
				"icon" => "fas fa-tasks",
				"name" => "分機管理",
				"url" => "extensionInfo/extensionManage",
				"sub_url" => array("extensionInfo/extensionModify")
			),
		),
		"communication_history" => array(
			"name" => "通聯紀錄",
			"communication_search" => array(
				"icon" => "fas fa-search",
				"name" => "通聯查詢",
				"url" => "communicationHistory/communicationSearch"
			),
			"task_ranking" => array(
				"icon" => "fas fa-sort-amount-down-alt",
				"name" => "話務排行",
				"url" => "communicationHistory/taskRanking"
			),
			"point_history" => array(
				"icon" => "fas fa-piggy-bank",
				"name" => "儲值紀錄",
				"url" => "communicationHistory/pointHistory"
			),
			"record_download" => array(
				"icon" => "fas fa-microphone-alt",
				"name" => "錄音下載",
				"url" => "communicationHistory/recordDownload"
			),
			"black_list" => array(
				"icon" => "fas fa-ban",
				"name" => "黑名單管理",
				"url" => "communicationHistory/blackList"
			)
		),
		"group_call_setting" => array(
			"name" => "群呼設定",
			"group_call_schedule" => array(
				"icon" => "fas fa-plus-circle",
				"name" => "新增群呼",
				"url" => "groupCallSetting/groupCallSchedule",
				"sub_url" => array("groupCallSetting/groupCallScheduleModify")
			)
		),
		"sys_lookout" => array(
			"name" => "系統監視",
			"call_status" => array(
				"icon" => "fas fa-assistive-listening-systems",
				"name" => "呼叫狀態",
				"url" => "sysLookout/callStatus"
				// "url" => "sysLookout/callStatus_vue"
			),
			"key_method" => array(
				"icon" => "fas fa-keyboard",
				"name" => "按鍵功能",
				"url" => "sysLookout/keyMethod"
			),
			"net_state" => array(
				"icon" => "fas fa-network-wired",
				"name" => "網路狀態",
				"url" => "sysLookout/ping"
			),
			"call_statistics" => [
				"icon" => "fas fa-id-badge",
				"name" => "使用線數統計",
				"url" => "sysLookout/callStatistics"
			]
		),
		"sys_sweep" => array(
			"name" => "掃號系統",
			"add_sweep" => array(
				"icon" => "fas fa-calendar-plus",
				"name" => "新增掃號",
				"url" => "sweepSetting/addSweep",
				"sub_url" => array("sweepSetting/addSweepModify")
			),
			"sweep_status" => array(
				"icon" => "fas fa-chalkboard-teacher",
				"name" => "掃號狀態",
				"url" => "sysSweep/sweepStatus"
			)
		),
		"ad_call_setting" => [
			"name" => "語音廣告設定",
			"voice_file_manage" => [
				"icon" => "fas fa-volume-down",
				"name" => "語音檔管理",
				"url" => "adCallSetting/voiceFileManage"
			],
			"ad_call_schedule" => [
				"icon" => "fas fa-ad",
				"name" => "新增廣告群呼",
				"url" => "adCallSetting/adCallSchedule",
				"sub_url" => ["adCallSetting/adCallScheduleModify"]
			],
			"ad_call_status" => [
				"icon" => "fas fa-map",
				"name" => "廣告呼叫狀態",
				"url" => "adCallSetting/adCallStatus"
			],
			"ad_communication_search" => [
				"icon" => "fas fa-hands-helping",
				"name" => "廣告通聯查詢",
				"url" => "adCallSetting/adCommunicationSearch"
			]
		],
		"member_info" => [
			"name" => "會員資訊",
			"member_list" => [
				"icon" => "fas fa-users",
				"name" => "會員列表",
				"url" => "memberInfo/memberList",
				"sub_url" => ["memberInfo/memberModify", "memberInfo/memberAdd"]
			]
		],
		"system" => [
			"name" => "系統設定",
			"bulletin_board" => [
				"icon" => "fas fa-clipboard-list",
				"name" => "佈告欄管理",
				"url" => "system/bulletinBoard",
			],
			"pwssword_check" => [
				"url" => "system/passwork_check",
				"hide" => true
			]
		]
	);

	public function CreateMenuManage($parentMenuList, $menuList, $userID) //checkbox清單
	{
		$this->menuList = explode(",", $menuList);
		$this->parentMenuList = explode(",", $parentMenuList);
		return "<ul style='display: inline-block;'>" . $this->getSubMenuList(Menu::$menus, $userID) . "</ul>";
	}

	private function getSubMenuList($menus, $userID)
	{
		$html = "";
		if (is_array($menus))
			foreach ($menus as $key => $val) {
				if (is_array($val) && !array_key_exists("url", $val)) {
					$tmp_html = $this->getSubMenuList($val, $userID);
				} else if (isset($val["name"])) {
					//不在user_only的要過濾掉

					if (isset($val["user_only"]) && !in_array($userID, $val["user_only"]))
						continue;
					if (!in_array($key, $this->parentMenuList) && $userID != "root")
						continue;

					$tmp_html = "<li style='float:left;margin:5px 15px 5px 10px;'><label><input type='checkbox' name='menuList[]' value='$key' " .
						(in_array($key, $this->menuList) ? "checked" : "") .
						" />" . $val["name"] . "</label></li>";
				}

				$html .= $tmp_html;
			}
		return $html;
	}

	public function CreateMenu($permission, $userID)
	{
		$menus = $this->delete_menus(Menu::$menus, explode(",", stripslashes($permission)), $userID);
		//        print_r($menus);
		return $this->getSubMenu($menus);
	}

	//刪除不再permission清單中的menu
	private function delete_menus($menus, $menu_list, $userID)
	{
		foreach ($menus as $key => $val) {
			if (is_array($val) && !array_key_exists("url", $val)) {
				$menus[$key] = $this->delete_menus($val, $menu_list, $userID);
			} else {
				if (!isset($val["user_must"]) || !in_array($userID, $val["user_must"])) {
					if (!in_array($key, $menu_list) && $key != "name") {
						unset($menus[$key]);
					}
					//不在user_only的要過濾掉
					if (isset($val["user_only"]) &&  !in_array($userID, $val["user_only"])) {
						unset($menus[$key]);
					}
				}
			}
		}
		return $menus;
	}

	private function getSubMenu($subMenu, $floor = 0)
	{

		switch ($floor) {
			case 0:
				$class = "mainmenu sidebar-nav";
				$space_li = "<li style='list-style-type: none;visibility: hidden;height:20px;'>a</li>";
				break;
			case 1:
				$class = "submenu";
				break;
			case 2:
				$class = "lastmenu";
				break;
		}
		$body = "";
		foreach ($subMenu as $key => $val) {
			if ($key == "name" || $val["hide"]) {
				continue;
			}

			if (isset($val["url"])) {
				$tmp_class = "";
				if (strpos($_SERVER['REQUEST_URI'], config("folder") . $val["url"]) !== false) {
					$this->currentName = $val["name"];
					$tmp_class = "slideactive";
				}
				if (is_array($val["sub_url"]) && empty($tmp_class)) {
					foreach ($val["sub_url"] as $sub_url) {
						if (strpos($_SERVER['REQUEST_URI'], config("folder") . $sub_url) !== false) {
							$tmp_class = "slideactive";
						}
					}
				}

				$body .= "<li>";
				$class = "lastmenu";
				$body .= "<a class='$tmp_class' href='" . config("folder") . $val["url"] . "'>" .
					(isset($val["icon"]) ? "<i class='{$val['icon']}'></i>" : "") .
					$val["name"] . "</a>";
				$body .= "</li>";
			} else {
				$body .= "<li>";
				$body .= "<a>" . $val["name"] . "</a>" . $this->getSubMenu($val, $floor + 1);
				$body .= "</li>";
			}
		}

		return "<ul class = '$class'>{$space_li}{$body}{$space_li}</ul>";
	}

	public function setModel($model)
	{
		$this->model = $model;
	}

	static public function findNameByKey($findKey, $menus)
	{
		foreach ($menus as $menu) {
			if (is_array($menu[$findKey])) return $menu[$findKey]["name"];
		}
	}

	static public function getAllMenus()
	{
		$res = [];
		foreach (Menu::$menus as $firstMenu) {
			foreach ($firstMenu as $key => $menu) {
				if ($key != "name" && $menu["hide"] !== true) {
					$res[] = [
						"value" => $key,
						"name" => $menu["name"]
					];
				}
			}
		}
		return $res;
	}
}
