<?php

class GroupCallSetting_Controller extends JController
{
    public function groupCallSchedule()
    {
        return parent::render();
    }

    public function groupCallScheduleModify()
    {
        return parent::render();
    }

    public function addDefaultSchedule(){
        $this->model->postDefaultSchedule($this->model->list);
    }
}

?>