<?php

use comm\Request;
use service\UserRouteService;

class UserRouteController extends JController
{
	function __construct()
	{
		$this->service = new UserRouteService("AsRouting");
	}

	public function list(Request $request)
	{
		return $this->service->list($request);
	}

	public function create(Request $request)
	{
		return $this->service->create($request);
	}

	public function update(Request $request)
	{
		return $this->service->update($request);
	}

	public function delete(Request $request)
	{
		return $this->service->delete($request);
	}

	public function createBatch(Request $request)
	{
		return $this->service->createBatch($request);
	}
}
