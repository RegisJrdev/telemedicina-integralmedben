<?php
use App\Http\Controllers\Pagina\PaginaCreateController;
use App\Http\Controllers\Pagina\PaginaIndexController;
use App\Http\Controllers\Pagina\PaginaShowController;
use App\Http\Controllers\Pagina\PaginaStoreController;
use Illuminate\Support\Facades\Route;

Route::get('create', PaginaCreateController::class)->name('create');
Route::get('/', PaginaIndexController::class)->name('index');
Route::post('store', PaginaStoreController::class)->name('store');
Route::get('/{tenant}', PaginaShowController::class)->name('show');
