<?php

use comm\Request;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Created by PhpStorm.
 * User: lu7766
 * Date: 2017/6/18
 * Time: 下午8:30
 */
class APIController extends JController
{

    public function getTaskRankingList(Request $request)
    {
        $selectRaw = DB::raw("
            sum(cast(CallOutCDR.CallDuration as float)) as CallDuration,
            COUNT(1) as Count,
            sum(cast(BillValue as float)) as BillValue,
            sum(cast(BillCost as float)) as BillCost");
        $query = DB::table("CallOutCDR")
            ->leftJoin("SysUser", "CallOutCDR.UserID", "=", "SysUser.UserID")
            ->orderBy("CallOutCDR.UserID");
        if (!empty($request->input("userID"))) {
            $query->where("CallOutCDR.UserID", $request->input("userID"));
        } else {
            $query->whereIn("CallOutCDR.UserID", session("current_sub_emp"));
        }
        if (!empty($request->input("callStopBillingDate"))) {
            $query->whereRaw("cast((CallOutCDR.CallStartBillingDate+' '+CallOutCDR.CallStartBillingTime) as datetime) < ?", [$request->input("callStopBillingDate") . "  23:59:59"]);
        }
        if (!empty($request->input("callStartBillingDate"))) {
            $query->whereRaw("cast((CallOutCDR.CallStopBillingDate+' '+CallOutCDR.CallStopBillingTime) as datetime) > ?", [$request->input("callStartBillingDate") . " 00:00:00"]);
        }

        switch ($request->input("display_mode")) {
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

    public function getDailyReportList(Request $request)
    {
        $query = DB::table("CallOutCDR")
            ->leftJoin("SysUser", "CallOutCDR.UserID", "=", "SysUser.UserID")
            ->where("CallOutCDR.UserID", $request->input("userID"));
            
        
        if (!empty($request->input("callStopBillingDate"))) {
            $query->where("CallOutCDR.CallStartBillingDate", "<=" , $request->input("callStopBillingDate"));
        }
        if (!empty($request->input("callStartBillingDate"))) {
            $query->where("CallOutCDR.CallStopBillingDate", ">=", $request->input("callStartBillingDate"));
        }

        return $query
            ->select("CallOutCDR.CallStopBillingDate as Date", "CallOutCDR.UserID", "SysUser.UserName", DB::raw("
                sum(cast(CallOutCDR.CallDuration as float)) as CallDuration,
                COUNT(1) as Count,
                sum(cast(BillValue as float)) as BillValue,
                sum(cast(BillCost as float)) as BillCost"))
            ->groupBy("CallOutCDR.CallStopBillingDate", "CallOutCDR.UserID", "SysUser.UserName")
            ->orderBy("CallOutCDR.CallStopBillingDate")
            ->get();
        
    }
}
