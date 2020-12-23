<?php

use comm\DB;
use comm\DBA;

class GroupCallSetting_Model extends JModel
{
    public function __construct($base)
    {
        parent::__construct($base);
        // 名單數限制
        $this->phone_limit = getenv2("GROUP_CALL_PHONE_LIMIT", 200000);
        // 排程數限制
        $this->list_limit = getenv2("GROUP_CALL_LIST_LIMIT", 99999);
    }

    private function getCallPlanCount() {
        return DB::table('CallPlan')
            ->select('count(1) as count')
            ->where('UserID', $this->session["choice"])
            ->get()[0]["count"];
    }

    public function getEffectiveNumber()
    {
        $this->searchNumber = preg_replace("/\?/", "_", $this->searchNumber);
//        if($this->countryCode==0) // taiwan
//        {
//            $result = $this->dba->getAll("select DISTINCT OrgCalledId as number from CalloutCDR with (nolock) where OrgCalledId like ? and CallBill='1';",
//                [$this->searchNumber]);
//        }
//        else if($this->countryCode==1)//china
//        {
//        }
        $dba2 = new DBA();
        $dba2->dbHost = "125.227.84.247";
        $dba2->dbName = "NumberCollector";
        $dba2->connect();
        $result = $dba2->getAll("select DISTINCT CalledNumber as number from AllNumberList with (nolock) where CalledNumber like ? and CallResult in ('2','3');",
            [$this->searchNumber]);
        if (count($result) > $this->phone_limit) {
            echo json_encode(["len" => -1, "status" => -1,]);
            return;
        }
        $limit = 5000;
        $list = [];
        foreach ($result as $item) {
            $list[] = $item["number"];
        }
        $len = ceil(count($list) / $limit);
        $i = 0;
        while ($i < $len) {
            $this->postDefaultSchedule(array_slice($list, $i * $limit, $limit));
            $i++;
        }
        echo json_encode(["len" => count($result), "status" => 0]);
    }

    public function postDefaultSchedule($list)
    {
        $list = is_string($list) ? explode(",", $list) : $list;
        $len = count($list);
        $callOutID = DB::table('CallPlan')->select('max(CallOutID)+1 as count')->get()[0]["count"];//確保寫入numberList的calloutid是唯一值
        $callOutID = empty($callOutID) ? "1" : $callOutID;
        DB::table('CallPlan')->insert([
            'UserID'            => $this->session["choice"],
            'PlanName'          => $list[0],
            'StartCalledNumber' => $list[0],
            'CalledCount'       => $len,
            'CallerPresent'     => 1,
            'CallerID'          => '',
            'CalloutGroupID'    => 1, //座群
            'Calldistribution'  => 1,//自動均配
            'CallProgressTime'  => 20,//撥出電話等待秒數
            'ExtProgressTime'   => 15,//轉分機等待秒數
            'UseState'          => 0,//$this->useState??
            'ConcurrentCalls'   => 10,//自動撥號速度
            'NumberMode'        => 1,//名單上傳
            'CallOutID'         => $callOutID
        ])->exec();
        while ($len-- > 0) {
            DB::table('NumberList')->insert([
                'CallOutID'    => $callOutID,
                'CalledNumber' => array_shift($list)
            ])->exec();
        }
    }

    public function getEffectiveNumber2()
    {
        $this->searchNumber = preg_replace("/\?/", "_", $this->searchNumber);
        if ($this->countryCode == "0") // taiwan
        {
            $result = $this->dba->getAll("select DISTINCT OrgCalledId as number from CalloutCDR with (nolock) where OrgCalledId like ? and CallBill='1';",
                [$this->searchNumber]);
        } else {
            if ($this->countryCode == "1")//china
            {
                $dba2 = new DBA();
                $dba2->dbHost = "125.227.84.247";
                $dba2->dbName = "NumberCollector";
                $dba2->connect();
                $result = $dba2->getAll("select DISTINCT CalledNumber as number from AllNumberList with (nolock) where CalledNumber like ? and CallResult in ('2','3');",
                    [$this->searchNumber]);
            }
        }
        if (count($result) > $this->phone_limit) {
            echo json_encode(["data" => -1, "status" => -1,]);
            return;
        }
        echo json_encode(["data" => $result, "status" => 0, "country" => $this->countryCode]);
    }

    private function checkCallPlanCount() {
        if (($this->getCallPlanCount() + 1) > $this->list_limit) {
            $this->warning = "排程不得超過{$this->list_limit}筆";
            return false;
        }
        return true;
    }

    private function postEffectiveGroupCall($result) {
        if (!$this->checkCallPlanCount()) return ;
        $numbers = array_splice($result, 0, $this->phone_limit);
        $callOutID = DB::table('CallPlan')->select('max(CallOutID)+1 as count')->get()[0]["count"];//確保寫入numberList的calloutid是唯一值
        $callOutID = empty($callOutID) ? "1" : $callOutID;
        DB::table('CallPlan')->insert([
            'UserID'            => $this->session["choice"],
            'PlanName'          => $this->planName,
            'StartCalledNumber' => $numbers[0],
            'EndCalledNumber'   => end($numbers),
            'CalledCount'       => count($numbers),
            'CallerPresent'     => 1,
            'CallerID'          => '',
            'CalloutGroupID'    => $this->calloutGroupID,
            'Calldistribution'  => $this->calldistribution,
            'CallProgressTime'  => $this->callProgressTime,
            'ExtProgressTime'   => $this->extProgressTime,
            'UseState'          => 0,
            'ConcurrentCalls'   => $this->concurrentCalls,
            'NumberMode'        => $this->numberMode,
            'CallOutID'         => $callOutID
        ])->exec();
        $body = [];
        while ($number = array_shift($numbers)) {
            $body[] = [
                'CallOutID'    => $callOutID,
                'CalledNumber' => $number
            ];
        }
        $this->chunkInsertDB2('NumberList', $body);
        return count($result) > 0
            ? $this->postEffectiveGroupCall($result)
            : $this;
    }

    private function getEffectiveNumberByGroupCall($number, $count) {
        $dba2 = new DBA();
        $dba2->dbHost = "125.227.84.248";
        $dba2->dbName = "AcCdrSvr";
        $dba2->connect();
        $result = $dba2->getAll("select DISTINCT top $count OrgCalledId as number from AllCdrList with (nolock) where OrgCalledId >= ? order by OrgCalledId;",
            [$number]);
        return $result;
    }
}

?>
