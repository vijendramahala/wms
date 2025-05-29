<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::middleware([TestMiddleware::class])->get('/test', function () {
// });
Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[RegisterController::class,'register']);


Route::middleware('auth:sanctum')->group(function () {
Route::post('/user',[RegisterController::class,'user']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/change-password',[AuthController::class,'changePassword']);
});