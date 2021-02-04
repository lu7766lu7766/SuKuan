<?php

namespace setting;

class Config
{
    static private $instance;

    static public function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    static public function get($key)
    {
        return self::getInstance()->base[$key];
    }

    public $base = [];

    public function __construct()
    {
        $base["version"] = "2018110201";
        $base["default_controller"] = "main";
        $base["default_action"] = "main";

        $base["folder"] = getenv2("BASE_FOLDER"); //"/ZHCC/"; //dirname(dirname(__DIR__)). "/"; // "/aurora02/"
        $base["root_folder"] = dirname(dirname(__DIR__)) . "/";
        $base["swop"] = $base["root_folder"] . "swop/";
        $base["swop_uri"] = "swop/";
        $base["model_dir"] = $base["swop"] . "model/";
        $base["controller"] = "controller/";
        $base["controller_dir"] = $base["swop"] . $base["controller"];
        $base["view_dir"] = $base["swop"] . "view/";
        $base["setting_uri"] = $base["swop_uri"] . "setting/";
        $base["setting_dir"] = $base["swop"] . "setting/";
        $base["comm_dir"] = $base["swop"] . "comm/";
        $base["library_dir"] = $base["swop"] . "library/";
        $base["language_dir"] = $base["swop"] . "language/";
        $base["lang"] = "tw";
        $base["public"] = $base["root_folder"] . "public/";
        $base["js_dir"] = $base["public"] . "js/";
        $base["css_dir"] = $base["public"] . "css/";
        $base["img_dir"] = $base["public"] . "img/";
        $base["tpl"] = $base["public"] . "img/template/";
        $base["download"] = $base["root_folder"] . "download/";
        $base["record"] = $base["download"] . "record/";
        $base["communicationSearch"] = $base["download"] . "communicationSearch/";
        $base["callStatus"] = $base["download"] . "callStatus/";
        $base["sweep"] = $base["download"] . "sweep/";
        $base["ad"] = $base["download"] . "ad/";
        $base["voiceManage"] = $base["download"] . "voiceManage/";
        $base["cn_phone_rule"] = $base["setting_dir"] . "cn_phone_rule.json";
        $base["tw_phone_rule"] = $base["setting_dir"] . "tw_phone_rule.json";

        $this->base = $base;
    }
}
