<?php

use comm\DB;
use comm\SQL;
use comm\Rules;

class AdCallSetting_Model extends JModel
{
    /**
     * 上傳廣告群乎音源檔
     * @return $this|void
     */
    public function uploadFileAndConvert($fieldName = "voiceFile")
    {
        return lib\VoiceRecord::uploadFile(session("choice"), $fieldName);
    }

    /**
     * 廣告群乎音源檔全部刪除
     * @return $this|void
     */
    public function deleteAllVoiceFile()
    {
        foreach ($this->fileNames as $fileName) {
            $this->deleteVoiceFile($fileName);
        }
    }

    /**
     * 廣告群乎音源檔刪除
     * @return $this|void
     */
    public function deleteVoiceFile($fileName)
    {
        lib\VoiceRecord::delFile(session("choice"), $fileName);
    }

    
    ////////////////////////////////////////////////////////////////////

    /**
     * 廣告群乎狀態
     */
    public function getAdCallStatus()
    {
        $user = DB::table('SysUser')
            ->select(
                'MaxRoutingCalls',
                'MaxCalls',
                'PlanDistribution',
                'AdSuspend',
                'MaxRegularCalls',
                'MaxSearchCalls',
                'AdNote'
            )
            ->where('UserID', session("choice"))->get()[0];
        $this->is_suspend = $user["AdSuspend"] != '1';
        $this->maxRoutingCalls = $user["MaxRoutingCalls"];
        $this->maxCalls = $user["MaxCalls"];
        $this->planDistribution = $user["PlanDistribution"];
        $this->adSuspend = $user["AdSuspend"];
        $this->maxRegularCalls = $user["MaxRegularCalls"];
        $this->maxSearchCalls = $user["MaxSearchCalls"];
        $this->adNote = $user["AdNote"];
    }

    public function getAjaxAdCallStatusContent()
    {
        $sql = "select
                  UserID, CallOutID, StartCalledNumber,
                  CalledCount, RunTimeCount, CallConCount,
                  CallUnavailable, ConcurrentCalls, CalloutCount,
                  TotalFee, UseState, PlanName, CallRetry,
                  (SELECT COUNT(*) FROM AdNumberList WHERE CallOutID=AdPlan.CallOutID AND CallResult=1 AND CallCount<AdPlan.CallRetry+1) as UnRecieveWaitCall
                from AdPlan with (nolock)
                where UserID=?
                order by CallOutID desc";
        $params = [session("choice")];
        $this->data3 = $this->dba->getAll($sql, $params);
        $db = DB::table('CallState')
            ->select('count(*) as count')
            ->where('CallDuration', '>', 0)
            ->andWhere('UserID', session("choice"));
        $db2 = clone $db;
        $this->waitExtensionNoCount = $db->andWhere([
            ['ExtensionNo', SQL::IS, null],
            ['ExtensionNo', '']
        ], SQL::OR)
            ->get()['count'];
        $this->extensionNoCount = $db2->andWhere([
            ['ExtensionNo', SQL::IS, null],
            ['ExtensionNo', '<>', '']
        ], SQL::OR)
            ->get()['count'];
        $this->balance = number_format(
            DB::table('SysUser')->select('Balance')->where('UserID', session("choice"))->get()[0]["Balance"],
            2,
            ".",
            ""
        );
    }

    public function updateAdSuspend()
    {
        $dba = $this->dba;
        $sql = "update SysUser set AdSuspend=abs(AdSuspend-1) where UserID=?";
        $params = [$this->userId];
        return $dba->exec($sql, $params);
    }

    public function updateUserAdNote()
    {
        $dba = $this->dba;
        $sql = "update SysUser set AdNote=? where UserID=?";
        $params = [nl2br2($this->adNote), $this->userId];
        return $dba->exec($sql, $params);
    }

    public function updateConcurrentCalls()
    {
        $dba = $this->dba;
        $sql = "update AdPlan set ConcurrentCalls=? where UserID=? and CallOutID=?;";
        $params = [$this->concurrentCalls, $this->userId, $this->calloutId];
        return $dba->exec($sql, $params);
    }

    public function updateUseState()
    {
        $dba = $this->dba;
        $sql = "update AdPlan set UseState=? where UserID=? and CallOutID=?";
        $params = [$this->useState, $this->userId, $this->callOutId];
        return $dba->exec($sql, $params);
    }

    public function deleteAdPlan()
    {
        $dba = $this->dba;
        $sql = "delete from AdPlan where UserID=? and CallOutID=?;
                delete from AdNumberList where CallOutID=?;";
        $params = [$this->userId, $this->callOutId, $this->callOutId];
        return $dba->exec($sql, $params);
    }

