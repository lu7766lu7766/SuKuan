<?php

class Model
{
    public function __construct()
    {
        require_once config("comm_dir") . "DBA.php";
        $this->dba = new comm\DBA;
        $this->dba->connect();
        $this->session = $this->getSession();
    }

    private function getSession()
    {
        return $_SESSION[config("folder")];
    }
}
