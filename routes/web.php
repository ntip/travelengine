<?php

use Illuminate\Support\Facades\Route;
use App\Models\Route as RouteModel;

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
