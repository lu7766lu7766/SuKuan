<?php

use comm\Route;

Route::get("/user/create", "UserController@create");

// rate manage
Route::post("/rate/list", "RateController@list");
Route::post("/rate/create", "RateController@create");
Route::post("/rate/update", "RateController@update");
Route::post("/rate/delete", "RateController@delete");

//task ranking
Route::post("/api/taskReanking/list", "APIController@getTaskRankingList");
