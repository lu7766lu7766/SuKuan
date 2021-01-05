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
        $dba = $this->dba;
        $username = $this->username;
        if (empty($username)) {
            return false;
        }
        $password = $this->password;
        $password = \lib\Hash::encode($password);

        $sql = "select UserID,UserName,UserGroup,MenuList,PermissionControl,CanSwitchExtension from SysUser
                      where (UserID=? or UserID2=?) AND
                      UserPassword=?";
        $result = $dba->getAll($sql, [$username, $username, $password]);

        if (count($result)) {
            session("login", $result[0]);
            session("choicer", $result[0]);
            session("choice", $result[0]["UserID"]);
            session("isRoot", $result[0]["UserID"] == "root");
            session("permission", $result[0]["MenuList"]);
            $subEmp = $this->getSubEmp($result[0]["UserID"]);
            uasort($subEmp, function ($a, $b) {
                return $a["UserID"] <=> $b["UserID"];
            });
            session("sub_emp", $subEmp);
            session("current_sub_emp", EmpHelper::getCurrentSubEmp(
                session("sub_emp"),
                session("choice")
            ));
            session("permission_control", $result[0]["PermissionControl"]);
            return true;
        }
        return false;
    }
}
