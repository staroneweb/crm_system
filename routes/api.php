<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\LeadStatusController;
use App\Http\Controllers\MeetingController;

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


// forget password

Route::post('forgot/password',[ForgotPasswordController::class,'sendResetLinkEmail']);
Route::post('reset/password', [ResetPasswordController::class, 'reset'])->name('password.reset');


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
    Route::get('user/name/list',[UserController::class,'userNameList']);

    
    // Contacts

    Route::post('contact/add',[ContactController::class,'contactAdd']);
    Route::post('contact/edit',[ContactController::class,'contactEdit']);
    Route::post('contact/update',[ContactController::class,'contactUpdate']);
    Route::post('contact/delete',[ContactController::class,'contactDelete']);
    // Route::post('contact/status',[ContactController::class,'contactStatus']);
    Route::post('contact/search/list',[ContactController::class,'contactList']);
    Route::get('contact/name/list',[ContactController::class,'contactNameList']);

    // profile data

    Route::post('profile/show',[AuthController::class,'profileShow']);
    Route::post('profile/update',[AuthController::class,'profileUpdate']);

    // Task

    Route::post('task/add',[TaskController::class,'taskAdd']);
    Route::post('task/edit',[TaskController::class,'taskEdit']);
    Route::post('task/update',[TaskController::class,'taskUpdate']);
    Route::post('task/delete',[TaskController::class,'taskDelete']);
    Route::post('task/status',[TaskController::class,'taskStatus']);
    Route::post('task/search/list',[TaskController::class,'taskList']);

    // Sales

    Route::post('sales/add',[SalesController::class,'salesAdd']);
    Route::post('sales/edit',[SalesController::class,'salesEdit']);
    Route::post('sales/update',[SalesController::class,'salesUpdate']);
    Route::post('sales/delete',[SalesController::class,'salesDelete']);
    Route::post('sales/search/list',[SalesController::class,'salesList']);

    // Lead source

    Route::post('source/add',[SourceController::class,'sourceAdd']);
    Route::post('source/edit',[SourceController::class,'sourceEdit']);
    Route::post('source/update',[SourceController::class,'sourceUpdate']);
    Route::post('source/delete',[SourceController::class,'sourceDelete']);
    Route::post('source/search/list',[SourceController::class,'sourceList']);
    Route::post('source/status/update',[SourceController::class,'sourceStatus']);
    Route::get('source/name/list',[SourceController::class,'sourceNameList']);

    // Lead stage

    Route::post('stage/add',[StageController::class,'stageAdd']);
    Route::post('stage/edit',[StageController::class,'stageEdit']);
    Route::post('stage/update',[StageController::class,'stageUpdate']);
    Route::post('stage/delete',[StageController::class,'stageDelete']);
    Route::post('stage/search/list',[StageController::class,'stageList']);
    Route::post('stage/status/update',[StageController::class,'stageStatus']);
    Route::get('stage/name/list',[StageController::class,'stageNameList']);

    // Lead status

    Route::post('status/add',[LeadStatusController::class,'statusAdd']);
    Route::post('status/edit',[LeadStatusController::class,'statusEdit']);
    Route::post('status/update',[LeadStatusController::class,'statusUpdate']);
    Route::post('status/delete',[LeadStatusController::class,'statusDelete']);
    Route::post('status/search/list',[LeadStatusController::class,'statusList']);
    Route::post('status',[LeadStatusController::class,'Status']);   // active and inactive status
    Route::get('status/name/list',[LeadStatusController::class,'statusNameList']);


    // leads

    Route::prefix('leads')->group(function () {
        Route::post('/list', [LeadController::class, 'index']); // List all leads
        Route::post('/store', [LeadController::class, 'store']); // Add a lead
        Route::post('/show', [LeadController::class, 'show']); // Show lead details
        Route::post('/fetch', [LeadController::class, 'edit']); // edit lead details
        Route::post('/delete', [LeadController::class, 'destroy']); // Delete lead
        Route::post('/update', [LeadController::class, 'update']); // Update lead

    });

    // notification

    Route::post('/notifications-add', [NotificationController::class, 'store']);
    Route::post('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications-show', [NotificationController::class, 'show']);
    Route::post('/notifications-update', [NotificationController::class, 'update']);
    Route::post('/notifications-destroy', [NotificationController::class, 'destroy']);


       // meetings
    Route::post('/meetings/list', [MeetingController::class, 'index']); // Get all meetings
    Route::post('/meetings/create', [MeetingController::class, 'store']); // Create a meeting
    Route::post('/meetings/detail', [MeetingController::class, 'show']); // Get a single meeting
    Route::post('/meetings/update', [MeetingController::class, 'update']); // Update a meeting
    Route::post('/meetings/delete', [MeetingController::class, 'destroy']); // Delete a meeting

});


