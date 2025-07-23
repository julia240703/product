<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (No Login Required)
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/motor/{id}', [PublicController::class, 'motorDetail'])->name('motor.detail');
Route::get('/motors/category/{slug}', [PublicController::class, 'motorsByCategory'])->name('motors.category');
Route::get('/compare', [PublicController::class, 'compare'])->name('motors.compare');
Route::get('/accessories', [PublicController::class, 'accessories'])->name('accessories.index');
Route::get('/apparels', [PublicController::class, 'apparels'])->name('apparels.index');
Route::get('/branches', [PublicController::class, 'branches'])->name('branches.index');
Route::get('/price-list', [PublicController::class, 'priceList'])->name('price.list');

Route::get('/test-ride', [PublicController::class, 'showTestRideForm'])->name('test-ride.form');
Route::post('/test-ride', [PublicController::class, 'submitTestRide'])->name('test-ride.submit');

Route::get('/credit-simulation', [PublicController::class, 'showCreditForm'])->name('credit.form');
Route::post('/credit-simulation', [PublicController::class, 'submitCreditSimulation'])->name('credit.submit');

/*
|--------------------------------------------------------------------------
| ADMIN LOGIN (Public - No Auth Middleware)
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Login Required, Must be Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'user-access:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/home', [AdminController::class, 'adminHome'])->name('home');

    // Motors
    Route::get('/motors', [AdminController::class, 'motorsIndex'])->name('motors.index');
    Route::get('/motors/create', [AdminController::class, 'motorsCreate'])->name('motors.create');
    Route::post('/motors', [AdminController::class, 'motorsStore'])->name('motors.store');
    Route::get('/motors/{id}/edit', [AdminController::class, 'motorsEdit'])->name('motors.edit');
    Route::put('/motors/{id}', [AdminController::class, 'motorsUpdate'])->name('motors.update');
    Route::delete('/motors/{id}', [AdminController::class, 'motorsDelete'])->name('motors.delete');

    // Features
    Route::post('/features', [AdminController::class, 'featuresStore'])->name('features.store');
    Route::get('/features/{id}/edit', [AdminController::class, 'featuresEdit'])->name('features.edit');
    Route::put('/features/{id}', [AdminController::class, 'featuresUpdate'])->name('features.update');
    Route::delete('/features/{id}', [AdminController::class, 'featuresDelete'])->name('features.delete');

    // Colors
    Route::post('/colors', [AdminController::class, 'colorsStore'])->name('colors.store');
    Route::get('/colors/{id}/edit', [AdminController::class, 'colorsEdit'])->name('colors.edit');
    Route::put('/colors/{id}', [AdminController::class, 'colorsUpdate'])->name('colors.update');
    Route::delete('/colors/{id}', [AdminController::class, 'colorsDelete'])->name('colors.delete');

    // Specifications
    Route::post('/specs', [AdminController::class, 'specsStore'])->name('specs.store');
    Route::get('/specs/{id}/edit', [AdminController::class, 'specsEdit'])->name('specs.edit');
    Route::put('/specs/{id}', [AdminController::class, 'specsUpdate'])->name('specs.update');
    Route::delete('/specs/{id}', [AdminController::class, 'specsDelete'])->name('specs.delete');

    // Accessories
    Route::post('/accessories', [AdminController::class, 'accessoriesStore'])->name('accessories.store');
    Route::get('/accessories/{id}/edit', [AdminController::class, 'accessoriesEdit'])->name('accessories.edit');
    Route::put('/accessories/{id}', [AdminController::class, 'accessoriesUpdate'])->name('accessories.update');
    Route::delete('/accessories/{id}', [AdminController::class, 'accessoriesDelete'])->name('accessories.delete');

    // Parts
    Route::post('/parts', [AdminController::class, 'partsStore'])->name('parts.store');
    Route::get('/parts/{id}/edit', [AdminController::class, 'partsEdit'])->name('parts.edit');
    Route::put('/parts/{id}', [AdminController::class, 'partsUpdate'])->name('parts.update');
    Route::delete('/parts/{id}', [AdminController::class, 'partsDelete'])->name('parts.delete');

    // Apparels
    Route::post('/apparels', [AdminController::class, 'apparelsStore'])->name('apparels.store');
    Route::get('/apparels/{id}/edit', [AdminController::class, 'apparelsEdit'])->name('apparels.edit');
    Route::put('/apparels/{id}', [AdminController::class, 'apparelsUpdate'])->name('apparels.update');
    Route::delete('/apparels/{id}', [AdminController::class, 'apparelsDelete'])->name('apparels.delete');

    // Branches
    Route::post('/branches', [AdminController::class, 'branchesStore'])->name('branches.store');
    Route::get('/branches/{id}/edit', [AdminController::class, 'branchesEdit'])->name('branches.edit');
    Route::put('/branches/{id}', [AdminController::class, 'branchesUpdate'])->name('branches.update');
    Route::delete('/branches/{id}', [AdminController::class, 'branchesDelete'])->name('branches.delete');

    // Banners
    Route::get('/banners', [AdminController::class, 'bannersIndex'])->name('banners.index');
    Route::get('/banners/create', [AdminController::class, 'bannersCreate'])->name('banners.create');
    Route::post('/banners', [AdminController::class, 'bannersStore'])->name('banners.store');
    Route::get('/banners/{id}/edit', [AdminController::class, 'bannersEdit'])->name('banners.edit');
    Route::put('/banners/{id}', [AdminController::class, 'bannersUpdate'])->name('banners.update');
    Route::delete('/banners/{id}', [AdminController::class, 'bannersDelete'])->name('banners.delete');

    // Test Ride Requests
    Route::get('/test-rides', [AdminController::class, 'testRidesIndex'])->name('test-rides.index');
    Route::get('/test-rides/{id}', [AdminController::class, 'testRidesShow'])->name('test-rides.show');
    Route::delete('/test-rides/{id}', [AdminController::class, 'testRidesDelete'])->name('test-rides.delete');

    // Credit Simulation Requests
    Route::get('/credits', [AdminController::class, 'creditsIndex'])->name('credits.index');
    Route::get('/credits/{id}', [AdminController::class, 'creditsShow'])->name('credits.show');
    Route::delete('/credits/{id}', [AdminController::class, 'creditsDelete'])->name('credits.delete');
});