<?php

use service\UserRouteService;

class ManualUserRouteController extends JController
{

	function __construct()
	{
		$this->service = new UserRouteService("UserRouting");
	}

	public function list($ctx)
	{
		return $this->service->list($ctx);
	}

	public function create($ctx)
	{
		return $this->service->create($ctx);
	}

	public function update($ctx)
	{
		return $this->service->update($ctx);
	}

	public function delete($ctx)
	{
		return $this->service->delete($ctx);
	}

	public function createBatch($ctx)
	{
		return $this->service->createBatch($ctx);
	}
}
