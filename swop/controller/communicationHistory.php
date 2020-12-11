<?php

use lib\ReturnMessage;
use Illuminate\Database\Capsule\Manager as DB;

class CommunicationHistory_Controller extends JController
{
    public function communicationSearch()
    {
        $model = $this->model;
        if ($model->submit) {
            $model->getCommunicationSearch();
        }
        $model->empSelect2 = EmpHelper::getEmpSelect(
            $model->empSelect,
            ["selected" => $model->userId, "option" => ["value" => "", "name" => ""]]
        );
        foreach ($model->empSelect2["option"] as $i => $option) {
            if (
                $option["value"] != "" &&
                $option["value"] != $model->session["choice"] &&
                !in_array($option["value"], $model->session["current_sub_emp"])
            ) {
                unset($model->empSelect2["option"][$i]);
            }
        }
        $model->pageSelect = PageHelper::getPageSelect($model->page, $model->last_page);
        $model->callTypeSelect = [
            "id"       => "callType",
            "name"     => "callType",
            "selected" => $model->callType,
            "option"   => [
                ["value" => "", "name" => "全部"],
                ["value" => "0", "name" => "自動撥號"],
                ["value" => "1", "name" => "手動撥號"]
            ]
        ];
        return parent::render();
    }

    public function communicationSearch_vue()
    {
        return parent::render();
    }

    /**
     * 取得除通聯紀錄列表
     */
    public function ajax_getCommunicationSearchCommonData()
    {
        $this->model->getCommunicationSearchCommonData();
        echo json_encode([
            "code" => 0,
            "data" => [
                "count"      => $this->model->rows,
                "totalTime"  => $this->model->totalTime ?? 0,
                "totalMoney" => $this->model->totalMoney ?? 0
            ]
        ]);
    }

    /**
     * 軟刪除通聯紀錄
     */
    public function ajax_delCommunicationSearchCommonData()
    {
        $res = $this->model->delCommunicationSearchCommonData();
        echo json_encode([
            "code" => 0,
            "data" => $res
        ]);
    }

    public function ajax_getCommunicationSearchPageDatas()
    {
        $this->model->getCommunicationSearchPageDatas();
        echo json_encode([
            "code" => 0,
            "data" => $this->model->data
        ]);
    }

    public function communicationSearchDownload()
    {
        $model = $this->model;
        $model->getCommunicationSearchDownload();
        $fileName = $model->fileName;
        $filePath = $this->base["communicationSearch"] . $fileName;
        $newFileName = str_replace(".txt" . $model->session["login"]["UserID"], ".txt", $fileName);
        $fileSize = filesize($filePath);
        header('Pragma: public');
        header('Expires: 0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: attachment; filename="' . $newFileName . '";');
        header('Content-Transfer-Encoding: binary');
        readfile($filePath);
    }

    public function getTaskRankingList()
    {
        $selectRaw = DB::raw("
            sum(cast(CallOutCDR.CallDuration as float)) as CallDuration,
            COUNT(1) as Count,
            sum(cast(BillValue as float)) as BillValue,
            sum(cast(BillCost as float)) as BillCost");
        $query = DB::table("CallOutCDR")
            ->leftJoin("SysUser", "CallOutCDR.UserID", "=", "SysUser.UserID")
            ->orderBy("CallOutCDR.UserID");
        if (!empty($this->model->userID)) {
            $query->where("CallOutCDR.UserID", $this->model->userID);
        } else {
            $query->whereIn("CallOutCDR.UserID", $this->model->session["current_sub_emp"]);
        }
        if (!empty($this->model->callStartBillingDate)) {
            $query->whereRaw("cast((CallOutCDR.CallStartBillingDate+' '+CallOutCDR.CallStartBillingTime) as datetime) < ?", [$this->model->callStopBillingDate . "  23:59:59"]);
        }
        if (!empty($this->model->callStopBillingDate)) {
            $query->whereRaw("cast((CallOutCDR.CallStopBillingDate+' '+CallOutCDR.CallStopBillingTime) as datetime) > ?", [$this->model->callStartBillingDate . " 00:00:00"]);
        }

        switch ($this->model->display_mode) {
            case "0":
                ReturnMessage::success(
                    $query
                        ->select("CallOutCDR.UserID", "CallOutCDR.ExtensionNo", "SysUser.UserName", $selectRaw)
                        ->groupBy("CallOutCDR.UserID", "CallOutCDR.ExtensionNo", "SysUser.UserName")
                        ->get()
                );
                break;
            case "1":
                ReturnMessage::success(
                    $query
                        ->select("CallOutCDR.UserID", "SysUser.UserName", $selectRaw)
                        ->groupBy("CallOutCDR.UserID", "SysUser.UserName")
                        ->get()
                );
                break;
        }
    }

    public function taskRanking()
    {
        return parent::render();
    }

    public function pointHistory()
    {
        $model = $this->model;
        if ($model->submit) {
            $model->getPointHistory();
            $model->pageSelect = PageHelper::getPageSelect($model->page, $model->last_page);
        }
        $model->empSelect2 = EmpHelper::getEmpSelect(
            $model->empSelect,
            ["selected" => $model->userId, "option" => ["value" => "", "name" => ""]]
        );
        return parent::render();
    }

    /**
     * edit pointHistory memo
     */
    public function ajaxEditRechargeLogMemo()
    {
        $this->model->editRechargeLogMemo();
    }

    public function recordDownload()
    {
        return parent::render();
    }

    public function blackList()
    {
        if ($this->model->status == "delete") {
            $this->model->delBlackList();
        } else {
            if ($this->model->status == "add") {
                $this->model->postBlackList();
            } else {
                if ($this->model->status == "file") {
                    if (file_exists($_FILES['list']['tmp_name'])) {
                        $this->model->uploadBlackList();
                    } else {
                        $this->model->warning = "找不到上傳的檔案！！";
                    }
                }
            }
        }
        $this->model->getBlackList();
        $this->model->pageSelect = PageHelper::getPageSelect($this->model->page, $this->model->last_page);
        return parent::render();
    }

    public function effectiveNumberUpload()
    {
        return parent::render();
    }

    public function checkCalledNumber()
    {
    }

    public function downloadBlackList()
    {

        $this->model->getDownloadBlackList();
        $this->fileDownload($this->model->filePath);
    }

    private function fileDownload($filePath)
    {
        $fileName = end(explode("/", $filePath));
        $fileSize = filesize($filePath);
        header('Pragma: public');
        header('Expires: 0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i ') . ' GMT');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: octet-stream');
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: attachment; filename="' . $fileName . '";');
        header('Content-Transfer-Encoding: binary');
        readfile($filePath);
        unlink($filePath);
    }
}
