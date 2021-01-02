<?php

use Illuminate\Database\Capsule\Manager as DB;

class SystemController extends JController
{
	public function bulletinBoardDetail($ctx)
	{
		return DB::table("BulletinBoard")->first();
	}

	public function bulletinBoardInsertOrUpdate($ctx)
	{
		["post" => $post] = $ctx;
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
