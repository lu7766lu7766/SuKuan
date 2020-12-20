<?php

class SysLookout_Model extends JModel
{
    public function getPing()
    {
        $pingresult = exec("ping $this->ip", $outcome, $status);
//    if (0 == $status) {
//        $status = "alive";
//    } else {
//        $status = "dead";
//    }
        //echo "The IP address, $ip, is  ".$status."^^<BR>";
        foreach ($outcome as $val) {
            echo iconv("BIG5", "UTF-8", $val) . "<br>";
        }
        return $status;
    }

    /**
     * 呼叫狀態下載
     * 筆數
     */
    public function getDownloadCalledCount()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=?";
        $params = [$this->callOutId];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}");
    }

    private function createFile($sql, $params, $suffix = "")
    {
        $dba = $this->dba;
        $result = $dba->getAll($sql, $params);
        $data = [];
        foreach ($result as $val) {
            $data[] = $val["CalledNumber"];
        }
        $txt = join("\r\n", $data);
        $this->fileName = date("Y-m-d", time()) . "_{$this->session['choice']}{$suffix}.txt";
        $this->filePath = "download/" . $this->fileName;
        @mkdir($this->base["download"]);
        @mkdir($this->base["callStatus"]);
        file_put_contents($this->fileName, $txt);
        rename($this->fileName, $this->filePath);
        $pastMonth = date("Y-m", strtotime('-2 month'));
        $this->delTreePrefix($this->base["callStatus"], $pastMonth);
    }

    /**
     * 待發
     */
    public function getDownloadWaitCall()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult=?";
        $params = [$this->callOutId, 0];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}");
    }

    /**
     * 執行
     */
    public function getDownloadCalloutCount()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult<>?";
        $params = [$this->callOutId, 0];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}");
    }

    public function getDownloadCallSwitchCount()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult=?";
        $params = [$this->callOutId, 3];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}");
    }

    /**
     * 接聽數
     */
    public function getDownloadCallConCount()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult=?";
        $params = [$this->callOutId, 3];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}");
    }

    /**
     * 失敗下載
     */
    public function getDownloadFaild()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult=?";
        $params = [$this->callOutId, 1];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}_fail");
    }

    /**
     * 未接
     */
    public function getDownloadMissed()
    {
        $sql = "select CalledNumber from NumberList where CallOutID=? and CallResult=?";
        $params = [$this->callOutId, 2];
        $this->createFile($sql, $params, "_{$this->startCalledNumber}_missed");
    }
}

?>
