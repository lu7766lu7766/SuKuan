<?php

class ExtensionInfo_Controller extends JController
{
    /**
     * 新增分機
     * @return $this|void
     */
    public function addExtension()
    {
        return parent::render("extensionDetail");
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
        $this->menu->currentName = "分機設定變更";
        return parent::render("extensionDetail");
    }
}
