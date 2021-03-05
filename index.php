<?php
require("swop/library/helper.php");
require("system/Session.php");

date_default_timezone_set("Asia/Taipei");
set_time_limit(0);

ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_NOTICE);
require("vendor/autoload.php");

require("Route.php");
require("system/eloquent/Start.php");

use voku\helper\AntiXSS;
use comm\Route;


$antiXss = new AntiXSS();
if (is_array($_GET)) {
    foreach ($_GET as $key => $value) {
        if ($value != '') {
            $_GET[$key] = $antiXss->xss_clean($value);
        }
    }
}
if (is_array($_POST)) {
    foreach ($_POST as $key => $value) {
        if ($value != '') {
            $_POST[$key] = $antiXss->xss_clean($value);
        }
    }
}

Route::procRoute();
