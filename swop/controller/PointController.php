<?php

use Illuminate\Database\Capsule\Manager as DB;
use service\PaginateService;

class PointController extends JController
{
    public function total($ctx)
    {
        $db = DB::table("RechargeLog");
        $db = $this->buildWhere($db, $ctx);
        return $db->count();
    }

    public function list($ctx)
    {
        ["post" => $post] = $ctx;
        $db = DB::table("RechargeLog");
        $db = $this->buildWhere($db, $ctx);
        $db = (new PaginateService)->proccess($db, $post["page"], $post["per_page"]);
        return $db->get();
    }

    public function buildWhere($db, $ctx)
    {
        ["post" => $post, "session" => $session] = $ctx;
        if (!empty($post["UserID"])) {
            $db->where("UserID", $post["UserID"]);
        } else {
            $db->whereIn("UserID", $session["current_sub_emp"]);
        }
        if (!empty($post["StartDate"])) {
            $db->whereRaw("cast(AddTime as datetime) > ?", [$post["StartDate"] . " 00:00:00"]);
        }
        if (!empty($post["EndDate"])) {
            $db->whereRaw("cast(AddTime as datetime) < ?", [$post["EndDate"] . " 23:59:59"]);
        }
        if (!empty($post["Memo"])) {
            $db->setParameter("Memo", "%{$post["Memo"]}%");
        }
        return $db;
    }

    public function update($ctx)
    {
        ["post" => $post] = $ctx;
        return DB::table("RechargeLog")
            ->where("LogID", $post["LogID"])
            ->update([
                "Memo" => $post["Memo"]
            ]);
    }
}
