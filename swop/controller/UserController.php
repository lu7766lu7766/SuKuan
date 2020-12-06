<?php

use \lib\ReturnMessage;
use Illuminate\Database\Capsule\Manager as DB;

class UserController extends JController
{
    public function create($req)
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
}
