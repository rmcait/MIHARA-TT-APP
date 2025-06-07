<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Employee\TableController;
use App\Http\Controllers\UserController;

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/employee/tables', [TableController::class, 'index'])->name('employee.tables');
Route::post('/employee/tables/{id}/toggle', [TableController::class, 'toggleStatus'])->name('employee.toggle');

// routes/web.php または routes/api.php に追加

Route::get('/employee/tables/statuses', [TableController::class, 'getTableStatuses']);
Route::get('/employee/waiting-lists/statuses', [TableController::class, 'getWaitingListStatuses']);
Route::get('/employee/timeslot-context', [TableController::class, 'getTimeSlotContext']);
Route::post('/employee/waiting-lists/{id}/toggle', [TableController::class, 'toggleWaitingStatus']);

// web.php
Route::get('/user/tables', [UserController::class, 'index']);
Route::get('/', [UserController::class, 'index']);
Route::get('/user/time-slot-context', [UserController::class, 'getTimeSlotContext']);
Route::get('/user/fetch-tables', [UserController::class, 'fetchTableStatuses']);
Route::get('/user/fetch-waitinglists', [UserController::class, 'fetchWaitingLists']);

Route::post('/user/trigger-auto-update', [UserController::class, 'triggerAutoUpdate']);

// routes/web.php に追加
Route::post('/employee/closed-today', [TableController::class, 'setClosedToday']);
// routes/web.php
Route::post('/employee/unset-closed-today', [TableController::class, 'unsetClosedToday']);

// routes/web.php
Route::get('/employee/closed-today-check', [TableController::class, 'getClosedToday']);




require __DIR__.'/auth.php';