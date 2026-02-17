<?php

use App\Http\Controllers\TenantController;
use App\Models\Question;
use App\Models\Tenant;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Admin dashboard routes (uses master database)
|
*/

Route::prefix('admin')->name('admin.')->middleware(['web', 'auth'])->group(function () {
    Route::get('/', function () {
        $tenants = Tenant::with('questions')->paginate(10);
        $questions = Question::all();

        return Inertia::render('Dashboard', [
            'tenants' => $tenants,
            'questions' => $questions
        ]);
    })->name('dashboard');

    Route::resource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/questions', [TenantController::class, 'attachQuestions'])
        ->name('tenants.attach-questions');
});