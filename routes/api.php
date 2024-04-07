<?php

use App\Http\Controllers\Api\AuthController;
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

Route::post('/line-webhook', [LineController::class, 'webhook']);

Route::get('/reminder', [ScheduleController::class, 'reminder']);
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(LineController::class)->group(function () {
        Route::get('/line/test', 'test');
    });
    Route::controller(ScheduleController::class)->group(function () {
        Route::post('/schedule/save', 'save');
    });
});

Route::post('/login', [AuthController::class, 'login']);
