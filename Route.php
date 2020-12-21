<?php

use comm\Route;

Route::get("/user/echoPassword", "UserController@echoPassword");
Route::post("/user/list", "UserController@list");
Route::post("/user/create", "UserController@create");
Route::post("/user/create/batch", "UserController@createBatch");
Route::post("/user/update", "UserController@update");
Route::post("/user/delete", "UserController@delete");
Route::post("/user/menus", "UserController@menus");
Route::post("/user/detail", "UserController@detail");

// rate manage
Route::post("/rate/list", "RateController@list");
Route::post("/rate/create", "RateController@create");
Route::post("/rate/create/batch", "RateController@createBatch");
Route::post("/rate/update", "RateController@update");
Route::post("/rate/delete", "RateController@delete");

//task ranking
Route::post("/api/taskReanking/list", "APIController@getTaskRankingList");

//communication
Route::post("/api/communication/list", "CommunicationController@list");
Route::post("/api/communication/common", "CommunicationController@common");
Route::post("/api/communication/delete", "CommunicationController@delete");

// user route
Route::post("/api/userRoute/list", "UserRouteController@list");
Route::post("/api/userRoute/create", "UserRouteController@create");
Route::post("/api/userRoute/create/batch", "UserRouteController@createBatch");
Route::post("/api/userRoute/update", "UserRouteController@update");
Route::post("/api/userRoute/delete", "UserRouteController@delete");
// 
Route::post("/api/manualUserRoute/list", "ManualUserRouteController@list");
Route::post("/api/manualUserRoute/create", "ManualUserRouteController@create");
Route::post("/api/manualUserRoute/create/batch", "ManualUserRouteController@createBatch");
Route::post("/api/manualUserRoute/update", "ManualUserRouteController@update");
Route::post("/api/manualUserRoute/delete", "ManualUserRouteController@delete");
// point history
Route::post("/api/point/history/list", "PointController@list");
Route::post("/api/point/history/total", "PointController@total");
Route::post("/api/point/history/update", "PointController@update");
//call status
Route::post("/api/callStatus/base", "CallStatusController@base");
Route::post("/api/callStatus/modify/concurrentCalls", "CallStatusController@concurrentCalls");
Route::post("/api/callStatus/modify/calloutGroupID", "CallStatusController@calloutGroupID");
Route::post("/api/callStatus/modify/useState", "CallStatusController@useState");
Route::post("/api/callStatus/delete/callPlan", "CallStatusController@deleteCallPlan");
Route::post("/api/callStatus/update/suspend", "CallStatusController@switchSuspend");
Route::post("/api/callStatus/update/maxRoutingCalls", "CallStatusController@updateMaxRoutingCalls");
Route::post("/api/callStatus/update/maxCalls", "CallStatusController@updateMaxCalls");
Route::post("/api/callStatus/update/callWaitingTime", "CallStatusController@updateCallWaitingTime");
Route::post("/api/callStatus/update/planDistribution", "CallStatusController@updatePlanDistribution");
Route::post("/api/callStatus/callRelease", "CallStatusController@callRelease");
Route::post("/api/callStatus/numberList", "CallStatusController@numberList");
Route::post("/api/callStatus/waitCall", "CallStatusController@waitCall");
Route::post("/api/callStatus/callOut", "CallStatusController@callOut");
Route::post("/api/callStatus/callCon", "CallStatusController@callCon");
Route::post("/api/callStatus/callFaild", "CallStatusController@callFaild");
Route::post("/api/callStatus/callMissed", "CallStatusController@callMissed");
