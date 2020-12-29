<?php

use lib\ReturnMessage;
use Tightenco\Collect\Support\Collection;

class OldApi_Controller extends JController
{
	public function empSelect()
	{
		ReturnMessage::success($this->model->empSelect["option"]);
	}

	public function subEmp()
	{
		// dd($this->model->empSelect["option"]);
		ReturnMessage::success(Collection($this->model->empSelect["option"])
			->filter(function ($x) {
				return $x["value"]
					&& $x["value"] == $this->model->session["choice"]
					|| in_array($x["value"], $this->model->session["current_sub_emp"]);
			}));
	}

	public function choicer()
	{
		ReturnMessage::success($this->model->session["choicer"]);
	}

	public function parentOptions()
	{
		ReturnMessage::success(EmpHelper::KillValue(
			EmpHelper::getEmpSelect(
				$this->model->empSelect,
				[
					"name" => "parentId",
				]
			),
			$this->model->userID
		)["option"]);
	}
}
