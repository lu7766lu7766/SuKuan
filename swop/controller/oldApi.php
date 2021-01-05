<?php

use lib\ReturnMessage;

class OldApi_Controller extends JController
{
	public function empSelect()
	{
		echo ReturnMessage::success($this->model->empSelect["option"]);
	}

	public function subEmpSelect()
	{
		echo ReturnMessage::success(collect($this->model->empSelect["option"])
			->filter(function ($x) {
				return collect(session("current_sub_emp"))->contains($x["value"]);
			}));
	}

	public function choicer()
	{
		echo ReturnMessage::success(session("choicer"));
	}

	public function parentOptions()
	{
		echo ReturnMessage::success(EmpHelper::KillValue(
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
