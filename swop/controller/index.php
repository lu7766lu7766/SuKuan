<?php

use comm\Session;
use Illuminate\Database\Capsule\Manager as DB;

class Index_Controller extends JController
{
    public function index()
    {
        return parent::render();
    }

    public function service()
    {
        return parent::render();
    }

    public function password()
    {
        return parent::render();
    }

    public function logout()
    {
        Session::clear();
        $this->redirect("main/main");
    }

    public function changeUser()
    {
        $user = json_decode(json_encode(DB::table("SysUser")->where("UserID", $this->model->choice)->first()), true);
        session("choicer", $user);
        session("permission", $user["MenuList"]);
        session("choice", $this->model->choice);
        session("isRoot", session("choice") == "root");
        session("sub_emp", $this->model->getSubEmp(session("login")["UserID"]));
        session("current_sub_emp", EmpHelper::getCurrentSubEmp(
            session("sub_emp"),
            session("choice")
        ));
        session("permission_control", session("choice") == session("login")["UserID"]
            ? session("login")["PermissionControl"]
            : EmpHelper::getPermissionControl(session("sub_emp"), session("choice")));
    }

    public function update_pwd()
    {
        if ($this->model->result) {
            echo "success";
        } else {
            echo "error";
        }
    }

    /**
     * 關機
     */
    public function shotdown()
    {
        $url = "http://127.0.0.1:60/SHUTDOWN.atp";
        comm\Http::get($url);
        $this->redirect("main/main");
    }

    /**
     * 重啟
     */
    public function reboot()
    {
        $url = "http://127.0.0.1:60/REBOOT.atp";
        comm\Http::get($url);
        $this->redirect("main/main");
    }

    public function test()
    {
        //        echo "<pre>";
        //        print_r($this->model->data);
    }
}
