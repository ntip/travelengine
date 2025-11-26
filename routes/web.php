<?php

use Illuminate\Support\Facades\Route;
use App\Models\Route as RouteModel;
use App\Http\Controllers\Admin\ProviderController;

Route::get('/', function () {
    return view('welcome');
});

// Admin Panel - Dashboard / Homepage - Show Admin View
Route::get('/admin', function () {
    return view('admin.show');
});
// Admin Panel - Routes Management - Show Routes View
Route::get('/admin/routes', function () {
    return view('admin.routes.index');
})->name('admin.routes');

// Single route details (uses implicit model binding with UUID)
Route::get('/routes/{route}', function (RouteModel $route) {
    return view('admin.routes.show', compact('route'));
})->name('admin.routes.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/providers', [ProviderController::class, 'index'])
        ->name('providers');

    Route::get('/providers/{provider}', [ProviderController::class, 'show'])
        ->name('providers.show');

    // Jobs admin
    Route::get('/jobs', function () {
        return view('admin.jobs.index');
    })->name('jobs');

    Route::get('/jobs/{job}', function (App\Models\RouteJob $job) {
        return view('admin.jobs.show', compact('job'));
    })->name('jobs.show');

    // Scrapes admin
    Route::get('/scrapes', function () {
        return view('admin.scrapes.index');
    })->name('scrapes');

    Route::get('/scrapes/{scrape}', function (App\Models\Scrape $scrape) {
        return view('admin.scrapes.show', compact('scrape'));
    })->name('scrapes.show');
});
