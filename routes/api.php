<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;

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

Route::post('login',[AuthController::class,'login']);
Route::post('logout',[AuthController::class,'logout'])->middleware('auth.Token');

// Route::middleware(['auth.Token','role'])->group(function () {
Route::middleware(['auth.Token'])->group(function () {

    // User Routes

    // Route::post('user/add',[UserController::class,'userAdd'])->middleware('role:admin|sales');
    Route::post('user/add',[UserController::class,'userAdd']);
    Route::post('user/edit',[UserController::class,'userEdit']);
    Route::post('user/update',[UserController::class,'userUpdate']);
    Route::post('user/delete',[UserController::class,'userDelete']);
    Route::post('user/status',[UserController::class,'userStatus']);
    Route::post('user/search/list',[UserController::class,'userList']);
    // search by

    // first name,last name,email,mobile number


    // Route::post('forgot-password',[AuthController::class,'forgotPassword']);
    // Route::post('password/email',[ForgotPasswordController::class,'sendResetLinkEmail']);
    // Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.reset');



});


