<?php

use comm\Route;

Route::get("/user/echoPassword", "UserController@echoPassword");
Route::post("/user/list", "UserController@list")->middleware("api", "auth");
Route::post("/user/create", "UserController@create")->middleware("api", "auth");
Route::post("/user/create/batch", "UserController@createBatch")->middleware("api", "auth");
Route::post("/user/update", "UserController@update")->middleware("api", "auth");
Route::post("/user/delete", "UserController@delete")->middleware("api", "auth");
Route::post("/user/menus", "UserController@menus")->middleware("api", "auth");
Route::post("/user/detail", "UserController@detail")->middleware("api", "auth");

// rate manage
Route::post("/rate/list", "RateController@list")->middleware("api", "auth");
Route::post("/rate/create", "RateController@create")->middleware("api", "auth");
Route::post("/rate/create/batch", "RateController@createBatch")->middleware("api", "auth");
Route::post("/rate/update", "RateController@update")->middleware("api", "auth");
Route::post("/rate/delete", "RateController@delete")->middleware("api", "auth");

//task ranking
Route::post("/api/taskReanking/list", "APIController@getTaskRankingList")->middleware("api", "auth");

//communication
Route::post("/api/communication/list", "CommunicationController@list")->middleware("api", "auth");
Route::post("/api/communication/common", "CommunicationController@common")->middleware("api", "auth");
Route::post("/api/communication/delete", "CommunicationController@delete")->middleware("api", "auth");

// user route
Route::post("/api/userRoute/list", "UserRouteController@list")->middleware("api", "auth");
Route::post("/api/userRoute/create", "UserRouteController@create")->middleware("api", "auth");
Route::post("/api/userRoute/create/batch", "UserRouteController@createBatch")->middleware("api", "auth");
Route::post("/api/userRoute/update", "UserRouteController@update")->middleware("api", "auth");
Route::post("/api/userRoute/delete", "UserRouteController@delete")->middleware("api", "auth");
// 
Route::post("/api/manualUserRoute/list", "ManualUserRouteController@list")->middleware("api", "auth");
Route::post("/api/manualUserRoute/create", "ManualUserRouteController@create")->middleware("api", "auth");
Route::post("/api/manualUserRoute/create/batch", "ManualUserRouteController@createBatch")->middleware("api", "auth");
Route::post("/api/manualUserRoute/update", "ManualUserRouteController@update")->middleware("api", "auth");
Route::post("/api/manualUserRoute/delete", "ManualUserRouteController@delete")->middleware("api", "auth");
// point history
Route::post("/api/point/history/list", "PointController@list")->middleware("api", "auth");
Route::post("/api/point/history/total", "PointController@total")->middleware("api", "auth");
Route::post("/api/point/history/update", "PointController@update")->middleware("api", "auth");
//call status
Route::post("/api/callStatus/base", "CallStatusController@base")->middleware("api", "auth");
Route::post("/api/callStatus/modify/calloutGroupID", "CallStatusController@calloutGroupID")->middleware("api", "auth");
Route::post("/api/callStatus/modify/useState", "CallStatusController@useState")->middleware("api", "auth");
Route::post("/api/callStatus/delete/callPlan", "CallStatusController@deleteCallPlan")->middleware("api", "auth");
Route::post("/api/callStatus/update/suspend", "CallStatusController@switchSuspend")->middleware("api", "auth");
Route::post("/api/callStatus/update/maxCalls", "CallStatusController@updateMaxCalls")->middleware("api", "auth");
Route::post("/api/callStatus/update/concurrentCallsAmp", "CallStatusController@updateConcurrentCallsAmp")->middleware("api", "auth");
Route::post("/api/callStatus/update/callWaitingTime", "CallStatusController@updateCallWaitingTime")->middleware("api", "auth");
Route::post("/api/callStatus/update/planDistribution", "CallStatusController@updatePlanDistribution")->middleware("api", "auth");
Route::post("/api/callStatus/callRelease", "CallStatusController@callRelease")->middleware("api", "auth");
Route::post("/api/callStatus/numberList", "CallStatusController@numberList")->middleware("api", "auth");
Route::post("/api/callStatus/waitCall", "CallStatusController@waitCall")->middleware("api", "auth");
Route::post("/api/callStatus/callOut", "CallStatusController@callOut")->middleware("api", "auth");
Route::post("/api/callStatus/callCon", "CallStatusController@callCon")->middleware("api", "auth");
Route::post("/api/callStatus/callMissed", "CallStatusController@callMissed")->middleware("api", "auth");
Route::post("/api/callStatus/callStatistics", "CallStatusController@callStatistics")->middleware("api", "auth");
// group call schedule
Route::post("/api/groupCallSchedule/list", "GroupCallScheduleController@list")->middleware("api", "auth");
Route::post("/api/groupCallSchedule/detail", "GroupCallScheduleController@detail")->middleware("api", "auth");
Route::post("/api/groupCallSchedule/create", "GroupCallScheduleController@create")->middleware("api", "auth");
Route::post("/api/groupCallSchedule/update", "GroupCallScheduleController@update")->middleware("api", "auth");
Route::post("/api/groupCallSchedule/delete", "GroupCallScheduleController@delete")->middleware("api", "auth");
// system
Route::post("/api/system/bulletinBoard/detail", "SystemController@bulletinBoardDetail")->middleware("api", "auth");
Route::post("/api/system/bulletinBoard/insertOrUpdate", "SystemController@bulletinBoardInsertOrUpdate")->middleware("api", "auth");
// extension manage
Route::post("/api/extensionManage/list", "ExtensionManageController@list")->middleware("api", "auth");
Route::post("/api/extensionManage/total", "ExtensionManageController@total")->middleware("api", "auth");
Route::post("/api/extensionManage/delete", "ExtensionManageController@delete")->middleware("api", "auth");
Route::post("/api/extensionManage/detail", "ExtensionManageController@detail")->middleware("api", "auth");
Route::post("/api/extensionManage/create", "ExtensionManageController@create")->middleware("api", "auth");
Route::post("/api/extensionManage/update", "ExtensionManageController@update")->middleware("api", "auth");

// ad group call schedule
Route::post("/api/adGroupCallSchedule/options", "ADGroupCallScheduleController@options")->middleware("api", "auth");
Route::post("/api/adGroupCallSchedule/list", "ADGroupCallScheduleController@list")->middleware("api", "auth");
Route::post("/api/adGroupCallSchedule/detail", "ADGroupCallScheduleController@detail")->middleware("api", "auth");
Route::post("/api/adGroupCallSchedule/create", "ADGroupCallScheduleController@create")->middleware("api", "auth");
Route::post("/api/adGroupCallSchedule/update", "ADGroupCallScheduleController@update")->middleware("api", "auth");
Route::post("/api/adGroupCallSchedule/delete", "ADGroupCallScheduleController@delete")->middleware("api", "auth");
