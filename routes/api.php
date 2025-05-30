<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaffController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::middleware([TestMiddleware::class])->get('/test', function () {
// });
Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[RegisterController::class,'register']);


Route::middleware('auth:sanctum')->group(function () {
Route::post('/user',[UserController::class,'user']);
Route::put('/update-user/{id}', [UserController::class, 'updateuser']);
Route::get('/user/{id}',[UserController::class, 'getUserById']);
Route::resource('/staff',StaffController::class);



Route::post('/change-password',[AuthController::class,'changePassword']);
Route::post('/logout', [AuthController::class, 'logout']);
});