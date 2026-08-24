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
    Route::post('/settings/tokens/{id}/revoke', [App\Http\Controllers\Api\SettingsController::class, 'revokeToken']);

    Route::get('/settings/badges', [App\Http\Controllers\Api\SettingsController::class, 'getBadges']);
    Route::patch('/settings/badges', [App\Http\Controllers\Api\SettingsController::class, 'updateBadges']);
    Route::get('/settings/history', [App\Http\Controllers\Api\SettingsController::class, 'getHistory']);
    Route::get('/settings/apps', [App\Http\Controllers\Api\SettingsController::class, 'getApps']);
    Route::post('/settings/apps/{id}/revoke', [App\Http\Controllers\Api\SettingsController::class, 'revokeApp']);
    Route::get('/settings/blocks', [App\Http\Controllers\Api\SettingsController::class, 'getBlocks']);

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

    // Video Hub API
    Route::get('/video/feed', [App\Http\Controllers\Api\VideoApiController::class, 'index']);

    // Clips API
    Route::get('/clips', [App\Http\Controllers\Api\ClipsController::class, 'index']);
    Route::get('/clips/saved', [App\Http\Controllers\Api\ClipsController::class, 'saved']);
    Route::post('/clips/{status}/save', [App\Http\Controllers\Api\ClipsController::class, 'save']);
    Route::delete('/clips/{status}/save', [App\Http\Controllers\Api\ClipsController::class, 'unsave']);

    // Profile API
    Route::get('/profile/{identifier}', [App\Http\Controllers\Api\ProfileController::class, 'show']);
    Route::get('/profile/{identifier}/statuses', [App\Http\Controllers\Api\ProfileController::class, 'statuses']);
    Route::post('/profile/{identifier}/follow', [App\Http\Controllers\Api\ProfileController::class, 'follow']);
    Route::post('/profile/{identifier}/block', [App\Http\Controllers\Api\ProfileController::class, 'block']);
    Route::delete('/profile/{identifier}/unblock', [App\Http\Controllers\Api\ProfileController::class, 'unblock']);

    // Messages API
    Route::get('/messages', [App\Http\Controllers\Api\MessageController::class, 'index']);
    Route::get('/messages/updates', [App\Http\Controllers\Api\MessageController::class, 'updates']);
    Route::get('/messages/{identifier}', [App\Http\Controllers\Api\MessageController::class, 'show']);
    Route::post('/messages/{identifier}', [App\Http\Controllers\Api\MessageController::class, 'store']);
    Route::post('/messages/{identifier}/read', [App\Http\Controllers\Api\MessageController::class, 'markAsRead']);

    // Notifications API
    Route::get('/notifications', [App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
    Route::post('/notifications/read-all', [App\Http\Controllers\Api\NotificationController::class, 'markAllAsRead']);
    Route::post('/notifications/{id}/read', [App\Http\Controllers\Api\NotificationController::class, 'markAsRead']);

    // Live Event Stream (SSE)
    Route::get('/live/stream', [App\Http\Controllers\LiveStreamController::class, 'stream'])->name('api.live.stream');

    // Wallet API
    Route::get('/wallet/balance', [App\Http\Controllers\Api\WalletController::class, 'balance']);

    // Forums API (Mobile specific)
    Route::get('/forums/categories', [App\Http\Controllers\Api\ForumApiController::class, 'categories']);
    Route::get('/forums/categories/{categoryId}/topics', [App\Http\Controllers\Api\ForumApiController::class, 'topics']);
    Route::post('/forums/categories/{categoryId}/topics', [App\Http\Controllers\Api\ForumApiController::class, 'storeTopic']);
    Route::get('/forums/topics/{topicId}', [App\Http\Controllers\Api\ForumApiController::class, 'show']);
    Route::post('/forums/topics/{topicId}/replies', [App\Http\Controllers\Api\ForumApiController::class, 'storeReply']);

    // Store API (Mobile specific)
    Route::get('/store/products', [App\Http\Controllers\Api\StoreApiController::class, 'index']);
    Route::get('/store/products/{id}', [App\Http\Controllers\Api\StoreApiController::class, 'show']);
    Route::get('/store/products/{id}/knowledgebase', [App\Http\Controllers\Api\StoreApiController::class, 'knowledgebase']);

    // Orders API
    Route::get('/orders', [App\Http\Controllers\Api\OrderApiController::class, 'index']);
    Route::get('/orders/{id}', [App\Http\Controllers\Api\OrderApiController::class, 'show']);
    Route::post('/orders/{id}/offers', [App\Http\Controllers\Api\OrderApiController::class, 'submitOffer']);

    // Gamification API
    Route::get('/quests', [App\Http\Controllers\Api\GamificationApiController::class, 'quests']);
    Route::post('/quests/{id}/claim', [App\Http\Controllers\Api\GamificationApiController::class, 'claimQuest']);
    Route::post('/pts/transfer', [App\Http\Controllers\Api\GamificationApiController::class, 'transferPts']);
    Route::post('/pts/vouchers/create', [App\Http\Controllers\Api\GamificationApiController::class, 'createVoucher']);
    Route::post('/pts/vouchers/claim', [App\Http\Controllers\Api\GamificationApiController::class, 'claimVoucher']);

    // Live Search API
    Route::get('/search/live', [App\Http\Controllers\Api\SearchApiController::class, 'search']);

    // Settings Additions (FCM)
    Route::post('/settings/device-token', [App\Http\Controllers\Api\SettingsController::class, 'updateDeviceToken']);

    // Ads Stats API
    Route::get('/ads/stats', [App\Http\Controllers\Api\AdsStatsApiController::class, 'stats']);
});

