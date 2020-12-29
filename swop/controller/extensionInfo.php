<?php

class ExtensionInfo_Controller extends JController
{
    /**
     * 新增分機
     * @return $this|void
     */
    public function addExtension()
    {
        $model = $this->model;
        if($model->submit)
        {
            $rs = $model->postExtension();
            if($rs)
                parent::redirect("extensionInfo/extensionManage");
        }

        $model->empSelect2 = EmpHelper::getEmpSelect($model->empSelect,
            array("selected"=>$model->userId,"option"=>array("value"=>"","name"=>"")));

        $model->tmpPwd = "";
        while(strlen($model->tmpPwd)<10)
        {
            $model->tmpPwd .= rand(0,9);
        }
        return parent::render();
    }

    /**
     * 分機管理
     * @return $this|void
     */
    public function extensionManage()
    {
        return parent::render();
    }

    /**
     * 分機修改
     * @return $this|void
     */
    public function extensionModify()
    {
        $model = $this->model;
        $this->menu->currentName = "分機設定變更";

        if($model->submit)
        {
            $rs = $model->updateExtensionDetail();
            $url = "extensionInfo/extensionManage";
            $url .= !empty($model->search_userID)? "/search_userID/{$model->search_userID}": "";
            $url .= !empty($model->search_content)? "/search_content/{$model->search_content}": "";
            if($rs){
                parent::redirect($url);
            }
        }
        $model->getExtensionDetail();
        $model->calloutGroupIdSelect = array("id"=>"calloutGroupId","name"=>"calloutGroupId","class"=>"form-control","option"=>array(),"selected"=>$model->data["CalloutGroupID"]);
        $i = 1;
        while($i<=5)
        {
            $model->calloutGroupIdSelect["option"][] = array("value" => $i,"name"  => $i++);
        }
        return parent::render();
    }
}

?>