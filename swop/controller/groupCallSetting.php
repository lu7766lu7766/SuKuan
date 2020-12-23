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
        return parent::render("effectiveNumber2");
    }

    public function getEffectiveNumber(){}

    public function getEffectiveNumber2(){}

    public function addDefaultSchedule(){
        $this->model->postDefaultSchedule($this->model->list);
    }
}

?>