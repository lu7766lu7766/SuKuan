<?php

use Illuminate\Database\Capsule\Manager as DB;
use lib\ReturnMessage;

class CommunicationHistory_Model extends JModel
{
    public function __construct()
    {
        parent::__construct();
        // 單次上傳筆數限制
        $this->black_list_once_limit = getenv2("BLACK_LIST_ONCE_LIMIT", 10000);
    }

    private function writeCommunicationSearch($datas)
    {
        $content = "";
        foreach ($datas as $data) {
            $tmp_data = [
                $data["UserID"],
                $data["ExtensionNo"],
                $data["OrgCalledId"],
                $data["CallStartBillingDate"] . " " . $data["CallStartBillingTime"],
                $data["CallDuration"],
                $data["BillValue"],
                $data["CustomerLevel"]
            ];
            $content .= (join(",", $tmp_data) . "\r\n");
        }
        $this->fileName = date(
            "Y-m-d",
            time()
        ) . '-' . session("choice") . '.txt' . session("login")["UserID"];
        //        echo $this->fileName . "^^";
        $fp = fopen($this->fileName, 'w');
        fwrite($fp, $content);
        fclose($fp);
        @mkdir(config("download"));
        @mkdir(config("communicationSearch"));
        if (file_exists($this->fileName)) {
            rename($this->fileName, config("communicationSearch") . $this->fileName);
        }
        $pastMonth = date("Y-m", strtotime('-2 month'));
        $this->delTreePrefix(config("communicationSearch"), $pastMonth);
    }

    public function getBlackList()
    {
        $dba = $this->dba;
        $page = $this->page = $this->page ?? 0;
        $per_page = $this->per_page = (!empty($this->per_page) ? $this->per_page : 100);
        $offset = $per_page * $page + 1;
        $limit = $per_page;
        $order = " order by OrderKey";
        $sql1 = " select count(1) as count from Blacklist where UserID = ?";
        $sql = "select ROW_NUMBER() over ({$order}) rownum,
                CalledNumber,OrderKey
                from Blacklist with (nolock)
                where UserID = ?";
        $params = [session("choice")];
        $result = $dba->getAll($sql1, $params)[0];
        $this->rows = $result["count"];
        $this->last_page = ceil($this->rows / $per_page);
        $this->data = $dba->getAllLimit($sql, $params, $offset, $limit);
        $this->writeCommunicationSearch($this->data);
    }

    public function getDownloadBlackList()
    {
        $sql = "select CalledNumber from BlackList with (nolock) where UserID=?";
        $params = [session("choice")];
        $this->createFile($sql, $params, "_BlackList");
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
        $this->fileName = date("Y-m-d", time()) . "_" . session("choice") . "{$suffix}.txt";
        $this->filePath = "download/" . $this->fileName;
        @mkdir(config("download"));
        @mkdir(config("callStatus"));
        file_put_contents($this->fileName, $txt);
        rename($this->fileName, $this->filePath);
        $pastMonth = date("Y-m", strtotime('-2 month'));
        $this->delTreePrefix(config("callStatus"), $pastMonth);
    }

    public function uploadBlackList()
    {
        $userID = session("choice");
        $this->result = [];
        $result = $this->readUploadList();
        $len = count($result);
        if (!$len) {
            return;
        } else {
            if ($len > $this->black_list_once_limit) {
                $this->warning = "筆數不得超過{$this->black_list_once_limit}筆";
                return;
            }
        }
        $chunk_contents = array_chunk(array_map(function ($n) {
            return "'" . $n . "'";
        }, $result), 300);
        foreach ($chunk_contents as $contents) {
            $tmp_content = join(',', $contents);
            $repeat_numbers = $this->dba->getAll("select CalledNumber from Blacklist with (nolock) where UserID='$userID' and CalledNumber in ({$tmp_content})");
            foreach ($repeat_numbers as $number) {
                $this->result[] = $number["CalledNumber"];
            }
        }

        $body = [];
        while ($len-- > 0) {
            $number = array_shift($result);
            if (in_array($number, $this->result) || empty($number)) {
                continue;
            }
            $body[] = [
                'UserID'       => $userID,
                'CalledNumber' => $number
            ];
        }
        $this->chunkInsertDB2('Blacklist', $body, 300);

        if (!empty($sql)) {
            $this->dba->exec($sql);
        }
    }

    public function delBlackList()
    {
        $userID = session("choice");
        $delete = $this->delete;
        $delete = join(",", array_map(function ($v) {
            return "'" . $v . "'";
        }, $delete));
        $this->dba->exec("delete from Blacklist where UserID='$userID' and CalledNumber in ($delete)");
    }

    public function postBlackList()
    {
        $this->calledNumber = preg_replace('/\s(?=)/', '', $this->calledNumber);
        if (empty($this->calledNumber)) {
            return false;
        }
        $numbers = explode(",", $this->calledNumber);
        foreach ($numbers as $i => $number) {
            if (count($this->dba->getAll("select 1 from Blacklist with (nolock) where UserID='" . session("choice") . "' and CalledNumber='$number'"))) {
                $this->result[] = $number;
                unset($numbers[$i]);
            }
        }
        if (count($numbers)) {
            $calledNumber = join(" , ", array_map(function ($v) {
                return "('" . session("choice") . "','$v')";
            }, $numbers));
            $sql = "insert into Blacklist (UserID,CalledNumber) values $calledNumber";
            $this->dba->exec($sql);
        }
    }

    public function doCheckCalledNumber()
    {
        return ReturnMessage::success(
            DB::table("Blacklist")
                ->where("UserID", session("choice"))
                ->where("CalledNumber", $this->number)
                ->count()
        );
    }

    private function delete_files($target)
    {
        if (is_dir($target)) {
            $files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
            foreach ($files as $file) {
                $this->delete_files($file);
            }
            rmdir($target);
        } elseif (is_file($target)) {
            unlink($target);
        }
    }
}
