<?php

use comm\Request;
use Illuminate\Database\Capsule\Manager as DB;

class RateController extends JController
{
    public function list()
    {
        return DB::table("RateGroup")->select(["RateGroupID", "RateGroupName"])->where("UseState", "1")->orderBy("RateGroupID", "desc")->get();
    }

    public function create(Request $request)
    {
        return DB::table("RateGroup")->insert([
            "RateGroupID" => $request->input("RateGroupID"),
            "RateGroupName" => $request->input("RateGroupName"),
        ]);
    }

    public function createBatch(Request $request)
    {
        return DB::table("RateGroup")->insert(
            collect($request->input("datas"))->map(function ($x) {
                return [
                    "RateGroupID" => $x["RateGroupID"],
                    "RateGroupName" => $x["RateGroupName"],
                ];
            })->toArray()
        );
    }

    public function update(Request $request)
    {
        return DB::table("RateGroup")->where("RateGroupID", $request->input("RateGroupID"))->update([
            "RateGroupName" => $request->input("RateGroupName"),
        ]);
    }

    public function delete(Request $request)
    {
        $reatGroupIDs = $request->input("RateGroupIDs");
        if (count($reatGroupIDs) > 0) {
            DB::transaction(function () use ($reatGroupIDs) {
                DB::table("RateGroup")->whereIn("RateGroupID", $reatGroupIDs)->delete();
                DB::table("RateDetail")->whereIn("RateGroupID", $reatGroupIDs)->delete();
            });
        }
        return true;
    }
}