    /**
     * 廣告呼叫狀態下載
     * 筆數
     */
    public function getDownloadCalledCount()
    {
        $sql = "select CalledNumber from AdNumberList where CallOutID=? order by OrderKey";
        $params = [$this->callOutID];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /**
     * 待發
     */
    public function getDownloadWaitCall()
    {
        $sql = "select CalledNumber from AdNumberList where CallOutID=? and CallResult=? order by OrderKey";
        $params = [$this->callOutID, 0];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /**
     * 執行
     */
    public function getDownloadCalloutCount()
    {
        $sql = "select CalledNumber from AdNumberList where CallOutID=? and CallResult<>? order by OrderKey";
        $params = [$this->callOutID, 0];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /**
     * 接聽數
     */
    public function getDownloadCallConCount()
    {
        $sql = "select CalledNumber from AdNumberList where CallOutID=? and CallResult=? order by OrderKey";
        $params = [$this->callOutID, 3];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /**
     * 未接通待發
     */
    public function getDownloadUnRecieveWaitCall()
    {
        $sql = "select CalledNumber FROM AdNumberList WHERE CallOutID=? AND CallResult=1 AND CallCount<?+1 order by OrderKey";
        $params = [$this->callOutID, $this->callRetry];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /**
     * 未接通
     */
    public function getDownloadCallUnavailable()
    {
        $sql = "select CalledNumber FROM AdNumberList WHERE CallOutID=? AND CallResult=1 order by OrderKey";
        $params = [$this->callOutID];
        $this->data = [];
        array_map(function ($data) {
            $this->data[] = $data["CalledNumber"];
        }, $this->dba->getAll($sql, $params));
    }

    /////////////////////////////////////////////////////////////////////////////

    /**
     * 廣告通聯查詢
     */
    public function getAdCommunicationSearch()
    {
        $query = DB::table('AdCDR')->select([
            'OrgCalledId',
            'CalledCalloutDate',
            "CallStartBillingDate + ' ' + CallStartBillingTime as CallStartBillingDateTime",
            'CallLeaveDateTime',
            'CallDuration',
            'RecvDTMF',
            'FaxCall'
        ])->where('UserID', session("choice"));
        if ($this->CalledCalloutDateStart) // getdate()
        {
            $query->andWhere('cast(CalledCalloutDate as datetime)', '>=', $this->CalledCalloutDateStart);
        }
        if ($this->CalledCalloutDateStop) {
            $query->andWhere('cast(CalledCalloutDate as datetime)', '<=', $this->CalledCalloutDateStop);
        }
        if ($this->PlanName) {
            $query->andWhere('PlanName', $this->PlanName);
        }
        if ($this->CallBill > -1) {
            $query->andWhere('CallBill', $this->CallBill);
        }
        $query->orderBy(function ($db) {
            return $db->cast('CalledCalloutDate', 'datetime');
        }, 'desc');
        $this->data = $query->get();
    }

    /**
     * 廣告通聯查詢
     */
    public function getAdCommunicationSearch2()
    {
        $dba = $this->dba;
        $sql = "select ROW_NUMBER() over (order by CalledCalloutDate desc,LogID) rownum,
                  OrgCalledId, CalledCalloutDate,
                  CallStartBillingDate+' '+CallStartBillingTime as CallStartBillingDateTime,
                  CallLeaveDateTime, CallDuration
                from AdCDR with (nolock) where UserID='" . session("choice") . "' and ";
        $sql2 = "select count(1) as count ,
                  sum(CallDuration) as TotalTime,
                  sum(cast(BillValue as float)) as TotalMoney
                  from AdCDR with (nolock)
                  where UserID='" . session("choice") . "' and ";
        $sql3 = "select count(1) as TotalConnected
                  from AdCDR with (nolock)
                  where UserID='" . session("choice") . "' and CallDuration is not null and CallDuration > 0 and ";
        $condition = [];
        $params = [];
        if ($this->CalledCalloutDateStart) // getdate()
        {
            $condition[] = " cast(CalledCalloutDate as datetime) >= ? ";
            $params[] = $this->CalledCalloutDateStart;
        }
        if ($this->CalledCalloutDateStop) {
            $condition[] = " cast(CalledCalloutDate as datetime) <= ? ";
            $params[] = $this->CalledCalloutDateStop;
        }
        if ($this->PlanName) {
            $condition[] = " PlanName = ? ";
            $params[] = $this->PlanName;
        }
        if ($this->CallBill > -1) {
            $condition[] = " CallBill = ? ";
            $params[] = $this->CallBill;
        }
        if ($this->SearchSec > 0) {
            $condition[] = " CallDuration >= ? ";
            $params[] = $this->SearchSec;
        }
        if ($this->RecvDTMF) {
            $condition[] = " RecvDTMF = ? ";
            $params[] = $this->RecvDTMF;
        }
        if (count($condition)) {
            $sql .= join(" and ", $condition);
            $sql2 .= join(" and ", $condition);
            $sql3 .= join(" and ", $condition);
        } else {
            $sql = substr($sql, 0, -5);
            $sql2 = substr($sql, 0, -5);
            $sql3 = substr($sql, 0, -5);
        }
        $result = $dba->getAll($sql2, $params)[0];
        $this->count = $result['count'];
        $this->totalTime = $result['TotalTime'];
        $this->totalMoney = $result['TotalMoney'];
        $this->totalConnected = $dba->getAll($sql3, $params)[0]['TotalConnected'];
        //die(json_encode($this->count));
        $this->data = $dba->getAllLimit($sql, $params, $this->offset, $this->limit);
    }
}
