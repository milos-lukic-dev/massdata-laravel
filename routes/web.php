<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

Route::prefix('/user-management')->middleware(['auth', 'verified', 'permission:user-management'])->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('user.index');
    Route::get('/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/store', [UserController::class, 'store'])->name('user.store');
    Route::get('/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/destroy', [UserController::class, 'destroy'])->name('user.destroy');
});

Route::prefix('/permission')->middleware(['auth', 'verified', 'permission:user-management'])->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('permission.index');
    Route::get('/users/{id}', [PermissionController::class, 'users'])->name('permission.users');
    Route::post('/search-user', [PermissionController::class, 'searchUser'])->name('permission.searchUser');
    Route::post('/assign-user-permission', [PermissionController::class, 'assignUserPermission'])->name('permission.assignUserPermission');
    Route::delete('/remove-user-permission', [PermissionController::class, 'removeUserPermission'])->name('permission.removeUserPermission');
    Route::get('/create', [PermissionController::class, 'create'])->name('permission.create');
    Route::post('/store', [PermissionController::class, 'store'])->name('permission.store');
    Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name('permission.edit');
    Route::put('/update', [PermissionController::class, 'update'])->name('permission.update');
    Route::delete('/destroy', [PermissionController::class, 'destroy'])->name('permission.destroy');
});

Route::prefix('imported-data')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/{importType}/{fileKey}', [ImportController::class, 'importedData'])->name('imported-data.show');
    Route::get('/{importType}/{fileKey}/export', [ImportController::class, 'export'])->name('imported-data.export');
    Route::delete('/{importType}/{fileKey}/{id}', [ImportController::class, 'destroy'])->middleware('import.delete-record')->name('imported-data.destroy');
    Route::post('/audit-logs/{importType}/{objectId}', [ImportController::class, 'getAudits'])->name('imported-data.audits');
});

Route::prefix('import-logs')->middleware(['auth', 'verified'])->group(function () {
    Route::get('/', [ImportController::class, 'importLogs'])->name('import-log.index');
    Route::get('/{id}', [ImportController::class, 'importLogErrors'])->name('import-log.show');
});

Route::middleware(['auth', 'verified', 'import.permissions'])->group(function () {
    Route::get('/data-import', [ImportController::class, 'import'])->name('import.create');
    Route::post('/data-import-upload', [ImportController::class, 'upload'])->name('import.upload');
});

require __DIR__.'/auth.php';
