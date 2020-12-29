<?php
//require_once "swop/library/dba.php";
//$dba=new dba();
use \comm\DB;
use \comm\SQL;

class ExtensionInfo_Model extends JModel
{
	/**
	 * 新增分機
	 * @return $this|void
	 */
	public function postExtension ()
	{
		$dba = $this->dba;
		$this->extensionNos = ( empty( $this->extensionNos ) || $this->extensionNos < $this->extensionNo ) ?
			$this->extensionNo : $this->extensionNos;

		$tmp_extensionNo = $this->extensionNo;
		$exists_extensionNo = [ ];

		$sql = "insert into CustomerLists (CustomerNO,UserID,ExtensionNo,UserName,ExtName,OffNetCli,CustomerPwd) values ";

		$params = [ ];

		while ($tmp_extensionNo <= $this->extensionNos) {
			$exists_extensionNo[] = "'$tmp_extensionNo'";

			$sql .= "(?,?,?,?,?,?,?),";
			$params[] = $tmp_extensionNo;
			$params[] = $this->userId;
			$params[] = $tmp_extensionNo;
			$params[] = $tmp_extensionNo++;
			$params[] = $this->extName;
			$params[] = $this->offNetCli;
			$params[] = $this->customerPwd;
		}
		$sql = substr($sql, 0, -1);

		//////////////////////////////////
		$sql1 = "select CustomerNO from CustomerLists where CustomerNO in (" . join(",", $exists_extensionNo) . ")";
		$result = $dba->getAll($sql1);
		$exists_extensionNo = [ ];
		foreach ($result as $val)
			$exists_extensionNo[] = $val[ "CustomerNO" ];

		if (count($result) > 0) {
			$this->warning = join(",", $exists_extensionNo) . "分機已存在，請避開這些分機";

			return;
		}
		/////////////////////////////////

		$result = $dba->exec($sql, $params);

		$tmp_extensionNo = $this->extensionNo;
		$sql = "
            insert into ExtensionGroup (UserID,CalloutGroupID,CustomerNO)
            values
        ";
		$params = [ ];
		while ($tmp_extensionNo <= $this->extensionNos) {
			$sql .= "(?,?,?),";
			$params[] = $this->userId;
			$params[] = $this->calloutGroupId;
			$params[] = $tmp_extensionNo++;
		}
		$sql = substr($sql, 0, -1);
		$result2 = $dba->exec($sql, $params);

		return $result && $result2;
	}

	/**
	 * 分機修改
	 * @return $this|void
	 */
	public function getExtensionDetail ()
	{
		$dba = $this->dba;
		$userId = $this->userId;
		$extensionNo = $this->extensionNo;
		$sql = "
            select a.ExtName,a.CustomerPwd,a.StartRecorder,a.Suspend,a.UseState,b.CalloutGroupID,a.OffNetCli
            from CustomerLists as a WITH (NOLOCK)
            left join ExtensionGroup as b WITH (NOLOCK) on a.UserID=b.UserID and a.CustomerNO=b.CustomerNO
            where a.UserID=? and a.ExtensionNo=?
        ";
		$params = [ $userId, $extensionNo ];
		$stmt = $dba->query($sql, $params);
		$this->data = $dba->fetch_assoc($stmt);
	}

	/**
	 * 分機更新
	 * @return $this|void
	 */
	public function updateExtensionDetail ()
	{
		$dba = $this->dba;
		$userId = $this->userId;
		$extensionNo = $this->extensionNo;

//        if($this->session["choice"]!="root"){
//            $sql = "select UseState from CustomerLists WITH (NOLOCK) where UserID=? and ExtensionNo=?;";
//            $params = array($userId,$extensionNo);
//            $this->useState = $dba->getAll($sql,$params)[0]["UseState"];
//        }
		if ($this->session[ "choice" ] != "root") {
			$sql = "
                select OffNetCli
                from CustomerLists WITH (NOLOCK)
                where UserID=? and ExtensionNo=?
            ";
			$params = [ $userId, $extensionNo ];
			$this->offNetCli = $dba->getAll($sql, $params)[ 0 ][ OffNetCli ];
		}


		$sql = "
            update CustomerLists set
                ExtName=?,
                CustomerPwd=?,
                StartRecorder=?,
                Suspend=?,
                UseState=?,
                OffNetCli=?
            where UserID=? and ExtensionNo=?;
            update ExtensionGroup set
                CalloutGroupID=?
            where UserID=? and CustomerNO=?;
        ";
		$params = [
			$this->extName,
			$this->customerPwd,
			$this->startRecorder??0,
			$this->suspend??1,
			$this->useState??0,
			$this->offNetCli,

			$userId, $extensionNo,

			$this->calloutGroupId,

			$userId, $extensionNo
		];

		return $dba->exec($sql, $params);

	}
}

?>
