<?php

use App\Http\Controllers\Auth\AuthProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthProfileController::class, 'edit'])
    ->name('edit');

Route::patch('/', [AuthProfileController::class, 'update'])
    ->name('update');

Route::put('/senha', [AuthProfileController::class, 'updatePassword'])
    ->name('update');

Route::delete('/{id}', [AuthProfileController::class, 'destroy'])
    ->name('destroy');