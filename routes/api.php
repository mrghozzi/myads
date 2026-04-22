<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdsServingController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\MarketplaceExtensionFeedController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make sure to install
| Laravel Sanctum to enable secure token-based authentication.
|
*/

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Legacy Ad Serving Endpoints (API version)
Route::get('/banner', [AdsServingController::class, 'bannerScript']);
Route::get('/link', [AdsServingController::class, 'linkScript']);
Route::get('/marketplace/extensions/plugins', [MarketplaceExtensionFeedController::class, 'plugins'])->name('api.marketplace.extensions.plugins');
Route::get('/marketplace/extensions/themes', [MarketplaceExtensionFeedController::class, 'themes'])->name('api.marketplace.extensions.themes');

// Protected Routes (Require Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Forum API
    Route::get('/forum', [ForumController::class, 'index']);
    Route::post('/forum', [ForumController::class, 'store']);
    
    // Add other endpoints here
});

// Developer Platform API v1
Route::prefix('developer/v1')->group(function () {
    Route::get('/me', [App\Http\Controllers\DeveloperApiController::class, 'me'])->name('api.developer.me');
    Route::get('/me/profile', [App\Http\Controllers\DeveloperApiController::class, 'myProfile'])->name('api.developer.me.profile');
    
    Route::get('/owner/profile', [App\Http\Controllers\DeveloperApiController::class, 'ownerProfile'])->name('api.developer.owner.profile');
    Route::get('/owner/content', [App\Http\Controllers\DeveloperApiController::class, 'ownerContent'])->name('api.developer.owner.content');
    
    Route::post('/owner/follow', [App\Http\Controllers\DeveloperApiController::class, 'ownerFollow'])->name('api.developer.owner.follow');
    
    // Route::get('/owner/messages', [App\Http\Controllers\DeveloperApiController::class, 'ownerMessagesRead'])->name('api.developer.owner.messages.read'); // Not implemented yet
    Route::post('/owner/messages', [App\Http\Controllers\DeveloperApiController::class, 'ownerMessages'])->name('api.developer.owner.messages');
});
