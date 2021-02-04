<?php
$rootFolder = dirname(dirname(__DIR__));

require($rootFolder . "/vendor/autoload.php");
require($rootFolder . "/swop/library/helper.php");
require($rootFolder . "/system/eloquent/Start.php");

use Illuminate\Database\Capsule\Manager as DB;
use service\GroupCallScheduleService;

$adFolder = config("ad");
$files = glob($adFolder . '_*', GLOB_MARK);
foreach ($files as $file) {
  $info = pathinfo($file);
  $callOutID = substr($info["filename"], 1);
  $fileName = $callOutID  . "." . $info["extension"];
  $folder = $info["dirname"] . "/";
  $filePath = $folder . $fileName;
  if (!DB::table("AdPlan")->where("CallOutID", $callOutID)->count()) {
    @unlink($file);
    continue;
  }
  // start proccess
  rename($folder . $info["basename"], $filePath);
  $list = preg_split("/\\r\\n|\\n|\\r/", file_get_contents($filePath));
  $service = new GroupCallScheduleService();
  $collection = $service->buildListNumberList($callOutID, $list);
  DB::transaction(function () use ($collection, $filePath, $callOutID) {
    $collection->chunk(100)->each(function ($chunk) {
      DB::table("AdNumberList")->insert($chunk->toArray());
    });
    DB::table("AdPlan")
      ->where("CallOutID", $callOutID)
      ->update(["Batch" => 1]);
    @unlink($filePath);
  });
}
