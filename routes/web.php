<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceSectionController;
use App\Http\Controllers\PanelController;
use App\Http\Controllers\WebsiteController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ProductImportExcelController;
use App\Http\Controllers\AdminLogController;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear', function () {
    Artisan::call('route:cache');
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return 'clear';
});
Route::get('/', [AuthController::class, 'login'])->name('/');
Route::get('/logout', [AuthController::class, 'logout'])->name('/logout');
Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('/admin/login');
Route::middleware('CheckUserLogin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'Dashboard'])->name('/Dashboard');
    // user
    Route::match(['get', 'post'], '/user', [UserController::class, 'list'])->name('user');
    Route::post('/user/update', [UserController::class, 'update'])->name('/user/update');
    Route::post('/get/user', [UserController::class, 'get'])->name('/get/user');
    // category
    Route::match(['get', 'post'], '/category', [CategoryController::class, 'list']);
    Route::post('/category/get', [CategoryController::class, 'get']);
    Route::post('/category/update', [CategoryController::class, 'update']);
    // product
    // Product routes
    Route::match(['GET', 'POST'], '/products', [ProductController::class, 'list'])->name('products.list');
    Route::post('/products/bulk', [ProductController::class, 'handle'])->name('products.bulk');
    Route::get('/products/add', [ProductController::class, 'add'])->name('products.add');
    Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('products.edit');
    Route::get('/products/copy/{id}', [ProductController::class, 'copy'])->name('products.copy');
    Route::post('/products/update', [ProductController::class, 'update'])->name('products.update');
    Route::post('/products/delete', [ProductController::class, 'delete'])->name('products.delete');
    Route::POST('/delete', [DashboardController::class, 'delete'])->name('/delete');
    // import
    Route::post('/admin/products/import', [ProductImportController::class, 'store'])
        ->name('admin.products.import');
    Route::get('/admin/products/import/template', [ProductImportController::class, 'template'])
        ->name('admin.products.import.template');
    Route::get('/admin/products/import/errors/{token}', [ProductImportController::class, 'downloadErrors'])
        ->name('admin.products.import.errors');
    Route::get('/admin/audit-logs', [AdminLogController::class, 'list'])
        ->name('admin.audit.index');
});
