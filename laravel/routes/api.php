<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MealController;
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
    Route::patch('/user/{user}/phone',[UserController::class,'changePhone']);
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