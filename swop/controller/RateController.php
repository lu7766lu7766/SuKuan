<?php

use Illuminate\Database\Capsule\Manager as DB;
use Tightenco\Collect\Support\Collection;

class RateController extends JController
{
    public function list($ctx)
    {
        return DB::table("RateGroup")->select(["RateGroupID", "RateGroupName"])->where("UseState", "1")->orderBy("RateGroupID", "desc")->get();
    }

    public function create($ctx)
    {
        return DB::table("RateGroup")->insert([
            "RateGroupID" => $ctx["post"]["RateGroupID"],
            "RateGroupName" => $ctx["post"]["RateGroupName"],
        ]);
    }

    public function createBatch($ctx)
    {
        ["post" => $post] = $ctx;
        return DB::table("RateGroup")->insert(
            collect($post["datas"])->map(function ($x) {
                return [
                    "RateGroupID" => $x["RateGroupID"],
                    "RateGroupName" => $x["RateGroupName"],
                ];
            })->toArray()
        );
    }

    public function update($ctx)
    {
        return DB::table("RateGroup")->where("RateGroupID", $ctx["post"]["RateGroupID"])->update([
            "RateGroupName" => $ctx["post"]["RateGroupName"],
        ]);
    }

    public function delete($ctx)
    {
        $reatGroupIDs = $ctx["post"]["RateGroupIDs"];
        if (count($reatGroupIDs) > 0) {
            DB::transaction(function () use ($reatGroupIDs) {
                DB::table("RateGroup")->whereIn("RateGroupID", $reatGroupIDs)->delete();
                DB::table("RateDetail")->whereIn("RateGroupID", $reatGroupIDs)->delete();
            });
        }

        return true;
    }
}
