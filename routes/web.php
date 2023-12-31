<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/note', [NoteController::class, 'index'])
        ->name('note');
    Route::post('/note', [NoteController::class, 'store'])
        ->name('note.insert');
    Route::patch('/note/{note}', [NoteController::class, 'update'])
        ->name('note.update');

    Route::delete('/note/{note}', [NoteController::class, 'destroy'])
        ->name('note.destroy');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/reminder', [ScheduleController::class, 'reminder']);

Route::get('/list', [ScheduleController::class, 'indexList'])
    ->name('list');

Route::get('/', [ScheduleController::class, 'index'])
    ->name('dashboard');


Route::get('/schedule', [ScheduleController::class, 'indexMonth'])
    ->name('schedule');



require __DIR__ . '/auth.php';
