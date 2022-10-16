<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
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
    Route::patch('/change-password',[AuthController::class,'changePassword']);
    Route::get('/user/{user}',[UserController::class,'get']);
    Route::get('/customer',[CustomerController::class,'get']);
    Route::post('/customer',[CustomerController::class,'store']);
    Route::put('/customer/{customer}',[CustomerController::class,'update']);
    Route::post('/order',[OrderController::class,'store']);
    Route::get('/order/{id}',[OrderController::class,'get']);
    Route::get('/order/phone/{phone}',[OrderController::class,'getByPhone']);
    Route::get('/meals/new',[MealController::class,'getNewMeal']);
    Route::get('/meals/best',[MealController::class,'getBestSellerMeal']);
    Route::get('/meals',[MealController::class,'getAll']);
    Route::get('/orders/{state}',[OrderController::class,'getByState']);
    Route::get('/orders',[OrderController::class,'getAll']);
    Route::patch('/order/success/{order}',[OrderController::class,'success']);
    Route::patch('/order/fail/{order}',[OrderController::class,'fail']);
    Route::patch('/order/cancel/{order}',[OrderController::class,'cancel']);
});
Route::middleware(['auth:sanctum','ability:admin,staff-manage:delete'])->group(function(){
    Route::delete('/user/{user}',[UserController::class,'destroy']);
});
Route::middleware(['auth:sanctum', 'ability:admin,staff-manage:create'])->group(function(){
    Route::post('/user',[UserController::class,'store']);
});
Route::middleware(['auth:sanctum', 'ability:admin,staff-manage:update'])->group(function(){
    Route::put('/user/{user}',[UserController::class,'update']);
});
Route::middleware(['auth:sanctum', 'ability:admin,staff-manage:update,staff-manage:create,staff-manage:delete'])->group(function(){
    Route::get('/users',[UserController::class,'getAll']);
});
Route::middleware(['auth:sanctum', 'ability:admin,meal-manage:create'])->group(function(){
    Route::post('/meal',[MealController::class,'store']);
});
Route::middleware(['auth:sanctum', 'ability:admin,meal-manage:delete'])->group(function(){
    Route::delete('/meal/{meal}',[MealController::class,'destroy']);
});
Route::middleware(['auth:sanctum', 'ability:admin,meal-manage:update'])->group(function(){
    Route::put('/meal/{meal}',[MealController::class,'update']);
});
Route::middleware(['auth:sanctum', 'ability:admin,customer-manage'])->group(function(){
    Route::get('/customers',[CustomerController::class,'getAll']);
    Route::get('/customer/{customer}',[CustomerController::class,'getById']);
});