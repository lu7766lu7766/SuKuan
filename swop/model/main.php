<?php

use Illuminate\Database\Capsule\Manager as DB;

class Main_Model extends JModel
{
    public function main()
    {
        $this->bulletinBoard = DB::table("BulletinBoard")->first();
        return $this;
    }

    public function login_in()
    {
        $db = DB::table("SysUser")
            ->where(function ($db) {
                return $db->where("UserID", request()->input("username"))->orWhere("UserID2", request()->input("username"));
            });
        if (!isDev()) {
            $db->where("UserPassword", \lib\Hash::encode(request()->input("password")));
        }
        $result = $db->first();
        if ($result) {
            $user = \lib\ArrayUtils::toArray($result);
            session("login", $user);
            session("choicer", $user);
            session("choice", $user["UserID"]);
            session("isRoot", $user["UserID"] == "root");
            session("permission", $user["MenuList"]);
            session("sub_emp", collect($this->getSubEmp($user["UserID"]))->sortBy("UserID")->toArray());
            session("current_sub_emp", EmpHelper::getCurrentSubEmp(
                session("sub_emp"),
                session("choice")
            ));
            session("permission_control", $user["PermissionControl"]);
            return true;
        }
        return false;
    }
}
