<?php

use Illuminate\Database\Capsule\Manager as DB;
use lib\ReturnMessage;
use service\PaginateService;

class PointController extends JController
{
    public function total($req)
    {
        $db = DB::table("RechargeLog");
        $db = $this->buildWhere($db, $req);
        ReturnMessage::success($db->count());
    }

    public function list($req)
    {
        ["post" => $post] = $req;
        $db = DB::table("RechargeLog");
        $db = $this->buildWhere($db, $req);
        $db = (new PaginateService)->proccess($db, $post["page"], $post["per_page"]);
        ReturnMessage::success($db->get());
    }

    public function buildWhere($db, $req)
    {
        ["post" => $post, "session" => $session] = $req;
        if (!empty($post["UserID"])) {
            $db->where("UserID", $post["UserID"]);
        } else {
            $db->whereIn("UserID", $session["current_sub_emp"]);
        }
        if (!empty($post["StartDate"])) {
            $db->whereRaw("cast(AddTime as datetime) > ?", [$post["StartDate"]. " 00:00:00"]);
        }
        if (!empty($post["EndDate"])) {
            $db->whereRaw("cast(AddTime as datetime) < ?", [$post["EndDate"]. " 23:59:59"]);
        }
        if (!empty($post["Memo"])) {
            $db->setParameter("Memo", "%{$post["Memo"]}%");
        }
        return $db;
    }

    public function update($req)
    {
        ["post" => $post] = $req;
        ReturnMessage::success(DB::table("RechargeLog")
            ->where("LogID", $post["LogID"])
            ->update([
                "Memo" => $post["Memo"]
            ]));
    }
}
