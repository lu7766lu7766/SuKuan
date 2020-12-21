<?php

class SysLookout_Model extends JModel
{
    public function getPing()
    {
        $pingresult = exec("ping $this->ip", $outcome, $status);

        foreach ($outcome as $val) {
            echo iconv("BIG5", "UTF-8", $val) . "<br>";
        }
        return $status;
    }
}
