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
