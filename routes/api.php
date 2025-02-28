<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\LeadController;


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
    
    // Contacts

    Route::post('contact/add',[ContactController::class,'contactAdd']);
    Route::post('contact/edit',[ContactController::class,'contactEdit']);
    Route::post('contact/update',[ContactController::class,'contactUpdate']);
    Route::post('contact/delete',[ContactController::class,'contactDelete']);
    // Route::post('contact/status',[ContactController::class,'contactStatus']);
    Route::post('contact/search/list',[ContactController::class,'contactList']);


    // forget password

    Route::post('forgot/password',[ForgotPasswordController::class,'sendResetLinkEmail']);
    Route::post('reset/password', [ResetPasswordController::class, 'reset'])->name('password.reset');

    // profile data

    Route::post('profile/show',[AuthController::class,'profileShow']);
    Route::post('profile/update',[AuthController::class,'profileUpdate']);

    Route::prefix('leads')->group(function () {
        Route::get('/', [LeadController::class, 'index']); // List all leads
        Route::post('/store', [LeadController::class, 'store']); // Add a lead
        Route::get('/{id}', [LeadController::class, 'show']); // Show lead details
        Route::delete('/{id}', [LeadController::class, 'destroy']); // Delete lead
        Route::put('/{id}', [LeadController::class, 'update']); // Update lead

    });

});


