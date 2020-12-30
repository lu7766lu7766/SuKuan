<?php

use lib\ReturnMessage;
use Tightenco\Collect\Support\Collection;

class OldApi_Controller extends JController
{
	public function empSelect()
	{
		ReturnMessage::success($this->model->empSelect["option"]);
	}

	public function subEmpSelect()
	{
		ReturnMessage::success(collect($this->model->empSelect["option"])
			->filter(function ($x) {
				return collect($this->model->session["current_sub_emp"])->contains($x["value"]);
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
