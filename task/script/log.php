<?php

date_default_timezone_set("Asia/Taipei");

$filePath = "task.log";

$files = glob(__DIR__ . '/*', GLOB_MARK);
$fileContent = date("Y-m-d H:i", time()) . "\r\n" . join($files, "\r\n");

file_put_contents($filePath, $fileContent);
