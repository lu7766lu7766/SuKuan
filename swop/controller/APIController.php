<?php

use Illuminate\Database\Capsule\Manager as DB;

/**
 * Created by PhpStorm.
 * User: lu7766
 * Date: 2017/6/18
 * Time: 下午8:30
 */
class APIController extends JController
{

    public function getTaskRankingList($ctx)
    {
        $selectRaw = DB::raw("
            sum(cast(CallOutCDR.CallDuration as float)) as CallDuration,
            COUNT(1) as Count,
            sum(cast(BillValue as float)) as BillValue,
            sum(cast(BillCost as float)) as BillCost");
        $query = DB::table("CallOutCDR")
            ->leftJoin("SysUser", "CallOutCDR.UserID", "=", "SysUser.UserID")
            ->orderBy("CallOutCDR.UserID");
        if (!empty($ctx["post"]["userID"])) {
            $query->where("CallOutCDR.UserID", $ctx["post"]["userID"]);
        } else {
            $query->whereIn("CallOutCDR.UserID", $ctx["session"]["current_sub_emp"]);
        }
        if (!empty($ctx["post"]["callStopBillingDate"])) {
            $query->whereRaw("cast((CallOutCDR.CallStartBillingDate+' '+CallOutCDR.CallStartBillingTime) as datetime) < ?", [$ctx["post"]["callStopBillingDate"] . "  23:59:59"]);
        }
        if (!empty($ctx["post"]["callStartBillingDate"])) {
            $query->whereRaw("cast((CallOutCDR.CallStopBillingDate+' '+CallOutCDR.CallStopBillingTime) as datetime) > ?", [$ctx["post"]["callStartBillingDate"] . " 00:00:00"]);
        }

        switch ($ctx["post"]["display_mode"]) {
            case "0":
                return $query
                    ->select("CallOutCDR.UserID", "CallOutCDR.ExtensionNo", "SysUser.UserName", $selectRaw)
                    ->groupBy("CallOutCDR.UserID", "CallOutCDR.ExtensionNo", "SysUser.UserName")
                    ->get();
                break;
            case "1":
                return $query
                    ->select("CallOutCDR.UserID", "SysUser.UserName", $selectRaw)
                    ->groupBy("CallOutCDR.UserID", "SysUser.UserName")
                    ->get();
                break;
        }
    }
}