// Developer Platform API v1
// SECURITY: Rate limited to prevent spam (follows, messages, data scraping)
Route::prefix('developer/v1')->middleware('throttle:30,1')->group(function () {
    // User Identity & Profile
    Route::get('/me', [App\Http\Controllers\DeveloperApiController::class, 'me'])->name('api.developer.me');
    Route::get('/me/profile', [App\Http\Controllers\DeveloperApiController::class, 'myProfile'])->name('api.developer.me.profile');
    Route::get('/me/email', [App\Http\Controllers\DeveloperApiController::class, 'myEmail'])->name('api.developer.me.email');
    Route::get('/me/social-links', [App\Http\Controllers\DeveloperApiController::class, 'mySocialLinks'])->name('api.developer.me.social_links');
    Route::get('/me/follows', [App\Http\Controllers\DeveloperApiController::class, 'myFollows'])->name('api.developer.me.follows');
    Route::post('/me/follows', [App\Http\Controllers\DeveloperApiController::class, 'followUser'])->name('api.developer.me.follow_user');

    // Content & Interaction
    Route::get('/me/content', [App\Http\Controllers\DeveloperApiController::class, 'myContent'])->name('api.developer.me.content');
    Route::post('/me/content', [App\Http\Controllers\DeveloperApiController::class, 'postContent'])->name('api.developer.me.post_content');
    Route::post('/me/reactions', [App\Http\Controllers\DeveloperApiController::class, 'reactContent'])->name('api.developer.me.reactions');

    // Messages & Notifications
    Route::get('/me/messages', [App\Http\Controllers\DeveloperApiController::class, 'myMessages'])->name('api.developer.me.messages');
    Route::post('/me/messages', [App\Http\Controllers\DeveloperApiController::class, 'sendMessage'])->name('api.developer.me.send_message');
    Route::get('/me/notifications', [App\Http\Controllers\DeveloperApiController::class, 'myNotifications'])->name('api.developer.me.notifications');

    // Economy, Gamification & Media
    Route::get('/me/wallet', [App\Http\Controllers\DeveloperApiController::class, 'myWallet'])->name('api.developer.me.wallet');
    Route::get('/me/badges', [App\Http\Controllers\DeveloperApiController::class, 'myBadges'])->name('api.developer.me.badges');
    Route::get('/me/clips', [App\Http\Controllers\DeveloperApiController::class, 'myClips'])->name('api.developer.me.clips');
    Route::get('/forums', [App\Http\Controllers\DeveloperApiController::class, 'forumsList'])->name('api.developer.forums');

    // Store & Ads
    Route::get('/store/products', [App\Http\Controllers\DeveloperApiController::class, 'storeProducts'])->name('api.developer.store.products');
    Route::get('/me/orders', [App\Http\Controllers\DeveloperApiController::class, 'myOrders'])->name('api.developer.me.orders');
    Route::get('/me/ads/stats', [App\Http\Controllers\DeveloperApiController::class, 'adsStats'])->name('api.developer.me.ads_stats');

    // Owner Scopes
    Route::get('/owner/profile', [App\Http\Controllers\DeveloperApiController::class, 'ownerProfile'])->name('api.developer.owner.profile');
    Route::get('/owner/content', [App\Http\Controllers\DeveloperApiController::class, 'ownerContent'])->name('api.developer.owner.content');
    Route::post('/owner/follow', [App\Http\Controllers\DeveloperApiController::class, 'ownerFollow'])->name('api.developer.owner.follow');
    Route::post('/owner/messages', [App\Http\Controllers\DeveloperApiController::class, 'ownerMessages'])->name('api.developer.owner.messages');
});
