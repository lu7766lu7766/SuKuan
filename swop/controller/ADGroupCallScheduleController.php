<?php

use comm\Request;
use Illuminate\Database\Capsule\Manager as DB;
use Rakit\Validation\Validator;
use service\GroupCallScheduleService;

class ADGroupCallScheduleController extends JController
{

	const RANGE = "0";
	const LIST = "1";

	function __construct()
	{
		$this->service = new GroupCallScheduleService();
	}

	public function options()
	{
		return lib\VoiceRecord::getFilesName(session("choice"));
	}

	public function list()
	{
		return DB::table("AdPlan")
			->where("UserID", session("choice"))
			->orderBy("CallOutID", "desc")
			->get()
			->map(function ($data) {
				$data->Count = DB::table("AdNumberList")->where("CallOutID", $data->CallOutID)->count();
				return $data;
			});
	}

	public function detail(Request $request)
	{
		$result = DB::table("AdPlan")
			->where("UserID", $request->input("UserID"))
			->where("CallOutID", $request->input("CallOutID"))
			->first();
		$result->User = DB::table("SysUser")
			->where("UserID", $request->input("UserID"))
			->first();
		return $result;
	}

	private function validate($request)
	{
		$validator = new Validator();
		$validation = $validator->validate($request->all(), [
			"NumberMode"               => "required",
			"CallProgressTime"         => "required|numeric|between:5,300",
			"StartCalledNumber"        => "required_if:NumberMode,0",
			"CalledCount"              => "required_if:NumberMode,0|numeric",
		]);
		if ($validation->fails()) {
			throw new Exception("驗證失敗，請確認欄位");
		}
	}

	public function create(Request $request)
	{
		$this->validate($request);
		$listCount = DB::table('AdPlan')->where('UserID', session("choice"))->count();
		$this->service->valideCallPlanMaxLimit($listCount);
		$StartCalledNumber = $request->input("StartCalledNumber");
		$CalledCount = $request->input("CalledCount");
		if ($request->input("NumberMode") == self::LIST) {
			$list = $this->service->getListAndValide();
			$StartCalledNumber = $list[0];
			$CalledCount = count($list);
		}

		$this->service->valideAdCallOnceLimit($CalledCount);
		$callOutID = DB::table("AdPlan")->select(DB::raw("max(CallOutID)+1 as count"))->first()->count ?? "1";
		switch ($request->input("NumberMode")) {
			case self::RANGE:
				$numberCollection = $this->service->buildRangeNumberList($callOutID, $StartCalledNumber, $CalledCount);
				break;
			case self::LIST:
				$numberCollection = $this->service->buildListNumberList($callOutID, $list);
				break;
		}
		if ($request->input("random")) {
			$numberCollection = $numberCollection->shuffle();
		}

		DB::transaction(function () use ($request, $callOutID, $numberCollection, $StartCalledNumber, $CalledCount) {
			DB::table("AdPlan")->insert([
				"UserID"            => session("choice"),
				"PlanName"          => $request->input("PlanName"),
				"StartCalledNumber" => $StartCalledNumber,
				"CalledCount" 			=> $CalledCount,
				"ConcurrentCalls"   => $request->input("ConcurrentCalls"),
				"UseState"          => 0,
				"NumberMode"        => $request->input("NumberMode"),
				"CallOutID"         => $callOutID,
				"StartDate"         => $request->input("StartDate"),
				"StartTime1"        => $request->input("StartTime1"),
				"StartTime2"        => $request->input("StartTime2"),
				"StartTime3"        => $request->input("StartTime3"),
				"StopDate"          => $request->input("StopDate"),
				"StopTime1"         => $request->input("StopTime1"),
				"StopTime2"         => $request->input("StopTime2"),
				"StopTime3"         => $request->input("StopTime3"),
				"FileName1"         => $request->input("FileName1"),
				"FileName2"         => $request->input("FileName2"),
				"CallRetry"         => $request->input("CallRetry"),
				"RetryTime"         => $request->input("RetryTime"),
				"StopOnConCount"    => $request->input("StopOnConCount"),
				"WaitDTMF"          => $request->input("WaitDTMF")
			]);
			@mkdir(config("ad"), 0777, true);
			file_put_contents(config("ad") . "_{$callOutID}.txt", $numberCollection->pluck("CalledNumber")->implode(PHP_EOL));
			// $numberCollection->chunk(100)->each(function ($chunk) {
			// 	DB::table("AdNumberList")->insert($chunk->toArray());
			// });
		});
		return true;
	}

	public function update(Request $request)
	{
		return DB::table("AdPlan")
			->where([
				["UserID", $request->input("UserID")],
				["CallOutID", $request->input("CallOutID")]
			])->update([
				"PlanName"          => $request->input("PlanName"),
				"CallProgressTime"	=> $request->input("CallProgressTime"),
				"ConcurrentCalls" 	=> $request->input("ConcurrentCalls"),
				"UseState"          => $request->input("UseState"),
				"StartDate" 				=> $request->input("StartDate"),
				"StartTime1" 				=> $request->input("StartTime1"),
				"StartTime2" 				=> $request->input("StartTime2"),
				"StartTime3" 				=> $request->input("StartTime3"),
				"StopDate" 					=> $request->input("StopDate"),
				"StopTime1" 				=> $request->input("StopTime1"),
				"StopTime2" 				=> $request->input("StopTime2"),
				"StopTime3" 				=> $request->input("StopTime3"),
				"FileName1" 				=> $request->input("FileName1"),
				"FileName2" 				=> $request->input("FileName2"),
				"CallRetry"         => $request->input("CallRetry"),
				"RetryTime"         => $request->input("RetryTime"),
				"StopOnConCount"    => $request->input("StopOnConCount"),
				"WaitDTMF"          => $request->input("WaitDTMF"),
			]);
	}

	public function delete(Request $request)
	{
		DB::transaction(function () use ($request) {
			foreach ($request->input("datas") as $data) {
				DB::table("AdPlan")->where("UserID", $data["UserID"])->where("CallOutID", $data["CallOutID"])->delete();
				DB::table("AdNumberList")->where("CallOutID", $data["CallOutID"])->delete();
			}
		});
		return true;
	}
}
