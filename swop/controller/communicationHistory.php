<?php

class CommunicationHistory_Controller extends JController
{
    public function communicationSearch()
    {
        return parent::render();
    }

    public function taskRanking()
    {
        return parent::render();
    }

    public function pointHistory()
    {
        return parent::render();
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

    public function checkCalledNumber()
    {
        echo $this->model->doCheckCalledNumber();
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
