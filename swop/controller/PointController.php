<?php

use comm\Request;
use Illuminate\Database\Capsule\Manager as DB;
use service\PaginateService;

class PointController extends JController
{
    public function total(Request $request)
    {
        $db = DB::table("RechargeLog");
        $db = $this->buildWhere($db, $request);
        return $db->count();
    }

    public function list(Request $request)
    {
        $db = DB::table("RechargeLog");
        $db = $this->buildWhere($db, $request);
        $db = (new PaginateService)->proccess($db, $request->input("page"), $request->input("per_page"));
        return $db->orderBy("AddTime", "desc")->get();
    }

    public function buildWhere($db, $request)
    {
        if (!empty($request->input("UserID"))) {
            $db->where("UserID", $request->input("UserID"));
        } else {
            $db->whereIn("UserID", session("current_sub_emp"));
        }
        if (!empty($request->input("StartDate"))) {
            $db->whereRaw("cast(AddTime as datetime) > ?", [$request->input("StartDate") . " 00:00:00"]);
        }
        if (!empty($request->input("EndDate"))) {
            $db->whereRaw("cast(AddTime as datetime) < ?", [$request->input("EndDate") . " 23:59:59"]);
        }
        if (!empty($request->input("Memo"))) {
            $db->setParameter("Memo", "%{$request->input("Memo")}%");
        }
        return $db;
    }

    public function update(Request $request)
    {
        return DB::table("RechargeLog")
            ->where("LogID", $request->input("LogID"))
            ->update([
                "Memo" => $request->input("Memo")
            ]);
    }
}
