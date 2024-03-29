<?php

use comm\Request;
use Illuminate\Database\Capsule\Manager as DB;
use Rakit\Validation\Validator;
use service\GroupCallScheduleService;

class GroupCallScheduleController extends JController
{

	const RANGE = "0";
	const LIST = "1";
	const SAME = "2";
	const VALID = "3";

	function __construct()
	{
		$this->service = new GroupCallScheduleService();
	}

	public function list()
	{
		return DB::table("CallPlan")
			->where("UserID", session("choice"))
			->orderBy("CallOutID", "desc")
			->get();
	}

	public function detail(Request $request)
	{
		$result = DB::table("CallPlan")
			->where("UserID", $request->input("userID"))
			->where("CallOutID", $request->input("callOutID"))
			->first();
		$result->User = DB::table("SysUser")
			->where("UserID", $request->input("userID"))
			->first();
		return $result;
	}

	private function validate($request)
	{
		$validator = new Validator();
		$validation = $validator->validate($request->all(), [
			"NumberMode"               => "required",
			"StartCalledNumber"        => "required_if:NumberMode,0,2",
			"CalledCount"              => "required_if:NumberMode,0,2|numeric",
		]);
		if ($validation->fails()) {
			throw new Exception("驗證失敗，請確認欄位");
		}
	}

	public function create(Request $request)
	{
		$this->validate($request);
		$listCount = DB::table('CallPlan')->where('UserID', session("choice"))->count();
		$this->service->valideCallPlanMaxLimit($listCount);
		$StartCalledNumber = $request->input("StartCalledNumber");
		$CalledCount = $request->input("CalledCount");
		if ($request->input("NumberMode") == self::LIST) {
			$list = $this->service->getListAndValide();
			$StartCalledNumber = $list[0];
			$CalledCount = count($list);
		} else if ($request->input("NumberMode") == self::VALID) {
			$list = $this->service->getValidListAndValid($StartCalledNumber, $CalledCount);
			$StartCalledNumber = $list[0];
		}

		$this->service->valideCallOnceLimit($CalledCount);
		$callOutID = DB::table("CallPlan")->select(DB::raw("max(CallOutID)+1 as count"))->first()->count ?? "1";
		switch ($request->input("NumberMode")) {
			case self::RANGE:
				$numberCollection = $this->service->buildRangeNumberList($callOutID, $StartCalledNumber, $CalledCount);
				break;
			case self::LIST:
			case self::VALID:
				$numberCollection = $this->service->buildListNumberList($callOutID, $list);
				break;
			case self::SAME:
				$numberCollection = $this->service->buildSameNumberList($callOutID, $StartCalledNumber, $CalledCount);
				break;
		}
		if ($request->input("random")) {
			$numberCollection = $numberCollection->shuffle();
		}

		DB::transaction(function () use ($request, $callOutID, $numberCollection, $StartCalledNumber, $CalledCount) {
			DB::table("CallPlan")->insert([
				"UserID"            => session("choice"),
				"PlanName"          => $request->input("PlanName"),
				"StartCalledNumber" => $StartCalledNumber,
				'EndCalledNumber'   => $numberCollection->max("CalledNumber"),
				"CalledCount"       => $CalledCount,
				"CallerPresent"     => 1,
				"CallerID"          => "",
				"CalloutGroupID"    => $request->input("CalloutGroupID"),
				"Calldistribution"  => $request->input("Calldistribution"),
				"CallProgressTime"  => $request->input("CallProgressTime"),
				"ExtProgressTime"   => $request->input("ExtProgressTime"),
				"UseState"          => 0,
				"NumberMode"        => $request->input("NumberMode"),
				"CallOutID"         => $callOutID
			]);
			$numberCollection->chunk(100)->each(function ($chunk) {
				DB::table("NumberList")->insert($chunk->toArray());
			});
		});
		return true;
	}

	public function update(Request $request)
	{
		return DB::table("CallPlan")
			->where([
				["UserID", $request->input("UserID")],
				["CallOutID", $request->input("CallOutID")]
			])->update([
				"PlanName"          => $request->input("PlanName"),
				"CallerPresent"     => $request->input("CallerPresent"),
				"CallerID"          => $request->input("CallerID"),
				"CalloutGroupID"    => $request->input("CalloutGroupID"),
				"Calldistribution"  => $request->input("Calldistribution"),
				"CallProgressTime"  => $request->input("CallProgressTime"),
				"ExtProgressTime"   => $request->input("ExtProgressTime"),
				"UseState"          => $request->input("UseState", 0)
			]);
	}

	public function delete(Request $request)
	{
		DB::transaction(function () use ($request) {
			foreach ($request->input("datas") as $data) {
				$delRows = DB::table("CallPlan")->where("UserID", $data["UserID"])->where("CallOutID", $data["CallOutID"])->delete();
				$delRows && DB::table("NumberList")->where("CallOutID", $data["CallOutID"])->delete();
			}
		});
		return true;
	}
}
