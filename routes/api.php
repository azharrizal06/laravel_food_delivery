<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//user register
Route::post('/user/register', [App\Http\Controllers\AuthController::class, 'userRegister']);
//register restaurant
Route::post('/restaurant/register', [App\Http\Controllers\AuthController::class, 'registerrestaurant']);
//register driver
Route::post('/driver/register', [App\Http\Controllers\AuthController::class, 'registerDriver']);
//login
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
//logout
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');
//updateLatLong
Route::post('/user/updateLatLong', [App\Http\Controllers\AuthController::class, 'updateLatLong'])->middleware('auth:sanctum');
//get all restaurants
Route::get('/restaurants', [App\Http\Controllers\AuthController::class, 'getAllRestaurant']);
//get products by restaurant
Route::apiResource('/products', App\Http\Controllers\ProductsController::class)->middleware('auth:sanctum');
