<?php

use service\UserRouteService;

class ManualUserRouteController extends JController
{

	function __construct()
	{
		$this->service = new UserRouteService("UserRouting");
	}

	public function list($req)
	{
		return $this->service->list($req);
	}

	public function create($req)
	{
		return $this->service->create($req);
	}

	public function update($req)
	{
		return $this->service->update($req);
	}

	public function delete($req)
	{
		return $this->service->delete($req);
	}

	public function createBatch($req)
	{
		return $this->service->createBatch($req);
	}
}
