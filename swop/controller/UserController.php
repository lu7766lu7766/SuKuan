<?php

use \lib\ReturnMessage;
use Illuminate\Database\Capsule\Manager as DB;

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
}
