<?php

use comm\Request;
use Illuminate\Database\Capsule\Manager as DB;

class SystemController extends JController
{
	public function bulletinBoardDetail(Request $request)
	{
		return DB::table("BulletinBoard")->first();
	}

	public function bulletinBoardInsertOrUpdate(Request $request)
	{
		$modify = [
			"Content" => $request->input("Content"),
			"Status" => $request->input("Status", 0),
		];
		$db = DB::table("BulletinBoard");
		return $db->count()
			? $db->update($modify)
			: $db->insert($modify);
	}
}
