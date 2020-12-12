<?php

use lib\ReturnMessage;

class OldApi_Controller extends JController
{
    public function empSelect()
    {
        ReturnMessage::success($this->model->empSelect["option"]);
    }
}
