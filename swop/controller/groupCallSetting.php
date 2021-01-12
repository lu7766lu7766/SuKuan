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

    public function effectiveNumber(){
        return parent::render();
    }

    public function ajax_getEffectiveNumber(){
        echo $this->model->getEffectiveNumber();
    }

    public function addDefaultSchedule(){
        $this->model->postDefaultSchedule($this->model->list);
    }
}

?>