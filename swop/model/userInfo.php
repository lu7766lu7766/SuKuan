<?php

use comm\DB;

class UserInfo_Model extends JModel
{
    /**
     * 取得使用者費率
     * @return $this
     */
    public function getUserRates()
    {
        $dba = $this->dba;
        $rateGroupId = $this->rateGroupId;
        $sql = "
            select PrefixCode,Time1,RateValue1,RateCost1,Time2,RateValue2,RateCost2
            from RateDetail
            where RateGroupID = ?
        ";//,SubNum,AddPrefix
        $this->rateDetail = $dba->getAll($sql, [$rateGroupId]);
        return $this;
    }

    public function deleteUserRates()
    {
        $dba = $this->dba;
        $sql = "delete from RateDetail where PrefixCode=? and RateGroupID=?";
        $params = [$this->PrefixCode, $this->rateGroupId];
        $dba->exec($sql, $params);
        return $this;
    }

    

    public function deleteAllUserRates()
    {
        $dba = $this->dba;
        $rateGroupId = $this->rateGroupId;
        $sql = "delete from RateDetail where RategroupID=?";
        $dba->exec($sql, [$rateGroupId]);
        $sql = "delete from RateGroup where RateGroupID=?";
        $dba->exec($sql, [$rateGroupId]);
        return $this;
    }

