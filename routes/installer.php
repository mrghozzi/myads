<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstallerController;

Route::group(['prefix' => 'install', 'middleware' => [\App\Http\Middleware\InstallerGuard::class]], function () {
    Route::get('/', [InstallerController::class, 'welcome'])->name('installer.welcome');
    Route::get('/requirements', [InstallerController::class, 'requirements'])->name('installer.requirements');
    Route::get('/database', [InstallerController::class, 'database'])->name('installer.database');
    Route::post('/database', [InstallerController::class, 'processDatabase'])->name('installer.database.process');
    Route::get('/migrate', [InstallerController::class, 'migrate'])->name('installer.migrate');
    Route::post('/migrate', [InstallerController::class, 'processMigrate'])->name('installer.migrate.process');
    Route::get('/admin', [InstallerController::class, 'admin'])->name('installer.admin');
    Route::post('/admin', [InstallerController::class, 'processAdmin'])->name('installer.admin.process');
    Route::get('/finish', [InstallerController::class, 'finish'])->name('installer.finish');
    
    // Update Routes (accessible even after installation for upgrades)
    Route::get('/update', [InstallerController::class, 'update'])->name('installer.update');
    Route::post('/update', [InstallerController::class, 'processUpdate'])->name('installer.update.process');
});
