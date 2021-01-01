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

    public function __destruct()
    {
        $this->setSession($this->session);
    }

    private function setSession($session)
    {
        $_SESSION[config("folder")] = $session;
    }

    private function getSession()
    {
        return $_SESSION[config("folder")];
    }
}
