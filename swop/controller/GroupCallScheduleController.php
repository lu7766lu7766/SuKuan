<?php

use Illuminate\Database\Capsule\Manager as DB;
use lib\ReturnMessage;
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
        $this->PHONE_LIMIT = getenv2("GROUP_CALL_PHONE_LIMIT", 200000);
        $this->LIST_LIMIT = getenv2("GROUP_CALL_LIST_LIMIT", 99999);
    }

    public function list($req)
    {
        ["session" => $session] = $req;
        ReturnMessage::success(
            DB::table("CallPlan")
                ->where("UserID", $session["choice"])
                ->orderBy("CallOutID", "desc")
                ->get()
        );
    }

    public function detail($req)
    {
        ["post" => $post] = $req;
        try {
            $result = DB::table("CallPlan")
                ->where("UserID", $post["userID"])
                ->where("CallOutID", $post["callOutID"])
                ->first();
            $result->User = DB::table("SysUser")
                ->where("UserID", $post["userID"])
                ->first();
            ReturnMessage::success($result);
        } catch (Exception $err) {
            ReturnMessage::error("查無資料");
        }
    }

    private function validate($post)
    {
        $validator = new Validator();
        $validation = $validator->validate($post, [
            "NumberMode"               => "required",
            "StartCalledNumber"        => "required",
            "CalledCount"              => "required|numeric",
        ]);
        if ($validation->fails()) {
            throw new Exception("驗證失敗，請確認欄位");
        }
    }

    public function create($req)
    {
        ["session" => $session, "post" => $post] = $req;
        try {
            $this->validate($post);
            $this->service->valideCallPlanMaxLimit($session["choice"]);
            if ($post["NumberMode"] == self::LIST) {
                $list = $this->service->getListAndValide($this->base);
                $post["StartCalledNumber"] = $list[0];
                $post["CalledCount"] = count($list);
            } else if ($post["NumberMode"] == self::VALID) {
                $list = $this->service->getValidListAndValide($post["StartCalledNumber"], $post["CalledCount"]);
                $post["StartCalledNumber"] = $list[0];
            }

            $this->service->valideCallOnceLimit($post["CalledCount"]);
            $callOutID = DB::table("CallPlan")->select(DB::raw("max(CallOutID)+1 as count"))->first()->count ?? "1";
            switch ($post["NumberMode"]) {
                case self::RANGE:
                    $numberCollection = $this->service->buildRangeNumberList($callOutID, $post["StartCalledNumber"], $post["CalledCount"]);
                    break;
                case self::LIST:
                case self::VALID:
                    $numberCollection = $this->service->buildListNumberList($callOutID, $list);
                    break;
                case self::SAME:
                    $numberCollection = $this->service->buildSameNumberList($callOutID, $post["StartCalledNumber"], $post["CalledCount"]);
                    break;
            }

            DB::transaction(function () use ($session, $post, $callOutID, $numberCollection) {
                DB::table("CallPlan")->insert([
                    "UserID"            => $session["choice"],
                    "PlanName"          => $post["PlanName"],
                    "StartCalledNumber" => $post["StartCalledNumber"],
                    "CalledCount"       => $post["CalledCount"],
                    "CallerPresent"     => 1,
                    "CallerID"          => "",
                    "CalloutGroupID"    => $post["CalloutGroupID"],
                    "Calldistribution"  => $post["Calldistribution"],
                    "CallProgressTime"  => $post["CallProgressTime"],
                    "ExtProgressTime"   => $post["ExtProgressTime"],
                    "UseState"          => 0,
                    "ConcurrentCalls"   => $post["ConcurrentCalls"],
                    "NumberMode"        => $post["NumberMode"],
                    "CallOutID"         => $callOutID
                ]);
                $numberCollection->chunk(100)->each(function ($chunk) {
                    DB::table("NumberList")->insert($chunk->toArray());
                });
            });
            ReturnMessage::success(true);
        } catch (Exception $err) {
            ReturnMessage::error($err->getMessage());
        }
    }

    public function update($req)
    {
        ["post" => $post] = $req;
        try {
            ReturnMessage::success(
                DB::table("CallPlan")
                    ->where([
                        ["UserID", $post["UserID"]],
                        ["CallOutID", $post["CallOutID"]]
                    ])->update([
                        "PlanName"          => $post["PlanName"],
                        "CallerPresent"     => $post["CallerPresent"],
                        "CallerID"          => $post["CallerID"],
                        "CalloutGroupID"    => $post["CalloutGroupID"],
                        "Calldistribution"  => $post["Calldistribution"],
                        "CallProgressTime"  => $post["CallProgressTime"],
                        "ExtProgressTime"   => $post["ExtProgressTime"],
                        "ConcurrentCalls"   => $post["ConcurrentCalls"],
                        "UseState"          => $post["UseState"] ?? 0
                    ])
            );
        } catch (Exception $err) {
            ReturnMessage::error("刪除失敗");
        }
    }

    public function delete($req)
    {
        ["post" => $post] = $req;
        try {
            DB::transaction(function () use ($post) {
                foreach ($post["datas"] as $data) {
                    DB::table("CallPlan")->where("UserID", $data["UserID"])->where("CallOutID", $data["CallOutID"])->delete();
                    DB::table("NumberList")->where("CallOutID", $data["CallOutID"])->delete();
                }
            });
            ReturnMessage::success(true);
        } catch (Exception $err) {
            ReturnMessage::error("刪除失敗");
        }
    }
}
