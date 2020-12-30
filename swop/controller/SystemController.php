<?php

use Illuminate\Database\Capsule\Manager as DB;

class SystemController extends JController
{
	public function bulletinBoardDetail($req)
	{
		return DB::table("BulletinBoard")->first();
	}

	public function bulletinBoardInsertOrUpdate($req)
	{
		["post" => $post] = $req;
		$modify = [
			"Content" => $post["Content"],
			"Status" => $post["Status"] ?? 0,
		];
		$db = DB::table("BulletinBoard");
		return $db->count()
			? $db->update($modify)
			: $db->insert($modify);
	}
}
