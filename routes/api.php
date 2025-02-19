<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

#ADMIN LOGIN
Route::post('login',[AdminController::class,'login']);



Route::middleware('auth:sanctum')->group(function () {
//admin routes
Route::post('create-user',[AdminController::class,'create_user']);
Route::post('update-user',[AdminController::class,'update_user']);
Route::get('view-users',[AdminController::class,'read_all_users']);
Route::get('view-user/{id}',[AdminController::class,'read_user']);
Route::post('delete-user',[AdminController::class,'delete_user']);

//LoT Routes
Route::post('create-device',[AdminController::class,'add_LoT']);
Route::post('update-device',[AdminController::class,'update_LoT']);
Route::post('delete-device',[AdminController::class,'deletw_LoT']);
Route::get('view-devices',[AdminController::class,'read_all_LoT']);
Route::get('view-device/{id}',[AdminController::class,'read_LoT']);

//LoT condition
Route::post('create-device-condition',[AdminController::class,'create_LoT_condition']);
Route::post('delete-device-condition',[AdminController::class,'delete_LoT_condition']);
Route::post('update-device-condition',[AdminController::class,'update_LoT_condtion']);
Route::get('view-device-condition/{id}',[AdminController::class,'view_LoT_condition']);

//logistics routes
Route::post('update-route',[AdminController::class,'update_route']);
Route::post('delete-route',[AdminController::class,'delete_route']);
Route::post('view-route',[AdminController::class,'view_route']);
Route::post('view-routes',[AdminController::class,'view-routes']);

});