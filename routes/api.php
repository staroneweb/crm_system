<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login',[UserController::class,'login']);
Route::post('logout',[UserController::class,'logout'])->middleware('auth.Token');

// Route::middleware(['auth.Token','role'])->group(function () {
Route::middleware(['auth.Token'])->group(function () {

    // User Routes

    // Route::post('user/add',[UserController::class,'userAdd'])->middleware('role:admin|sales');
    Route::post('user/add',[UserController::class,'userAdd']);
    Route::post('user/edit',[UserController::class,'userEdit']);
    Route::post('user/update',[UserController::class,'userUpdate']);
    Route::post('user/delete',[UserController::class,'userDelete']);
    Route::post('user/status',[UserController::class,'userStatus']);


    Route::post('forgot-password',[AuthController::class,'forgotPassword']);


});