    public function postUserRates()
    {
        $dba = $this->dba;
        $sql = "select 1 from RateDetail where RateGroupID=? and PrefixCode=?;";
        $params = [
            $this->rateGroupId,
            $this->PrefixCode
        ];
        $rs = $dba->getAll($sql, $params);
        if (count($rs) > 0) {
            $this->warning = "座席與前置碼重複，無法新增。";
            return;
        }
        $sql = "
            insert into RateDetail
            (RateGroupID,PrefixCode,Time1,RateValue1,RateCost1,Time2,RateValue2,RateCost2)
            values(?,?,?,?,?,?,?,?)
        ";//,SubNum,AddPrefix
        $params = [
            $this->rateGroupId,
            $this->PrefixCode,
            $this->Time1 ?? 0,
            $this->RateValue1 ?? 0,
            $this->RateCost1 ?? 0,
            $this->Time2 ?? 0,
            $this->RateValue2 ?? 0,
            $this->RateCost2 ?? 0
        ];
//        ,
//        $this->SubNum??0,
//            $this->AddPrefix
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    public function updateUserRates()
    {
        $dba = $this->dba;
        $sql = "
            update RateDetail set
            Time1=?,
            RateValue1=?,
            RateCost1=?,
            Time2=?,
            RateValue2=?,
            RateCost2=?
            where
            RateGroupID=? and
            PrefixCode=?
        ";
//        ,
//        SubNum=?,
//            AddPrefix=?
        $params = [
            $this->Time1 ?? 0,
            $this->RateValue1 ?? 0,
            $this->RateCost1 ?? 0,
            $this->Time2 ?? 0,
            $this->RateValue2 ?? 0,
            $this->RateCost2 ?? 0,
//            $this->SubNum??0,
//            $this->AddPrefix,
            $this->rateGroupId,
            $this->PrefixCode
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    //////////////////////////////////////////////////////////////// userRoute start

    /**
     * 取得使用者路由
     * @return $this
     */
    public function getUserRoute()
    {
        $dba = $this->dba;
        $sql = "
            select UserID,PrefixCode,AddPrefix,RouteCLI,TrunkIP,TrunkPort,RouteName,SubNum
            from AsRouting
        ";
        if ($this->session["choice"] != "root") {
            $sql .= " where UserID='{$this->session['choice']}'";
        }
        $this->userRoute = $dba->getAll($sql);
        return $this;
    }

    public function postUserRoute()
    {
        $dba = $this->dba;
        $sql = "select 1 from AsRouting where UserID=? and PrefixCode=?;";
        $params = [
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->getAll($sql, $params);
        if (count($rs) > 0) {
            $this->warning = "用戶與前置碼重複，無法新增。";
            return;
        }
        $sql = "
            insert into AsRouting
            (UserID,PrefixCode,AddPrefix,
            RouteCLI,TrunkIP,TrunkPort,
            RouteName,SubNum)
            values
            (?,?,?,
            ?,?,?,
            ?,?)
        ";
        $params = [
            $this->UserID,
            $this->PrefixCode,
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    public function updateUserRoute()
    {
        $dba = $this->dba;
        $sql = "
            update AsRouting
            set
              AddPrefix=?,
              RouteCLI=?,
              TrunkIP=?,
              TrunkPort=?,
              RouteName=?,
              SubNum=?
            where
              UserID=? and
              PrefixCode=?
        ";
        $params = [
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum,
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    public function deleteUserRoute()
    {
        $dba = $this->dba;
        $sql = "delete from AsRouting where UserID=? and PrefixCode=?";
        $params = [$this->UserID, $this->PrefixCode];
        $dba->exec($sql, $params);
        return $this;
    }
    //////////////////////////////////////////////////////////////// userRoute end
    /////////////////////////////////////////////////////////////// manualUserRoute start
    public function getManualUserRoute()
    {
        $dba = $this->dba;
        $sql = "
            select UserID,PrefixCode,AddPrefix,RouteCLI,TrunkIP,TrunkPort,RouteName,SubNum
            from UserRouting
        ";
        if ($this->session["choice"] != "root") {
            $sql .= " where UserID='{$this->session['choice']}'";
        }
        $this->userRoute = $dba->getAll($sql);
        return $this;
    }

    public function postManualUserRoute()
    {
        $dba = $this->dba;
        $sql = "select 1 from UserRouting where UserID=? and PrefixCode=?;";
        $params = [
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->getAll($sql, $params);
        if (count($rs) > 0) {
            $this->warning = "用戶與前置碼重複，無法新增。";
            return;
        }
        $sql = "
            insert into UserRouting
            (UserID,PrefixCode,AddPrefix,
            RouteCLI,TrunkIP,TrunkPort,
            RouteName,SubNum)
            values
            (?,?,?,
            ?,?,?,
            ?,?)
        ";
        $params = [
            $this->UserID,
            $this->PrefixCode,
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    public function updateManualUserRoute()
    {
        $dba = $this->dba;
        $sql = "
            update UserRouting
            set
              AddPrefix=?,
              RouteCLI=?,
              TrunkIP=?,
              TrunkPort=?,
              RouteName=?,
              SubNum=?
            where
              UserID=? and
              PrefixCode=?
        ";
        $params = [
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum,
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    public function deleteManualUserRoute()
    {
        $dba = $this->dba;
        $sql = "delete from UserRouting where UserID=? and PrefixCode=?";
        $params = [$this->UserID, $this->PrefixCode];
        $dba->exec($sql, $params);
        return $this;
    }
    /////////////////////////////////////////////////////////////// manualUserRoute end
    //////////////////////////////////////////////////////////////// searchRoute start
    /**
     * 取得查詢路由
     * @return \comm\DBA
     */
    public function getSearchRoute()
    {
        $dba = $this->dba;
        $sql = "
            select UserID,PrefixCode,AddPrefix,RouteCLI,TrunkIP,TrunkPort,RouteName,SubNum,InspectMode
            from SearchRouting
        ";
        if ($this->session["choice"] != "root") {
            $sql .= " where UserID='{$this->session['choice']}'";
        }
        $this->userRoute = $dba->getAll($sql);
        return $this;
    }

    /**
     * 新增查詢路由
     * @return \comm\DBA
     */
    public function postSearchRoute()
    {
        $dba = $this->dba;
        $sql = "select 1 from SearchRouting where UserID=? and PrefixCode=?;";
        $params = [
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->getAll($sql, $params);
        if (count($rs) > 0) {
            $this->warning = "用戶與前置碼重複，無法新增。";
            return;
        }
        $sql = "
            insert into SearchRouting
            (UserID,PrefixCode,AddPrefix,
            RouteCLI,TrunkIP,TrunkPort,
            RouteName,SubNum,InspectMode)
            values
            (?,?,?,
            ?,?,?,
            ?,?,?)
        ";
        $params = [
            $this->UserID,
            $this->PrefixCode,
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum,
            $this->InspectMode
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    /**
     * 更新查詢路由
     * @return \comm\DBA
     */
    public function updateSearchRoute()
    {
        $dba = $this->dba;
        $sql = "
            update SearchRouting
            set
              AddPrefix=?,
              RouteCLI=?,
              TrunkIP=?,
              TrunkPort=?,
              RouteName=?,
              SubNum=?,
              InspectMode=?
            where
              UserID=? and
              PrefixCode=?
        ";
        $params = [
            $this->AddPrefix,
            $this->RouteCLI,
            $this->TrunkIP,
            $this->TrunkPort,
            $this->RouteName,
            $this->SubNum,
            $this->InspectMode,
            $this->UserID,
            $this->PrefixCode
        ];
        $rs = $dba->exec($sql, $params);
        return $rs;
    }

    /**
     * 刪除查詢路由
     * @return \comm\DBA
     */
    public function deleteSearchRoute()
    {
        $dba = $this->dba;
        $sql = "delete from SearchRouting where UserID=? and PrefixCode=?";
        $params = [$this->UserID, $this->PrefixCode];
        $dba->exec($sql, $params);
        return $this;
    }
    //////////////////////////////////////////////////////////////// userRoute end

    public function setUpdatePermission($userId, $permission, $permission_control)
    {
        if ($this->session["choice"] == $userId) {
            $this->session["permission"] = $permission;
            $this->session["permission_control"] = $permission_control;
        }
        if ($this->session["login"]["UserID"] == $userId) {
            $this->session["login"]["MenuList"] = $permission;
            $this->session["login"]["PermissionControl"] = $permission_control;
            return;
        }
        foreach ($this->session["sub_emp"] as $key => $sub_emp) {
            if ($sub_emp["UserID"] == $userId) {
                $this->session["sub_emp"][$key]["MenuList"] = $permission;
                $this->session["sub_emp"][$key]["PermissionControl"] = $permission_control;
                return;
            }
        }
    }

    /**
     * 取得路由查詢
     */
    public function getRouteSearch()
    {
        $dba = $this->dba;
        $extensionNo = $this->extensionNo;
        $len = strlen($extensionNo);
        $prefixCode_condition = [];
        if ($len == 0) {
            return;
        }
        while ($len) {
            $prefixCode_condition[] = "?";
            $prefixCode_params[] = substr($extensionNo, 0, $len--);
        }
        $sql = "
          select PrefixCode from RateDetail where PrefixCode = SUBSTRING(?,0,
            (

            select max(len(PrefixCode))+1
            from RateDetail
            where RateGroupID in ( select RateGroupID from SysUser where UserID=? ) and
                  PrefixCode in (" . join(",", $prefixCode_condition) . ")

            )
          )";
        $params = [];
        $params[] = $extensionNo;
        $params[] = $this->userId;
        $params = array_merge($params, $prefixCode_params);
        $result = $dba->getAll($sql, $params);
        if (count($result)) {
            $data = $result[0];
            $this->prefixCode = $data["PrefixCode"];
        }
        if ($this->prefixCode) {
            $this->data = 1;
            $sql = "
              select RouteCLI, AddPrefix, TrunkIP, PrefixCode, SubNum from AsRouting
              where UserID=? and
              (
                  PrefixCode = SUBSTRING(?,0,
                  (
                    select max(len(PrefixCode))+1
                    from AsRouting
                    where UserID=? and PrefixCode in (" . join(",", $prefixCode_condition) . "))
                  ) or PrefixCode = '*'
              );";
            $params = array_merge([$this->userId, $extensionNo, $this->userId], $prefixCode_params);
            $result = $dba->getAll($sql, $params);
            $data = $result[0];
            if (count($result) > 1) {
                if ($result[0]["PrefixCode"] == "*") {
                    $data = $result[1];
                }
            }
            $this->routeAddPrefix = $data["PrefixCode"];
            $this->mainCall = $data["RouteCLI"];
            $this->lastCalled = $data["AddPrefix"] . substr($extensionNo, $data["SubNum"]);
            $this->trunkIp = $data["TrunkIP"];
        }
    }
}
