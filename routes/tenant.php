<?php

declare(strict_types=1);

use App\Http\Controllers\PatientController;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\Tenant\TenantAuthController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', [PublicFormController::class, 'show'])->name('public_form.show');
    Route::post('/public-form', [PublicFormController::class, 'store'])->name('public_form.store');

    Route::get('/admin/login', [TenantAuthController::class, 'showLoginForm'])->name('tenant.login');
    Route::post('/admin/login', [TenantAuthController::class, 'login']);

    Route::middleware('auth')->group(function () {
        Route::post('/admin/logout', [TenantAuthController::class, 'logout'])->name('tenant.logout');

        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/report', [PatientController::class, 'reportPdf'])->name('patients.report');
        Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{patient}/pdf', [PatientController::class, 'downloadPdf'])->name('patients.pdf');
    });
});

Route::middleware('tenant')->get('/teste', function () {
    return [
        'host' => request()->getHost(),
        'tenant' => tenant('id'),
        'db' => config('database.connections.tenant.database'),
    ];
});