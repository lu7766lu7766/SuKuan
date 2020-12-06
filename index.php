<?php
//session_start();
//start_session();
//ini_set('session.cookie_lifetime', 1);
//ini_set('session.gc_maxlifetime', 1);
date_default_timezone_set("Asia/Taipei");
set_time_limit(0);
session_start();
//echo ini_get("session.gc_maxlifetime");
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_NOTICE);
require("vendor/autoload.php");
require("swop/library/helper.php");
require("Route.php");
require("system/eloquent/Start.php");

use setting\Config;
use voku\helper\AntiXSS;
use comm\Route;

$config = new Config();

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

if (Route::isMatched()) {
    exit();
}

$air = explode('?', str_replace($config->base["folder"], "", $_SERVER['REQUEST_URI']))[0];

$base_hierarchy = explode("/", $air);
$base_url = $config->base['controller_dir'] . $base_hierarchy[0] . ".php";
$store_pos = 2;
if (file_exists($base_url)) // 驗證 controller 是否存在
{
    $controller = $base_hierarchy[0];
} else {
    $controller = $config->base["default_controller"];
    $base_url = $config->base['controller_dir'] . $config->base["default_controller"] . ".php";
    $store_pos--;
}
$controller_class = $controller . "_Controller";
require_once $base_url;
$swop = new $controller_class($config->base); //Env_Controller($base);
if (isset($base_hierarchy[1]) && method_exists($swop, $base_hierarchy[1])) //驗證 controller 與 action 是否存在
{
    $action = $base_hierarchy[1];
} else {
    //    if(strpos($air,"downloadAdCommunicationSearch")!==false)
    //    {
    //        die(); // 一個神奇的bug，沒有這行就會進來，導致excel無法下載
    //    }
    //    die($controller_class . "^^" . $base_hierarchy[0] . "^^" . $base_hierarchy[1]);
    $swop->redirect("index/index");
    $action = $config->base["default_action"];
    $store_pos--;
}
$data['submit_link'] = $config->base['folder'] . $controller . "/" . $action;
$data["controller"] = $controller;
$data["action"] = $action;
$data["top_layout"] = "shared/top.php";
$data["layout"] = $action;
$data["bottom_layout"] = "shared/bottom.php";
$a_get = [];
$len = count($base_hierarchy);
for ($i = $store_pos; $i < $len; $i += 2) {
    if (isset($base_hierarchy[$i + 1]) && $base_hierarchy[$i]) {
        $a_get[$base_hierarchy[$i]] = $base_hierarchy[$i + 1];
    }
}
foreach ($_GET as $key => $val) {
    $a_get[$key] = $val;
}
$data["get"] = $a_get;
$a_post = [];
foreach ($_POST as $key => $val) {
    $a_post[$key] = $val;
}
$data["post"] = $a_post;
$swop->getData($data);
$swop->$action();
