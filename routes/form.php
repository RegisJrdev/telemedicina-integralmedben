<?php

use App\Http\Controllers\Form\IndexFormController;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexFormController::class)
    ->name('index')
    ->middleware('permission:forms.view');