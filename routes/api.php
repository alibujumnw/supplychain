<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FarmerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\LogisticsController;

#ADMIN LOGIN
Route::post('login',[AdminController::class,'login']);

/**
 * create user
 */
Route::post('farmer-registration',[FarmerController::class,'create_farmer']);
Route::post('supplier-registration',[SupplierController::class,'create_supplier']);
Route::post('logistic-registration',[LogisticsController::class,'create_logistic']);


Route::middleware( 'auth:sanctum')->group(function () {

Route::get('total-warehouse',[AdminController::class,'total_warehouse']);


/*
* admin create users
*/
Route::post('create-user',[AdminController::class,'create_user']);


/*
*update users
*/
Route::post('update-user',[AdminController::class,'update_user']); 
Route::post('update-farmer-details',[FarmerController::class,'edit_farmer']);
Route::post('update-supplier-details',[SupplierController::class,'edit_supplier']);
Route::post('update-logistic-details',[LogisticsController::class,'edit_logistic']);


/*
 *View users 
 */
Route::get('view-all-users/{type}',[AdminController::class,'view_all_users']);
Route::get('view-suppliers',[SupplierController::class,'view_suppliers']);
Route::get('view-farmers/{userId}',[FarmerController::class,'view_farmers']);
Route::get('view-logistic',[LogisticsController::class,'view_logistic']);
Route::get('account-details',[AdminController::class,'account_details']);


/**
 * Delete users
 */
Route::get('delete-user/{id}',[AdminController::class,'delete_user']);
Route::get('delete-farmer/{id}',[FarmerController::class,'delete_farmer']);
Route::post('delete-supplier',[SupplierController::class,'delete_supplier']);
Route::post('delete-logistic',[LogisticsController::class,'delete_logistic']);

//IoT Routes
Route::post('create-device',[AdminController::class,'add_LoT']);
Route::post('update-device',[AdminController::class,'update_LoT']);
Route::get('delete-device/{id}',[AdminController::class,'delete_LoT']);
Route::get('view-devices',[AdminController::class,'read_all_LoT']);
Route::get('view-device/{id}',[AdminController::class,'read_LoT']);

//IoT condition
Route::post('create-device-condition',[AdminController::class,'create_LoT_condition']);
Route::get('delete-device-condition/{id}',[AdminController::class,'delete_LoT_condition']);
Route::post('update-device-condition',[AdminController::class,'update_LoT_condtion']);
Route::get('view-device-condition/{id}',[AdminController::class,'view_LoT_condition']);

//logistics routes
Route::post('update-route',[AdminController::class,'update_route']);
Route::get('delete-route/{id}',[AdminController::class,'delete_route']);
Route::post('view-route',[AdminController::class,'view_route']);
Route::get('view-routes',[AdminController::class,'view_routes']);

/**
 * FarmerController
 */
Route::post('change-password',[AdminController::class,'change_password']);



     
Route::get('delete-crop/{id}',[FarmerController::class,'delete_crop']);
Route::post('update-status',[FarmerController::class,'updateStatus']);
Route::get('show-status',[FarmerController::class,'show']);



/**
 * logistics
 */

 /**
  * new routes
  */

Route::post('create-crop',[FarmerController::class,'create_crop']);
Route::post('update-crop',[FarmerController::class,'update_crop']);
Route::post('create-livestock',[FarmerController::class,'create_livestock']);
  

Route::get('view-crop/{id}',[FarmerController::class,'view_crop']);


Route::get('view-livestock/{id}',[FarmerController::class,'view_livestock']);

Route::post('create-warehouse',[FarmerController::class,'create_warehouse']);

Route::get('view-warehouse/{id}',[FarmerController::class,'view_warehouse']);

Route::get('warehouse/{id}',[FarmerController::class,'warehouse_products']);

//logistic Routes
Route::post('create-Route',[LogisticsController::class,'create_route']);

});