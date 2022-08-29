<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/admin/login',[AuthController::class,'adminLogin']);
Route::post('/user/login',[AuthController::class,'userLogin']);
Route::middleware(['auth:sanctum', 'ability:admin,user'])->group(function(){
    Route::get('/logout',[AuthController::class,'logout']);
    Route::get('/me',[AuthController::class,'getUserByToken']);
    Route::patch('/user/{user}/phone',[UserController::class,'changePhone']);
    Route::get('/customer',[CustomerController::class,'get']);
    Route::post('/customer',[CustomerController::class,'store']);
    Route::put('/customer/{customer}',[CustomerController::class,'update']);
    Route::post('/order/customer',[OrderController::class,'storeWithNewCustomer']);
    Route::post('/order/customer/{customer_id}',[OrderController::class,'storeWithOldCustomer']);
    Route::patch('/order/customer/{customer}',[OrderController::class,'storeAndUpdateCustomer']);
    Route::get('/order/{order}',[OrderController::class,'get']);
    Route::put('/order/{order}',[OrderController::class,'update']);
});
Route::middleware(['auth:sanctum', 'ability:admin,staff-manage'])->group(function(){
    Route::post('/user',[UserController::class,'store']);
    Route::delete('/user/{user}',[UserController::class,'destroy']);
    Route::get('/users',[UserController::class,'getAll']);
    Route::put('/user/{id}/rules',[UserController::class,'changeRule']);
});
Route::middleware(['auth:sanctum', 'ability:admin,material-manage'])->group(function(){
    Route::post('/material',[MaterialController::class,'store']);
    Route::delete('/material/{material}',[MaterialController::class,'destroy']);
    Route::get('/materials',[MaterialController::class,'getAll']);
    Route::put('/material/{material}',[MaterialController::class,'update']);
});
Route::middleware(['auth:sanctum', 'ability:admin,meal-manage'])->group(function(){
    Route::post('/meal',[MealController::class,'store']);
    Route::delete('/meal/{meal}',[MealController::class,'destroy']);
    Route::get('/meals',[MealController::class,'getAll']);
    Route::put('/meal/{meal}',[MealController::class,'update']);
});
Route::middleware(['auth:sanctum', 'ability:admin,customer-manage'])->group(function(){
    Route::get('/customers',[CustomerController::class,'getAll']);
});
Route::middleware(['auth:sanctum', 'ability:admin,order-manage'])->group(function(){
    Route::get('/orders',[OrderController::class,'getAll']);
});