<?php

class DownloadFile_Controller extends JController
{
    public function __construct()
    {
        $this->checkLogin = false;
        parent::__construct();
    }

    public function recordFile()
    {
        $model = $this->model;
        $fileName = $model->fileName;
        $userId = $model->userId;
        $connectDate = $model->connectDate;
        $filePath = "D:\\Recording\\{$userId}\\{$connectDate}\\{$fileName}";
        $newFilePath = config("download") . $fileName;
        copy($filePath, $newFilePath);

        \comm\Http::download2($newFilePath);

    }

    public function recordFilesToZip()
    {
        $model = $this->model;
        $model->getRecordDownload();
        if ($model->warning != "") {
            echo "<script>";
            echo "alert('$model->warning');";
            echo "window.close();";
            echo "</script>";
            die("");
        }
        \comm\Http::download($model->targetPath);
    }
}
