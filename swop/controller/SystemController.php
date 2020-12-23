<?php

use Illuminate\Database\Capsule\Manager as DB;
use lib\ReturnMessage;

class SystemController extends JController
{
	public function bulletinBoardDetail($req)
	{
		ReturnMessage::success(DB::table("BulletinBoard")->first());
	}

	public function bulletinBoardInsertOrUpdate($req)
	{
		["post" => $post] = $req;
		$modify = [
			"Content" => $post["Content"],
			"Status" => $post["Status"] ?? 0,
		];
		$db = DB::table("BulletinBoard");
		ReturnMessage::success(
			$db->count()
				? $db->update($modify)
				: $db->insert($modify)
		);
	}
}
