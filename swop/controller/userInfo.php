<?php

class UserInfo_Controller extends JController
{
    public function userList()
    {
        return parent::render();
    }

    public function rateManage()
    {
        return parent::render();
    }

    public function userRatesModify()
    {
        $model = $this->model;
        if ($model->submit) //更新
        {
            if ($model->status == "delete" && $model->PrefixCode != "") {
                $model->deleteUserRates();
            } else {
                if ($model->status == "deleteAll") //deleteAll
                {
                    $model->deleteAllUserRates();
                    $this->redirect("userInfo/allRates");
                } else {
                    if ($model->status == "update") {
                        $model->updateUserRates();
                    } else {
                        $model->postUserRates();
                    }
                }
            }
        }
        $model->getUserRates();
        $model->subNumSelect = ["name" => "SubNum", "type" => "select", "class" => "form-control"];
        $i = 0;
        while ($i < 10) {
            $model->subNumSelect["option"][] = ["value" => $i, "name" => $i++];
        }
        $this->menu->currentName = "( " . $model->rateGroupId . " <=> " . $model->rateGroupName . " )用戶費率表更改";
        return parent::render("userRatesModify2");
    }

    public function userRoute()
    {
        return parent::render("userRoute");
    }

    public function manualUserRoute()
    {
        return parent::render("manualUserRoute");
    }

    public function searchRoute()
    {
        $model = $this->model;
        if ($model->submit) //更新
        {
            //            if($model->status=="delete" && is_array($model->delete) && count($model->delete))
            if ($model->status == "delete") {
                $model->deleteSearchRoute();
            } else {
                if ($model->status == "update") {
                    $model->updateSearchRoute();
                } else {
                    $model->postSearchRoute();
                }
            }
        }
        $model->empSelect2 = EmpHelper::getEmpSelect(
            $model->empSelect,
            ["name" => "UserID", "attr" => ["type" => "select"], "option" => ["value" => "", "name" => ""]]
        );
        foreach ($model->empSelect2["option"] as $i => $option) {
            if (
                $option["value"] != "" &&
                $option["value"] != $model->session["choice"] &&
                !in_array($option["value"], $model->session["current_sub_emp"])
            ) {
                unset($model->empSelect2["option"][$i]);
            }
        }
        sort($model->empSelect2["option"]);
        $model->inspectModeSelect = [
            "id"       => "InspectMode",
            "name"     => "InspectMode",
            "type"     => "select",
            "class"    => "form-control",
            "option"   => [],
            "selected" => $model->data["InspectMode"]
        ];
        $model->inspectModeSelect["option"][] = [
            "value" => 0,
            "name"  => "模式0"
        ];
        $model->inspectModeSelect["option"][] = [
            "value" => 1,
            "name"  => "模式1"
        ];
        $model->inspectModeSelect["option"][] = [
            "value" => 2,
            "name"  => "模式2"
        ];
        $model->getSearchRoute();
        return parent::render("searchRoute2");
    }

    public function userDetail()
    {
        $name = $this->model->userId ? '編輯' : '新增';
        $this->menu->currentName = "用戶" . $name;
        return parent::render();
    }


    public function routeSearch()
    {
        $model = $this->model;
        if ($model->submit) //更新
        {
            $model->getRouteSearch();
        }
        $model->empSelect2 = EmpHelper::getEmpSelect(
            $model->empSelect,
            ["selected" => $model->userId]
        );
        return parent::render();
    }
}
