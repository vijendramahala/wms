<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\MiscController;
use App\Http\Controllers\ProspectController;
use App\Http\Controllers\TestController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::middleware([TestMiddleware::class])->get('/test', function () {
// });
Route::get('/test',[TestController::class,'test']);

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[RegisterController::class,'register']);


Route::middleware('auth:sanctum')->group(function () {
Route::post('/user',[UserController::class,'user']);
Route::put('/update-user/{id}', [UserController::class, 'updateuser']);
Route::get('/user/{id}',[UserController::class, 'getUserById']);
Route::resource('/staff',StaffController::class);
Route::resource('/reminder',ReminderController::class);
Route::get('/reminder/fillter/date',[ReminderController::class, 'filterByDate']);

//notice
Route::post('/notice/create',[NoticeController::class, 'store']);
Route::put('/notice/update/{id}',[NoticeController::class, 'update']);
Route::delete('/notice/delete/{id}',[NoticeController::class, 'destroy']);
Route::get('/notice/show/{id}',[NoticeController::class, 'show']);

//note 
Route::post('/notes/create',[NotesController::class,'store']);
Route::PUT('/notes/update/{id}',[NotesController::class,'update']);
Route::delete('/notes/delete/{id}',[NotesController::class,'destroy']);
Route::get('/notes',[NotesController::class,'getByLocationAndStaff']);

//target
Route::post('/target/create',[TargetController::class,'store']);
Route::put('/target/update/{id}',[TargetController::class,'update']);
Route::delete('/target/delete/{id}',[TargetController::class,'destroy']);
Route::get('/target',[TargetController::class,'index']);

//leave
Route::post('/leave/create',[LeaveController::class, 'store']);
Route::put('/leave/update/{id}',[LeaveController::class, 'update']);
Route::get('/leave',[LeaveController::class, 'index']);
Route::get('/leave/staff',[LeaveController::class, 'staffLeaves']);

//misc 
Route::post('/misc/create',[MiscController::class, 'store']);
Route::put('/misc/update/{id}', [MiscController::class, 'update']);
Route::delete('/misc/delete/{id}',[MiscController::class, 'destroy']);
Route::get('/misc',[MiscController::class, 'index']);

//prospact
Route::post('/prospect/create',[ProspectController::class, 'store']);
Route::put('/prospect/update/{id}', [ProspectController::class, 'update']);
Route::delete('/prospect/delete/{id}', [ProspectController::class, 'destroy']);
Route::get('/prospect', [ProspectController::class, 'index']);
Route::get('/prospect/history/{id}', [ProspectController::class, 'history']);
Route::get('/prospect/create_at', [ProspectController::class, 'filterbycreate_at']);
Route::get('/prospect/date', [ProspectController::class, 'filterbydate']);
Route::get('/prospects/priority', [ProspectController::class, 'getByPriority']);
Route::get('/prospect/misc', [ProspectController::class, 'getAllSoftwareWithProspects']);
Route::get('/prospect/status', [ProspectController::class, 'getBystatus']);

Route::post('/change-password',[AuthController::class,'changePassword']);
Route::post('/logout', [AuthController::class, 'logout']);
});