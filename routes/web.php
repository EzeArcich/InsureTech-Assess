<?php

use App\Http\Controllers\AdminClaimController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CoordinacionesController;
use App\Http\Controllers\DataTableController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\SiniestroController;
use App\Http\Controllers\THController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('welcome');

Auth::routes();

Route::prefix('contact')->name('contact.')->group(function (): void {
    Route::view('/', 'contactForm')->name('create');
    Route::post('/', [ContactController::class, 'send'])->name('send');
});

Route::middleware(['auth', \App\Http\Middleware\RestrictInspectorAccess::class])->group(function (): void {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::post('/users/{user}/impersonate', \App\Http\Controllers\ImpersonationController::class)
        ->whereNumber('user')->name('users.impersonate');
    Route::resource('users', UsuarioController::class);
    Route::resource('roles', RolController::class);
    Route::resource('blogs', BlogController::class);

    Route::get('/route-sheets', [\App\Http\Controllers\RouteSheetController::class, 'index'])->name('route-sheets.index');
    Route::put('/route-sheets/{claim}/assignment', [\App\Http\Controllers\RouteSheetController::class, 'assign'])->name('route-sheets.assign');
    Route::post('/route-sheets/{claim}/geocode', \App\Http\Controllers\InspectionGeocodingController::class)->name('route-sheets.geocode');
    Route::put('/route-sheets/{claim}/location', [\App\Http\Controllers\RouteSheetController::class, 'location'])->name('route-sheets.location');

    Route::get('/inspection-payments', [\App\Http\Controllers\InspectionPaymentController::class, 'index'])->name('inspection-payments.index');
    Route::get('/inspection-payments/{payment}', [\App\Http\Controllers\InspectionPaymentController::class, 'show'])->name('inspection-payments.show');
    Route::post('/inspection-payments/claims/{claim}/resolve', [\App\Http\Controllers\InspectionPaymentController::class, 'resolve'])->name('inspection-payments.resolve');
    Route::post('/inspection-payments/{payment}/pay', [\App\Http\Controllers\InspectionPaymentController::class, 'pay'])->name('inspection-payments.pay');

    Route::prefix('workshops')->name('workshops.')->group(function (): void {
        Route::get('/{id}/data', [SiniestroController::class, 'tallerData'])->whereNumber('id')->name('data');
    });
    Route::get('/workshops/search', [\App\Http\Controllers\InspectionWorkshopController::class, 'search'])->name('workshops.search');
    Route::resource('workshops', THController::class);
    Route::get('/parts/search', [\App\Http\Controllers\PartController::class, 'search'])->name('parts.search');
    Route::middleware('role:CEO')->prefix('settings')->group(function (): void {
        Route::put('/companies/{company}/inspection-rates', [\App\Http\Controllers\InspectionRateController::class, 'update'])->name('settings.inspection-rates.update');
        Route::get('/rates', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.rates');
        Route::get('/repair-rates', [\App\Http\Controllers\CompanyRateController::class, 'index'])->name('settings.repair-rates');
        Route::put('/repair-rates', [\App\Http\Controllers\CompanyRateController::class, 'apply'])->name('settings.repair-rates.apply');
        Route::get('/inspection-rates', [\App\Http\Controllers\InspectionRateController::class, 'index'])->name('settings.inspection-rates');
        Route::put('/inspection-rates', [\App\Http\Controllers\InspectionRateController::class, 'apply'])->name('settings.inspection-rates.apply');
        Route::put('/companies/{company}/rates', [\App\Http\Controllers\CompanyRateController::class, 'update'])->name('settings.rates.update');
    });

    Route::get('/clients/{client}', [\App\Http\Controllers\ClientController::class, 'show'])->whereNumber('client')->name('clients.show');
    Route::get('/inspectors/{inspector}', [\App\Http\Controllers\InspectorController::class, 'show'])->whereNumber('inspector')->name('inspectors.show');

    Route::get('/inspectors/data', [SiniestroController::class, 'allUserData'])->name('inspectors.data');
    Route::get('/inspectors/{id}/data', [SiniestroController::class, 'userData'])->whereNumber('id')->name('inspectors.show.data');
    Route::get('/producers/{id}/data', [SiniestroController::class, 'productoresData'])->whereNumber('id')->name('producers.data');

    Route::prefix('claims')->name('claims.')->group(function (): void {
        Route::middleware('can:view-all-claims')->group(function (): void {
            Route::get('/all', [AdminClaimController::class, 'index'])->name('all');
            Route::get('/all/data', [AdminClaimController::class, 'data'])->name('all.data');
        });

        Route::get('/pending', [SiniestroController::class, 'pendientes'])->name('pending');
        Route::get('/absent', [SiniestroController::class, 'ausentes'])->name('absent');
        Route::get('/coordinated', [SiniestroController::class, 'coordinados'])->name('coordinated');
        Route::get('/assigned', [SiniestroController::class, 'peritos'])->name('assigned');
        Route::get('/in-progress', [SiniestroController::class, 'peritosgestion'])->name('in_progress');
        Route::get('/quotes', [SiniestroController::class, 'cotizaciones'])->name('quotes');
        Route::get('/third-parties', [SiniestroController::class, 'terceros'])->name('third_parties');
        Route::get('/referrals', [SiniestroController::class, 'derivaciones'])->name('referrals');
        Route::get('/assignments', [SiniestroController::class, 'asignaciones'])->name('assignments');
        Route::get('/assignments/data', [SiniestroController::class, 'allData'])->name('assignments.data');
        Route::get('/datatable', [DataTableController::class, 'paraderivar'])->name('datatable');
        Route::post('/{claim}/location-search', \App\Http\Controllers\InspectionGeocodingController::class)->whereNumber('claim')->name('location.search');
        Route::post('/check', [SiniestroController::class, 'check'])->name('check');
        Route::post('/data', [SiniestroController::class, 'storeData'])->name('data.store');
        Route::get('/{id}/data', [SiniestroController::class, 'editData'])->whereNumber('id')->name('data.show');
        Route::put('/{id}/data', [SiniestroController::class, 'updateData'])->whereNumber('id')->name('data.update');
        Route::get('/{claim}/inspection', [SiniestroController::class, 'inspection'])->name('inspection');
        Route::get('/{claim}/parts', [\App\Http\Controllers\PartController::class, 'show'])->name('parts');
        Route::post('/{claim}/workshops', [\App\Http\Controllers\InspectionWorkshopController::class, 'store'])->name('workshops.store');
        Route::get('/{claim}/refer', [SiniestroController::class, 'derivar'])->whereNumber('claim')->name('refer');
    });
    Route::resource('claims', SiniestroController::class)->whereNumber('claim');

    Route::prefix('coordination')->name('coordination.')->group(function (): void {
        Route::get('/contact', [CoordinacionesController::class, 'index'])->name('contact.index');
        Route::post('/contact', [CoordinacionesController::class, 'store'])->name('contact.store');
        Route::post('/producer-email', [CoordinacionesController::class, 'contacto'])->name('producer_email');
        Route::post('/emails', [CoordinacionesController::class, 'correo'])->name('emails.store');
        Route::post('/inspection-emails', [CoordinacionesController::class, 'correoEdu'])->name('inspection_emails.store');
    });
});
