<?php

use lib\ReturnMessage;

class SysLookout_Controller extends JController
{
    public function callStatus()
    {
        parent::render();
    }

    public function keyMethod()
    {
        parent::render();
    }

    public function ping()
    {
        parent::render();
    }

    public function ajaxPing()
    {
        $this->model->getPing();
    }

    public function downloadCalledCount()
    {
        $this->model->getDownloadCalledCount();
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
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . $fileSize);
        header('Content-Disposition: attachment; filename="' . $fileName . '";');
        header('Content-Transfer-Encoding: binary');
        readfile($filePath);
    }

    public function downloadWaitCall()
    {
        $this->model->getDownloadWaitCall();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadCalloutCount()
    {
        $this->model->getDownloadCalloutCount();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadCallSwitchCount()
    {
        $this->model->getDownloadCallSwitchCount();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadCallConCount()
    {
        $this->model->getDownloadCallConCount();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadFaild()
    {
        $this->model->getDownloadFaild();
        $this->fileDownload($this->model->filePath);
    }

    public function downloadMissed()
    {
        $this->model->getDownloadMissed();
        $this->fileDownload($this->model->filePath);
    }
}
