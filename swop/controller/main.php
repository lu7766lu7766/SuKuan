<?php

class Main_Controller extends Controller
{
    public function main()
    {
        $model = $this->model;
        if ($model->submit) {
            //            if(true)
            if (strtolower($model->captcha) == strtolower($_SESSION["code_login"]) || isDev()) {
                if ($model->login_in()) {
                    $this->redirect("index/index");
                } else {
                    $model->errorMsg = "帳號或密碼錯誤";
                }
            } else {
                if (!$_SESSION["code_login"] || !$model->captcha) {
                    $model->errorMsg = "伺服器錯誤";
                    $model->dba->server_restart();
                    header("Refresh:0");
                } else {
                    $model->errorMsg = "驗證碼錯誤";
                }
            }
        }

        return $this->render();
    }

    public function code_login()
    {
        require_once config("comm_dir") . "Verification.php";;
        $vert = new Verification(4);
        $_SESSION["code_login"] = $vert->getCode();
        $vert->draw();
    }

    public function server_restart()
    {
        $this->model->dba->server_restart();
        $this->redirect($this->defaultUrl);
    }
}
