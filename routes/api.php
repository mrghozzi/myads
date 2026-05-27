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

// Public Routes (rate-limited for security)
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register'])->middleware('throttle:3,1');
Route::post('/license/verify', [App\Http\Controllers\LicenseApiController::class, 'verify'])->name('api.license.verify')->middleware('throttle:10,1');

// Legacy Ad Serving Endpoints (API version)
Route::get('/banner', [AdsServingController::class, 'bannerScript']);
Route::get('/link', [AdsServingController::class, 'linkScript']);
Route::match(['GET', 'POST'], '/marketplace/extensions/plugins', [MarketplaceExtensionFeedController::class, 'plugins'])->name('api.marketplace.extensions.plugins');
Route::get('/marketplace/extensions/themes', [MarketplaceExtensionFeedController::class, 'themes'])->name('api.marketplace.extensions.themes');
Route::get('/marketplace/extensions/download', [MarketplaceExtensionFeedController::class, 'download'])->name('api.marketplace.extensions.download');

// Protected Routes (Require Sanctum)
Route::middleware(['api.key', 'auth:sanctum'])->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return new App\Http\Resources\UserResource($request->user());
    });

    // Settings API
    Route::get('/settings/overview', [App\Http\Controllers\Api\SettingsController::class, 'overview']);
    Route::get('/settings/profile', [App\Http\Controllers\Api\SettingsController::class, 'getProfile']);
    Route::post('/settings/profile', [App\Http\Controllers\Api\SettingsController::class, 'updateProfile']);
    Route::get('/settings/privacy', [App\Http\Controllers\Api\SettingsController::class, 'getPrivacy']);
    Route::patch('/settings/privacy', [App\Http\Controllers\Api\SettingsController::class, 'updatePrivacy']);
    Route::post('/settings/2fa/enable', [App\Http\Controllers\Api\SettingsController::class, 'enableTwoFactor']);
    Route::post('/settings/2fa/disable', [App\Http\Controllers\Api\SettingsController::class, 'disableTwoFactor']);
    Route::get('/settings/social', [App\Http\Controllers\Api\SettingsController::class, 'getSocial']);
    Route::patch('/settings/social', [App\Http\Controllers\Api\SettingsController::class, 'updateSocial']);
    Route::get('/settings/notification-preferences', [App\Http\Controllers\Api\SettingsController::class, 'getNotificationPreferences']);
    Route::patch('/settings/notification-preferences', [App\Http\Controllers\Api\SettingsController::class, 'updateNotificationPreferences']);
    Route::get('/settings/sessions', [App\Http\Controllers\Api\SettingsController::class, 'getSessions']);
    Route::post('/settings/sessions/{id}/revoke', [App\Http\Controllers\Api\SettingsController::class, 'revokeSession']);
    Route::get('/settings/badges', [App\Http\Controllers\Api\SettingsController::class, 'getBadges']);
    Route::patch('/settings/badges', [App\Http\Controllers\Api\SettingsController::class, 'updateBadges']);
    Route::get('/settings/history', [App\Http\Controllers\Api\SettingsController::class, 'getHistory']);
    Route::get('/settings/apps', [App\Http\Controllers\Api\SettingsController::class, 'getApps']);
    Route::post('/settings/apps/{id}/revoke', [App\Http\Controllers\Api\SettingsController::class, 'revokeApp']);

    // Forum API
    Route::get('/forum', [ForumController::class, 'index']);
    Route::post('/forum', [ForumController::class, 'store']);
    
    // Community Feed API
    Route::get('/portal/feed', [App\Http\Controllers\Api\PortalController::class, 'index']);
    
    // Composer Options
    Route::get('/composer/options', [App\Http\Controllers\Api\StatusController::class, 'composerOptions']);
    Route::post('/statuses/link-preview', [App\Http\Controllers\Api\StatusController::class, 'linkPreview']);
    
    // Statuses API
    Route::get('/statuses/{status}', [App\Http\Controllers\Api\StatusController::class, 'show']);
    Route::post('/statuses', [App\Http\Controllers\Api\StatusController::class, 'store']);
    Route::post('/statuses/{status}/update', [App\Http\Controllers\Api\StatusController::class, 'update']);
    Route::delete('/statuses/{status}', [App\Http\Controllers\Api\StatusController::class, 'destroy']);
    
    // Comments API
    Route::get('/statuses/{status}/comments', [App\Http\Controllers\Api\CommentController::class, 'index']);
    Route::post('/statuses/{status}/comments', [App\Http\Controllers\Api\CommentController::class, 'store']);
    
    // Reactions API
    Route::post('/reactions/toggle', [App\Http\Controllers\Api\ReactionController::class, 'toggle']);

    // Reels API
    Route::get('/reels', [App\Http\Controllers\Api\ReelsController::class, 'index']);
    Route::get('/reels/saved', [App\Http\Controllers\Api\ReelsController::class, 'saved']);
    Route::post('/reels/{status}/save', [App\Http\Controllers\Api\ReelsController::class, 'save']);
    Route::delete('/reels/{status}/save', [App\Http\Controllers\Api\ReelsController::class, 'unsave']);

    // Profile API
    Route::get('/profile/{identifier}', [App\Http\Controllers\Api\ProfileController::class, 'show']);
    Route::get('/profile/{identifier}/statuses', [App\Http\Controllers\Api\ProfileController::class, 'statuses']);
    Route::post('/profile/{identifier}/follow', [App\Http\Controllers\Api\ProfileController::class, 'follow']);

    // Messages API
    Route::get('/messages', [App\Http\Controllers\Api\MessageController::class, 'index']);
    Route::get('/messages/{identifier}', [App\Http\Controllers\Api\MessageController::class, 'show']);
    Route::post('/messages/{identifier}', [App\Http\Controllers\Api\MessageController::class, 'store']);
    Route::post('/messages/{identifier}/read', [App\Http\Controllers\Api\MessageController::class, 'markAsRead']);

    // Notifications API
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
    Route::post('/notifications/read-all', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);

    // Wallet API
    Route::get('/wallet/balance', [App\Http\Controllers\Api\WalletController::class, 'balance']);

    // Forums API (Mobile specific)
    Route::get('/forums/categories', [App\Http\Controllers\Api\ForumApiController::class, 'categories']);
    Route::get('/forums/categories/{categoryId}/topics', [App\Http\Controllers\Api\ForumApiController::class, 'topics']);
    Route::get('/forums/topics/{topicId}', [App\Http\Controllers\Api\ForumApiController::class, 'show']);

    // Store API (Mobile specific)
    Route::get('/store/products', [App\Http\Controllers\Api\StoreApiController::class, 'index']);
    Route::get('/store/products/{id}', [App\Http\Controllers\Api\StoreApiController::class, 'show']);
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
