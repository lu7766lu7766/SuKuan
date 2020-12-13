<?php

use comm\Route;

Route::get("/user/echoPassword", "UserController@echoPassword");
Route::post("/user/list", "UserController@list");
Route::post("/user/delete", "UserController@delete");
Route::post("/user/menus", "UserController@menus");
Route::post("/user/detail", "UserController@detail");

// rate manage
Route::post("/rate/list", "RateController@list");
Route::post("/rate/create", "RateController@create");
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
Route::post("/api/userRoute/update", "UserRouteController@update");
Route::post("/api/userRoute/delete", "UserRouteController@delete");
// 
Route::post("/api/manualUserRoute/list", "ManualUserRouteController@list");
Route::post("/api/manualUserRoute/create", "ManualUserRouteController@create");
Route::post("/api/manualUserRoute/update", "ManualUserRouteController@update");
Route::post("/api/manualUserRoute/delete", "ManualUserRouteController@delete");
// point history
Route::post("/api/point/history/list", "PointController@list");
Route::post("/api/point/history/total", "PointController@total");
Route::post("/api/point/history/update", "PointController@update");
