<?php

use lib\ReturnMessage;
use service\UserRouteService;

class UserRouteController extends JController
{
	function __construct()
	{
		$this->service = new UserRouteService("AsRouting");
	}

	public function list($req)
	{
		ReturnMessage::success($this->service->list($req));
	}

	public function create($req)
	{
		try {
			ReturnMessage::success($this->service->create($req));
		} catch (Exception $err) {
			ReturnMessage::error($err->getMessage());
		}
	}

	public function update($req)
	{
		try {
			ReturnMessage::success($this->service->update($req));
		} catch (Exception $err) {
			ReturnMessage::error($err->getMessage());
		}
	}

	public function delete($req)
	{
		ReturnMessage::success($this->service->delete($req));
	}
}
