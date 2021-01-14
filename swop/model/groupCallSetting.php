<?php

use Illuminate\Database\Capsule\Manager as DB;
use lib\ReturnMessage;

class GroupCallSetting_Model extends JModel
{
    public function __construct()
    {
        parent::__construct();
        // 名單數限制
        $this->phone_limit = getenv2("GROUP_CALL_PHONE_LIMIT", 200000);
        // 排程數限制
        $this->list_limit = getenv2("GROUP_CALL_LIST_LIMIT", 99999);
    }

    public function postDefaultSchedule($list)
    {
        $list = is_string($list) ? explode(",", $list) : $list;
        $len = count($list);
        $callOutID = DB::table('CallPlan')->select('max(CallOutID)+1 as count')->first()->count; //確保寫入numberList的calloutid是唯一值
        $callOutID = empty($callOutID) ? "1" : $callOutID;
        DB::table('CallPlan')->insert([
            'UserID'            => session("choice"), 
            'PlanName'          => $list[0],
            'StartCalledNumber' => $list[0],
            'CalledCount'       => $len,
            'CallerPresent'     => 1,
            'CallerID'          => '',
            'CalloutGroupID'    => 1, //座群
            'Calldistribution'  => 1, //自動均配
            'CallProgressTime'  => 20, //撥出電話等待秒數
            'ExtProgressTime'   => 15, //轉分機等待秒數
            'UseState'          => 0, //$this->useState??
            'ConcurrentCalls'   => 10, //自動撥號速度
            'NumberMode'        => 1, //名單上傳
            'CallOutID'         => $callOutID
        ]);
        while ($len-- > 0) {
            DB::table('NumberList')->insert([
                'CallOutID'    => $callOutID,
                'CalledNumber' => array_shift($list)
            ]);
        }
    }
}
