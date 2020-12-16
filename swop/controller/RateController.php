<?php

use Illuminate\Database\Capsule\Manager as DB;
use lib\ReturnMessage;
use Tightenco\Collect\Support\Collection;

class RateController extends JController
{
    public function list($req)
    {
        ReturnMessage::success(DB::table("RateGroup")->select(["RateGroupID", "RateGroupName"])->where("UseState", "1")->orderBy("RateGroupID", "desc")->get());
    }

    public function create($req)
    {
        ReturnMessage::success(DB::table("RateGroup")->insert([
            "RateGroupID" => $req["post"]["RateGroupID"],
            "RateGroupName" => $req["post"]["RateGroupName"],
        ]));
    }

    public function createBatch($req)
    {
        ["post" => $post] = $req;
        ReturnMessage::success(
            DB::table("RateGroup")->insert(
                Collection($post["datas"])->map(function ($x) {
                    return [
                        "RateGroupID" => $x["RateGroupID"],
                        "RateGroupName" => $x["RateGroupName"],
                    ];
                })->toArray()
            )
        );
    }

    public function update($req)
    {
        ReturnMessage::success(
            DB::table("RateGroup")->where("RateGroupID", $req["post"]["RateGroupID"])->update([
                "RateGroupName" => $req["post"]["RateGroupName"],
            ])
        );
    }

    public function delete($req)
    {
        $reatGroupIDs = $req["post"]["RateGroupIDs"];
        if (count($reatGroupIDs) > 0) {
            DB::transaction(function () use ($reatGroupIDs) {
                DB::table("RateGroup")->whereIn("RateGroupID", $reatGroupIDs)->delete();
                DB::table("RateDetail")->whereIn("RateGroupID", $reatGroupIDs)->delete();
            });
        }

        ReturnMessage::success(true);
    }
}
