<?php
ini_set("display_errors", "On");
error_reporting(E_ALL & ~E_NOTICE);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/swop/library/helper.php';

use comm\DB;
use comm\DBA;
use comm\SQL;
use voku\helper\AntiXSS;

//var_dump(file_exists(__DIR__ . '/vendor/autoload.php'));
//var_dump(new AntiXSS());
//print_r(new Dotenv());
print_r(getApiUrl());
print_r(DB::table('SysUser')->select('UserID')->where('UserID', 'root')->orderBy('UserID', 'asc')->limit(10)->get());
//print_r(
//    DB::table('CallOutCDR a')
//        ->select(
//            [
//            'a.UserID',
//            'a.ExtensionNo',
//            'b.UserName',
//            //'sum(cast(a.CallDuration as float)) as CallDuration',
//            function($db) {
//                return $db->sum( function($db){
//                    return $db->cast('a.CallDuratio', 'float');
//                }, 'CallDuration');
//            },
//            //'COUNT(1) as Count',
//            function($db) {
//                return $db->count('1', 'Count');
//            },
//            'sum(cast(BillValue as float)) as BillValue',
//            'sum(cast(BillCost as float)) as BillCost'
//        ]
//        )->leftJoin('SysUser b', 'a.UserID', '=', 'b.UserID')
//        ->where([
//            ['a.UserID', 'root'],
//            ['cast((a.CallStartBillingDate+\' \'+a.CallStartBillingTime) as datetime)', '<', "2017-01-01 23:59:59"],
//            ['cast((a.CallStopBillingDate+\' \'+a.CallStopBillingTime) as datetime)', '>', "2017-01-01 00:00:00"]
//        ])
//        ->andWhere(function($db) {
//            $db->whereExists(function () {
//                return DB::table('SysUser')->select('1');
//            });
//        })
//    ->andWhere(function($db){
//        //$db->whereIn('UserID', ['lu7766', 'root', 'User04']);
//    })
//        ->groupBy('a.UserID', 'a.ExtensionNo', 'b.UserName')->orderBy('UserID')
//    ->export()
//->get()
//)
//print_r(
//    DB::table('test')->insert([
//        [
//            "t1" => "t1value",
//            "t2" => "t2value"
//        ],
//        [
//            "t1" => "t11value",
//            "t2" => "t12value"
//        ]
//    ])->export()
//)
/*select
a.UserID, a.ExtensionNo, b.UserName,
sum(cast(a.CallDuration as float)) as CallDuration,
COUNT(1) as Count,
sum(cast(BillValue as float)) as BillValue,
sum(cast(BillCost as float)) as BillCost
from CallOutCDR as a with (nolock)
left join SysUser as b  with (nolock) on a.UserID = b.UserID
where";*/
//$n = "0927666000";
//$phone_len = strlen($n);
//echo str_pad($n++,$phone_len,'0',STR_PAD_LEFT);
//echo str_pad($n++,$phone_len,'0',STR_PAD_LEFT);
//$connectionInfo = array( "Database"=>"AcAssistor", "UID"=>"Assistor2008", "PWD"=>"Assistor@2008R2", "CharacterSet"=>"UTF-8");
//$conn = sqlsrv_connect( "localhost, 1276", $connectionInfo);
//
//if ( sqlsrv_begin_transaction( $conn ) === false ) {
//    die( print_r( sqlsrv_errors(), true ));
//}
////
//for($i = 0; $i< 100; $i ++)
//{
//    $stmt = sqlsrv_query( $conn, "insert into Test (CallOutID, CalledNumber) values('0', '$i');" );
//    if (!$stmt) {
//        sqlsrv_rollback( $conn );
//        echo "Transaction rolled back.<br />";
//        die('die');
//    }
//
//}
//
//sqlsrv_commit( $conn );
//echo "Transaction committed.<br />";
//$dba = new DBA();
//$dba->connect();
////$sql = "select ? from aa";
////foreach([0] as $val) {
////    $sql = $this->str_replace_first("?", $val ?? "''", $sql);
////}
////echo $sql."^^";
////echo "<br>";
//echo "<pre>";
//
//print_r(DB::table('CallPlan')->select('*')->where('RunTimeCount', '')->export());
//print_r(DB::table('CallPlan')->select('*')->where('RunTimeCount', 0)->export());
//echo $dba->get("select * from CallPlan where RunTimeCount = ?", ['']);
//print_r($dba->mergeSQL("select * from CallPlan where RunTimeCount = ?", [0]));
//for($i = 0; $i< 10000; $i ++)
//{
//    $dba->query("insert into Test (CallOutID, CalledNumber) values('0', '$i');");
//}
//echo DB::t
echo DB::table('AdCDR')->select([
    'OrgCalledId',
    'CalledCalloutDate',
    'CallStartBillingDateTime' => function ($db) {
        return "CallStartBillingDate+' '+CallStartBillingTime";
    },
    'CallLeaveDateTime',
    'CallDuration'
])->where('UserID', 'gg')->orderBy(function ($db) {
    return $db->cast('CalledCalloutDate', 'datetime');
}, 'desc')
    ->export();
//function args2array (array $args) {
//    $res = is_array($args[0]) ? $args[0] : $args;
//    foreach ($res as $index => $value) {
//        if (is_int($index)) {
//            unset($res[$index]);
//            $field_split = explode('.', $value);
//            $res[$value] = end($field_split);
//        }
//    }
//    return $res;
//}
//print_r(args2array([['a.test', 'b.tt' => 'help']]));
//echo "gg";
//foreach(["a" => function() { echo "a"; }] as $index => $value) {
//    if (is_callable($index)) {
//        $index();
//    }
//    if (is_callable($value)) {
//        $value();
//    }
//}
//foreach(['test', 'tt' => 'help'] as $index => $value) {
//    echo "{$index}^^{$value}<br>";
//}

?>
