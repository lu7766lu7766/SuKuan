<?php

use comm\Route;

Route::get("/user/echoPassword", "UserController@echoPassword");
Route::post("/user/list", "UserController@list")->middleware("api");
Route::post("/user/create", "UserController@create")->middleware("api");
Route::post("/user/create/batch", "UserController@createBatch")->middleware("api");
Route::post("/user/update", "UserController@update")->middleware("api");
Route::post("/user/delete", "UserController@delete")->middleware("api");
Route::post("/user/menus", "UserController@menus")->middleware("api");
Route::post("/user/detail", "UserController@detail")->middleware("api");

// rate manage
Route::post("/rate/list", "RateController@list")->middleware("api");
Route::post("/rate/create", "RateController@create");
Route::post("/rate/create/batch", "RateController@createBatch")->middleware("api");
Route::post("/rate/update", "RateController@update")->middleware("api");
Route::post("/rate/delete", "RateController@delete")->middleware("api");

//task ranking
Route::post("/api/taskReanking/list", "APIController@getTaskRankingList")->middleware("api");

//communication
Route::post("/api/communication/list", "CommunicationController@list")->middleware("api");
Route::post("/api/communication/common", "CommunicationController@common")->middleware("api");
Route::post("/api/communication/delete", "CommunicationController@delete")->middleware("api");

// user route
Route::post("/api/userRoute/list", "UserRouteController@list")->middleware("api");
Route::post("/api/userRoute/create", "UserRouteController@create")->middleware("api");
Route::post("/api/userRoute/create/batch", "UserRouteController@createBatch")->middleware("api");
Route::post("/api/userRoute/update", "UserRouteController@update")->middleware("api");
Route::post("/api/userRoute/delete", "UserRouteController@delete")->middleware("api");
// 
Route::post("/api/manualUserRoute/list", "ManualUserRouteController@list")->middleware("api");
Route::post("/api/manualUserRoute/create", "ManualUserRouteController@create")->middleware("api");
Route::post("/api/manualUserRoute/create/batch", "ManualUserRouteController@createBatch")->middleware("api");
Route::post("/api/manualUserRoute/update", "ManualUserRouteController@update")->middleware("api");
Route::post("/api/manualUserRoute/delete", "ManualUserRouteController@delete")->middleware("api");
// point history
Route::post("/api/point/history/list", "PointController@list")->middleware("api");
Route::post("/api/point/history/total", "PointController@total")->middleware("api");
Route::post("/api/point/history/update", "PointController@update")->middleware("api");
//call status
Route::post("/api/callStatus/base", "CallStatusController@base")->middleware("api");
Route::post("/api/callStatus/modify/calloutGroupID", "CallStatusController@calloutGroupID")->middleware("api");
Route::post("/api/callStatus/modify/useState", "CallStatusController@useState")->middleware("api");
Route::post("/api/callStatus/delete/callPlan", "CallStatusController@deleteCallPlan")->middleware("api");
Route::post("/api/callStatus/update/suspend", "CallStatusController@switchSuspend")->middleware("api");
Route::post("/api/callStatus/update/maxCalls", "CallStatusController@updateMaxCalls")->middleware("api");
Route::post("/api/callStatus/update/concurrentCallsAmp", "CallStatusController@updateConcurrentCallsAmp")->middleware("api");
Route::post("/api/callStatus/update/callWaitingTime", "CallStatusController@updateCallWaitingTime")->middleware("api");
Route::post("/api/callStatus/update/planDistribution", "CallStatusController@updatePlanDistribution")->middleware("api");
Route::post("/api/callStatus/callRelease", "CallStatusController@callRelease")->middleware("api");
Route::post("/api/callStatus/numberList", "CallStatusController@numberList")->middleware("api");
Route::post("/api/callStatus/waitCall", "CallStatusController@waitCall")->middleware("api");
Route::post("/api/callStatus/callOut", "CallStatusController@callOut")->middleware("api");
Route::post("/api/callStatus/callCon", "CallStatusController@callCon")->middleware("api");
Route::post("/api/callStatus/callMissed", "CallStatusController@callMissed")->middleware("api");
Route::post("/api/callStatus/callStatistics", "CallStatusController@callStatistics")->middleware("api");
// group call schedule
Route::post("/api/groupCallSchedule/list", "GroupCallScheduleController@list")->middleware("api");
Route::post("/api/groupCallSchedule/detail", "GroupCallScheduleController@detail")->middleware("api");
Route::post("/api/groupCallSchedule/create", "GroupCallScheduleController@create")->middleware("api");
Route::post("/api/groupCallSchedule/update", "GroupCallScheduleController@update")->middleware("api");
Route::post("/api/groupCallSchedule/delete", "GroupCallScheduleController@delete")->middleware("api");
// system
Route::post("/api/system/bulletinBoard/detail", "SystemController@bulletinBoardDetail")->middleware("api");
Route::post("/api/system/bulletinBoard/insertOrUpdate", "SystemController@bulletinBoardInsertOrUpdate")->middleware("api");
// extension manage
Route::post("/api/extensionManage/list", "ExtensionManageController@list")->middleware("api");
Route::post("/api/extensionManage/total", "ExtensionManageController@total")->middleware("api");
Route::post("/api/extensionManage/delete", "ExtensionManageController@delete")->middleware("api");
Route::post("/api/extensionManage/detail", "ExtensionManageController@detail")->middleware("api");
Route::post("/api/extensionManage/create", "ExtensionManageController@create")->middleware("api");
Route::post("/api/extensionManage/update", "ExtensionManageController@update")->middleware("api");
