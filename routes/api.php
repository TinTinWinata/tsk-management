<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiGoogleScheduleController;
use App\Http\Controllers\Api\ApiNoteController;
use App\Http\Controllers\Api\ApiNotificationController;
use App\Http\Controllers\Api\ApiScheduleController;
use App\Http\Controllers\Api\ApiSpaceController;
use App\Http\Controllers\Api\ApiSpaceLogController;
use App\Http\Controllers\Api\ApiUserController;
use App\Http\Controllers\LineController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello World!']);
});
Route::post('/line-webhook', [LineController::class, 'webhook']);
Route::get('/reminder', [ScheduleController::class, 'reminder']);
Route::controller(ApiAuthController::class)->group(function () {
    Route::post('/login', 'login');
    Route::post('/register', 'register');
    Route::post('/auth/google', 'googleAuth');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(LineController::class)->group(function () {
        Route::get('/line/test', 'test');
    });
    Route::controller(ApiGoogleScheduleController::class)->group(function() {
        Route::post('/google-schedule', 'store');
        Route::get('/google/test', 'test');
    });
    Route::controller(ApiScheduleController::class)->group(function () {
        Route::post('/schedule/save', 'save');
        Route::get('/schedule', 'index');
        Route::post('/schedule/ai', 'storeAi');
    });
    Route::controller(ApiAuthController::class)->group(function () {
        Route::get('/me', 'me');
    });
    Route::controller(ApiNotificationController::class)->group(function () {
        Route::post('/approve/{notification}', 'approve');
        Route::post('/reject/{notification}', 'reject');
        Route::get('/notification', 'index');
    });
    Route::controller(ApiSpaceController::class)->group(function () {
        Route::get('/space', 'index');
        Route::post('/space', 'store');
        Route::get('/space/schedule/{space}', 'schedules');
        Route::patch('/space/{space}', 'update');
        Route::delete('/space/{space}', 'delete');
    });
    Route::controller(ApiSpaceLogController::class)->group(function () {
        Route::get('/space-log/{id}', 'show');
    });
    Route::controller(ApiUserController::class)->group(function () {
        Route::get('/user', 'index');
        Route::post('/user/update-is-sync-google', 'updateIsSyncGoogle');
        Route::post('/user/update-profile', 'updateProfile');
        Route::post('/user/update-password', 'updatePassword');
    });
    Route::controller(ApiNoteController::class)->group(function () {
        Route::get('/note', 'index');
        Route::post('/note', 'store');
        Route::patch('/note/{note}', 'update');
        Route::delete('/note/{note}', 'delete');
    });
});

