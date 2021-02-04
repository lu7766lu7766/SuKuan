<?php
require(dirname(__DIR__) . "/vendor/autoload.php");

use GO\Scheduler;

$scriptFolder = "/script/";

$scheduler = new Scheduler();

$files = glob(__DIR__ . $scriptFolder . '*', GLOB_MARK);

foreach ($files as $file) {
  $scheduler->php($file)->everyMinute();
}

$scheduler->work();
