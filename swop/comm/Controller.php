<?php

class Controller
{
    public $checkLogin = true;

    public function __construct()
    {
        $this->defaultUrl = config("default_controller") . "/" . config("default_action");
    }

    public function getData($data)
    {
        foreach ($data as $key => $val) {
            $this->$key = $val;
        }
        $controller = $this->controller;
        $model_url = config("model_dir") . $controller . ".php";
        if (file_exists($model_url)) {
            require_once $model_url;
            $model_class = $controller . "_Model";
            $this->model = new $model_class();
        } else {
            $this->model = new JModel();
        }
        if (is_array($this->get)) {
            foreach ($this->get as $key => $val) {
                if (isset($key) && isset($val)) {
                    $this->model->$key = urldecode(preg_match("/^\d{4}-\d{1,2}-\d{1,2}/", $val) ? strtr(
                        $val,
                        ["-" => "/"]
                    ) : $val);
                }
            }
            $this->get = null;
        }
        if (is_array($this->post)) {
            foreach ($this->post as $key => $val) {
                if (isset($key) && isset($val)) {
                    $this->model->$key = $val;
                }
            }
            $this->post = null;
        }
        //            $this->redirect(config("default_controller")."/".config("default_action"));
        if ($controller != config("default_controller")) {
            if ($this->checkLogin && !session("login")) {
                $this->redirect($this->defaultUrl);
            }
        }
        if (is_array(session("model"))) {
            foreach (session("model") as $key => $val) {
                if (isset($key) && isset($val)) {
                    $this->model->$key = $val;
                }
            }
            session("model", null);
        }
        $action = $this->action;
        if (method_exists($this->model, $action)) {
            $this->model->$action();
        }
    }

    public function redirect($url = "", $model = [])
    {
        session("model", $model);
        if (!$url) {
            $url = $this->defaultUrl;
        }
        $url = trim($url, "/");
        $url = url($url);
        header("location:" . $url);
    }

    public function partialView($url)
    {
        $model = $this->model;
        $url = str_replace("~/", config("folder"), $url);
        if (file_exists($url)) {
            include_once $url;
        }
    }

    public function getModel($model)
    {
        include_once config("model_dir") . $model . ".php";
        $model_name = $model . "_Model";
        return new $model_name();
    }

    /**
     * @return 根目錄
     */
    public function getRootPath()
    {
        return dirname(dirname(__DIR__)) . '\\';
    }

    protected function render($view = null)
    {
        $model = $this->model;
        if (!$view || !is_string($view)) {
            $view = $this->layout;
        }
        $top_view_path = config("view_dir") . $this->top_layout;
        $view_path = config("view_dir") . $this->controller . "/" . $view . ".php";
        $bottom_view_path = config("view_dir") . $this->bottom_layout;
        if (file_exists($view_path)) {
            include_once $view_path;
        } else {
            $this->redirect("index/index");
        }
        return $this;
    }
}
