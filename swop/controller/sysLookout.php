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

    public function callStatistics()
    {
        parent::render();
    }
}
