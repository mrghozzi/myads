<?php

use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AdsController;
use App\Http\Controllers\AdsServingController;
use App\Http\Controllers\SmartAdsController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminGroupController;
use App\Http\Controllers\AdminSeoController;
use App\Http\Controllers\AdminPageController;
use App\Http\Controllers\AdminBillingController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MentionController;
use App\Http\Controllers\AdminAdminsController;
use App\Http\Controllers\AdminCommunityFeedController;
use App\Http\Controllers\AdminSecurityController;
use App\Http\Controllers\AdminStatusPromotionController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SeoPublicController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\OrderRequestController;
use App\Http\Controllers\OrderOfferController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\StatusPromotionController;
use App\Http\Middleware\AdminMiddleware;

Route::post('/reaction/toggle', [ReactionController::class, 'toggle'])->name('reaction.toggle')->middleware('auth');
Route::post('/comment/load', [CommentController::class, 'load'])->name('comment.load');
Route::post('/comment/store', [CommentController::class, 'store'])->name('comment.store')->middleware('auth');
Route::post('/comment/delete', [CommentController::class, 'destroy'])->name('comment.delete')->middleware('auth');

Route::post('/status/create', [App\Http\Controllers\StatusController::class, 'create'])->name('status.create')->middleware('auth');
Route::post('/status/upload-image', [App\Http\Controllers\StatusController::class, 'uploadImage'])->name('status.upload_image')->middleware('auth');
Route::post('/status/link-preview', [App\Http\Controllers\StatusController::class, 'linkPreview'])->name('status.link_preview')->middleware('auth');
Route::post('/status/gallery/add-images/{topicId}', [App\Http\Controllers\StatusController::class, 'addGalleryImages'])->name('status.gallery.add_images')->middleware('auth');
Route::post('/status/gallery/delete-image/{attachmentId}', [App\Http\Controllers\StatusController::class, 'deleteGalleryImage'])->name('status.gallery.delete_image')->middleware('auth');
Route::post('/status/gallery/clear/{topicId}', [App\Http\Controllers\StatusController::class, 'clearGallery'])->name('status.gallery.clear')->middleware('auth');
Route::post('/status/gallery/reorder/{topicId}', [App\Http\Controllers\StatusController::class, 'reorderGalleryImages'])->name('status.gallery.reorder')->middleware('auth');
Route::get('/mentions/users', [MentionController::class, 'users'])->name('mentions.users')->middleware('auth');
Route::get('/robots.txt', [SeoPublicController::class, 'robots'])->name('robots.txt');

// Auth Routes
Route::get('/captcha', [App\Http\Controllers\CaptchaController::class, 'generate'])->name('captcha.generate');
Route::get('/login/{provider}', [App\Http\Controllers\SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/login/{provider}/callback', [App\Http\Controllers\SocialAuthController::class, 'callback'])->name('social.callback');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Dynamic Pages
Route::get('/page/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('page.show');

// Legal Pages
Route::get('/privacy', [App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [App\Http\Controllers\PageController::class, 'terms'])->name('terms');

Route::get('/', function () {
    // Redirect to installer if DB tables are missing (e.g. fresh install with stale installed marker)
    try {
        if (!\Illuminate\Support\Facades\Schema::hasTable('users')) {
            return redirect('/install');
        }
    } catch (\Exception $e) {
        return redirect('/install');
    }

    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    app(\App\Services\SeoManager::class)->setContext([
        'scope_key' => 'home',
        'resource_title' => __('messages.seo_home_title'),
        'description' => __('messages.seo_home_description'),
        'breadcrumbs' => [
            ['name' => __('messages.home'), 'url' => url('/')],
        ],
    ]);

    return view('theme::welcome');
})->name('index');

Route::get('/home', [HomeController::class, 'index'])->name('dashboard')->middleware('auth');
Route::post('/home', [HomeController::class, 'convertPoints'])->name('dashboard.convert')->middleware('auth');
Route::get('/quests', [App\Http\Controllers\QuestController::class, 'index'])->name('quests.index')->middleware('auth');
Route::get('/plans', [BillingController::class, 'plans'])->name('billing.plans');

// Portal Routes
Route::get('/portal', [PortalController::class, 'index'])->name('portal.index');
Route::get('/share', [PortalController::class, 'share'])->name('portal.share')->middleware('auth');
Route::get('/developer', [PortalController::class, 'developer'])->name('portal.developer');
Route::get('/badges', [ProfileController::class, 'allBadges'])->name('badges.all');

// Groups Routes
Route::get('/groups', [GroupController::class, 'index'])->name('groups.index');
Route::middleware(['auth'])->group(function () {
    Route::get('/groups/create', [GroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::post('/groups/{group}/join', [GroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/{group}/leave', [GroupController::class, 'leave'])->name('groups.leave');
    Route::post('/groups/{group}/discussions', [GroupController::class, 'storeDiscussion'])->name('groups.discussions.store');
    Route::post('/groups/{group}/members/{membership}/approve', [GroupController::class, 'approveMembership'])->name('groups.members.approve');
    Route::post('/groups/{group}/members/{membership}/reject', [GroupController::class, 'rejectMembership'])->name('groups.members.reject');
    Route::post('/groups/{group}/members/{membership}/role', [GroupController::class, 'updateRole'])->name('groups.members.role');
});
Route::get('/groups/{group:slug}', [GroupController::class, 'show'])->name('groups.show');

// Message Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/attachment/{id}', [MessageController::class, 'attachment'])->name('messages.attachment');
    Route::get('/messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/{id}/history', [MessageController::class, 'history'])->name('messages.history');
    Route::get('/messages/{id}/load', [MessageController::class, 'load'])->name('messages.load');
    Route::post('/messages/{id}/send', [MessageController::class, 'send'])->name('messages.send');
    Route::get('/notification', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notification/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_read');
    Route::get('/notif/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::get('/settings/billing', [BillingController::class, 'dashboard'])->name('billing.dashboard');
    Route::post('/plans/{plan}/purchase', [BillingController::class, 'purchase'])->name('billing.purchase');
    Route::get('/billing/orders/{order}', [BillingController::class, 'showOrder'])->name('billing.orders.show');
    Route::get('/billing/orders/{order}/receipt', [BillingController::class, 'showReceipt'])->name('billing.orders.receipt.show');
    Route::post('/billing/orders/{order}/receipt', [BillingController::class, 'uploadReceipt'])->name('billing.orders.receipt.update');
    Route::get('/billing/return/{gateway}/{order}', [BillingController::class, 'handleReturn'])->name('billing.return');
});

Route::post('/billing/webhook/{gateway}', [BillingController::class, 'handleWebhook'])->name('billing.webhook');

// Forum Routes
Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/f{id}', [ForumController::class, 'category'])->name('forum.category');
Route::get('/t{id}', [ForumController::class, 'topic'])->name('forum.topic');
Route::get('/post', [ForumController::class, 'create'])->name('forum.create');
Route::post('/post', [ForumController::class, 'store'])->name('forum.store')->middleware('auth');
Route::get('/editor/{id}', [ForumController::class, 'edit'])->name('forum.edit')->middleware('auth');
Route::post('/editor/{id}', [ForumController::class, 'update'])->name('forum.update')->middleware('auth');
Route::post('/forum/delete', [ForumController::class, 'destroy'])->name('forum.delete')->middleware('auth');
Route::post('/forum/{topic}/pin', [ForumController::class, 'togglePin'])->name('forum.pin')->middleware('auth');
Route::post('/forum/{topic}/lock', [ForumController::class, 'toggleLock'])->name('forum.lock')->middleware('auth');
Route::get('/forum/attachment/{attachment}', [ForumController::class, 'downloadAttachment'])->name('forum.attachment.download');
Route::post('/forum/report', [AdminController::class, 'storeReport'])->name('forum.report');

// Directory Routes
Route::get('/directory', [DirectoryController::class, 'index'])->name('directory.index');
Route::get('/directory/{id}', [DirectoryController::class, 'show'])->name('directory.show');
Route::get('/dr{id}', [DirectoryController::class, 'showShort'])->name('directory.show.short');
Route::get('/site-{hash}', [DirectoryController::class, 'redirectShort'])->name('directory.redirect.short');
Route::post('/directory/store', [DirectoryController::class, 'store'])->name('directory.store')->middleware('auth');
    Route::get('/directory/{id}/edit', [DirectoryController::class, 'edit'])->name('directory.edit')->middleware('auth');
    Route::put('/directory/{id}', [DirectoryController::class, 'update'])->name('directory.update')->middleware('auth');
    Route::post('/directory/delete', [DirectoryController::class, 'destroy'])->name('directory.delete')->middleware('auth');
Route::get('/directory/category/{id}', [DirectoryController::class, 'category'])->name('directory.category');
Route::get('/cat/{id}', [DirectoryController::class, 'category'])->name('directory.category.legacy');
Route::get('/add-site.html', [DirectoryController::class, 'create'])->name('directory.create');
Route::post('/directory/fetch-metadata', [DirectoryController::class, 'fetchMetadata'])->name('directory.fetch_metadata')->middleware('auth');
Route::get('/directory/image/{id}', [DirectoryController::class, 'fetchImage'])->name('directory.image.fetch');

// News Routes
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');

// Ads Management Routes
Route::middleware(['auth'])->group(function () {
    // Main Ads Dashboard
    Route::get('/ads', [AdsController::class, 'index'])->name('ads.index');

    // Banners
    Route::get('/ads/banners', [AdsController::class, 'indexBanners'])->name('ads.banners.index');
    Route::get('/ads/banners/create', [AdsController::class, 'createBanner'])->name('ads.banners.create');
    Route::post('/ads/banners', [AdsController::class, 'storeBanner'])->name('ads.banners.store');
    Route::get('/ads/banners/code', [AdsController::class, 'codeBanner'])->name('ads.banners.code');
    Route::get('/ads/banners/{id}/edit', [AdsController::class, 'editBanner'])->name('ads.banners.edit');
    Route::put('/ads/banners/{id}', [AdsController::class, 'updateBanner'])->name('ads.banners.update');
    Route::delete('/ads/banners/{id}', [AdsController::class, 'destroyBanner'])->name('ads.banners.destroy');

    // Links
    Route::get('/ads/links', [AdsController::class, 'indexLinks'])->name('ads.links.index');
    Route::get('/ads/links/create', [AdsController::class, 'createLink'])->name('ads.links.create');
    Route::post('/ads/links', [AdsController::class, 'storeLink'])->name('ads.links.store');
    Route::get('/ads/links/code', [AdsController::class, 'codeLink'])->name('ads.links.code');
    Route::get('/ads/links/{id}/edit', [AdsController::class, 'editLink'])->name('ads.links.edit');
    Route::put('/ads/links/{id}', [AdsController::class, 'updateLink'])->name('ads.links.update');
    Route::delete('/ads/links/{id}', [AdsController::class, 'destroyLink'])->name('ads.links.destroy');

    // Smart Ads
    Route::get('/ads/smart', [SmartAdsController::class, 'index'])->name('ads.smart.index');
    Route::get('/ads/smart/create', [SmartAdsController::class, 'create'])->name('ads.smart.create');
    Route::post('/ads/smart', [SmartAdsController::class, 'store'])->name('ads.smart.store');
    Route::get('/ads/smart/code', [SmartAdsController::class, 'code'])->name('ads.smart.code');
    Route::get('/ads/smart/{id}/edit', [SmartAdsController::class, 'edit'])->name('ads.smart.edit');
    Route::put('/ads/smart/{id}', [SmartAdsController::class, 'update'])->name('ads.smart.update');
    Route::delete('/ads/smart/{id}', [SmartAdsController::class, 'destroy'])->name('ads.smart.destroy');

    // Referrals
    Route::get('/ads/referrals', [AdsController::class, 'referrals'])->name('ads.referrals');

    // Post Promotions
    Route::get('/ads/posts', [StatusPromotionController::class, 'index'])->name('ads.posts.index');
    Route::get('/ads/posts/{status}/promote', [StatusPromotionController::class, 'create'])->name('ads.posts.create');
    Route::post('/ads/posts/{status}/quote', [StatusPromotionController::class, 'quote'])->name('ads.posts.quote');
    Route::post('/ads/posts/{status}/promote', [StatusPromotionController::class, 'store'])->name('ads.posts.store');

    // Promote
    Route::get('/ads/promote', [AdsController::class, 'promote'])->name('ads.promote');
    Route::get('/promote', [AdsController::class, 'promote']); // Alias
});

// Ads Serving & Tracking (Legacy Compatibility)
Route::get('/bn.php', [AdsServingController::class, 'bannerScript'])->name('ads.script');
Route::get('/link.php', [AdsServingController::class, 'linkScript'])->name('ads.link.script');
Route::get('/smart.php', [AdsServingController::class, 'smartScript'])->name('ads.smart.script');
Route::get('/embed/banner.js', [AdsServingController::class, 'bannerEmbedScript'])->name('ads.embed.banner');
Route::get('/embed/link.js', [AdsServingController::class, 'linkEmbedScript'])->name('ads.embed.link');
Route::get('/embed/smart.js', [AdsServingController::class, 'smartEmbedScript'])->name('ads.embed.smart');
Route::get('/show.php', [AdsServingController::class, 'redirect'])->name('ads.redirect');
Route::get('/ads/redirect', [AdsServingController::class, 'redirect']); // Alias

// Visits Exchange Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/visits', [VisitController::class, 'index'])->name('visits.index');
    Route::get('/visits/create', [VisitController::class, 'create'])->name('visits.create');
    Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
    Route::get('/visits/{id}/edit', [VisitController::class, 'edit'])->name('visits.edit');
    Route::put('/visits/{id}', [VisitController::class, 'update'])->name('visits.update');
    Route::delete('/visits/{id}', [VisitController::class, 'destroy'])->name('visits.destroy');
    Route::get('/visits/surf', [VisitController::class, 'surf'])->name('visits.surf');
});

// Order Request Routes
Route::get('/orders', [OrderRequestController::class, 'index'])->name('orders.index');
Route::middleware(['auth'])->group(function () {
    Route::get('/orders/mine', [OrderRequestController::class, 'mine'])->name('orders.mine');
    Route::get('/orders/offers', [OrderRequestController::class, 'offers'])->name('orders.offers');
    Route::get('/orders/create', [OrderRequestController::class, 'create'])->name('orders.create');
    Route::post('/orders', [OrderRequestController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/edit', [OrderRequestController::class, 'edit'])->name('orders.edit');
    Route::match(['put', 'patch'], '/orders/{order}', [OrderRequestController::class, 'update'])->name('orders.update');
    Route::post('/orders/{order}/offers', [OrderOfferController::class, 'store'])->name('orders.offers.store');
    Route::match(['put', 'patch'], '/orders/offers/{offer}', [OrderOfferController::class, 'update'])->name('orders.offers.update');
    Route::delete('/orders/offers/{offer}', [OrderOfferController::class, 'destroy'])->name('orders.offers.destroy');
    Route::post('/orders/{order}/award', [OrderRequestController::class, 'award'])->name('orders.award');
    Route::post('/orders/{order}/start', [OrderRequestController::class, 'start'])->name('orders.start');
    Route::post('/orders/{order}/deliver', [OrderRequestController::class, 'deliver'])->name('orders.deliver');
    Route::post('/orders/{order}/complete', [OrderRequestController::class, 'complete'])->name('orders.complete');
    Route::post('/orders/{order}/cancel', [OrderRequestController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/rate', [OrderRequestController::class, 'rate'])->name('orders.rate');
    Route::post('/orders/{order}/rate-offer', [OrderRequestController::class, 'rate'])->name('orders.rate_offer');
    Route::post('/orders/{order}/select-best', [OrderRequestController::class, 'award'])->name('orders.select_best');
    Route::post('/orders/{order}/close', [OrderRequestController::class, 'close'])->name('orders.close');
    Route::delete('/orders/{order}', [OrderRequestController::class, 'destroy'])->name('orders.destroy');
});
Route::get('/orders/{order}', [OrderRequestController::class, 'show'])->name('orders.show');

// Store Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/store/create', [StoreController::class, 'create'])->name('store.create');
    Route::post('/store/store', [StoreController::class, 'store'])->name('store.store');
    Route::post('/store/delete', [StoreController::class, 'destroy'])->name('store.delete');
    Route::get('/store/{name}/update', [StoreController::class, 'update'])->name('store.update');
    Route::post('/store/{name}/update', [StoreController::class, 'storeUpdate'])->name('store.update.store');
    Route::post('/store/{name}/update-topic', [StoreController::class, 'updateTopic'])->name('store.update.topic');
    Route::post('/store/{name}/update-details', [StoreController::class, 'updateDetails'])->name('store.update.details');
    Route::post('/store/download/{id}', [StoreController::class, 'download'])->name('store.download');
    Route::post('/store/upload-zip', [StoreController::class, 'uploadZip'])->name('store.upload_zip');
    Route::post('/store/verify-name', [StoreController::class, 'verifyName'])->name('store.verify_name');
    Route::post('/store/categories', [StoreController::class, 'loadCategories'])->name('store.categories');
});

Route::get('/store', [StoreController::class, 'index'])->name('store.index');
Route::get('/store/{script}/{category}', [StoreController::class, 'index'])->name('store.script_category');
Route::get('/store/{name}', [StoreController::class, 'show'])->name('store.show');
Route::get('/download/{hash}', [StoreController::class, 'downloadByHash'])->name('store.download.hash');
Route::get('/kb/captcha', [StoreController::class, 'knowledgebaseCaptcha'])->name('kb.captcha');
Route::post('/kb/store', [StoreController::class, 'knowledgebaseStore'])->name('kb.store');
Route::post('/kb/approve', [StoreController::class, 'knowledgebaseApprove'])->name('kb.approve');
Route::post('/kb/community/publish', [StoreController::class, 'knowledgebasePublishToCommunity'])->name('kb.community.publish')->middleware('auth');
Route::post('/kb/community/delete', [StoreController::class, 'knowledgebaseDeleteCommunityPost'])->name('kb.community.delete')->middleware('auth');
Route::get('/kb/{name}:{article}', [StoreController::class, 'knowledgebaseShow'])->name('kb.show')->where('name', '[^/:]+');
Route::get('/edk/{name}:{article}', [StoreController::class, 'knowledgebaseEdit'])->name('kb.edit')->where('name', '[^/:]+');
Route::get('/pgk/{name}:{article}', [StoreController::class, 'knowledgebasePending'])->name('kb.pending')->where('name', '[^/:]+');
Route::get('/hkd/{name}:{article}', [StoreController::class, 'knowledgebaseHistory'])->name('kb.history')->where('name', '[^/:]+');
Route::get('/kb/{name}', [StoreController::class, 'knowledgebaseIndex'])->name('kb.index')->where('name', '[^/]+');



// Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings'); // Alias for settings
    Route::get('/history', [ProfileController::class, 'history'])->name('profile.history');
    Route::get('/settings/privacy', [ProfileController::class, 'privacy'])->name('profile.privacy');
    Route::post('/settings/privacy', [ProfileController::class, 'updatePrivacy'])->name('profile.privacy.update');
    Route::get('/settings/badges', [ProfileController::class, 'badges'])->name('profile.badges');
    Route::post('/settings/badges', [ProfileController::class, 'updateBadges'])->name('profile.badges.update');
    Route::get('/settings/social', [ProfileController::class, 'social'])->name('profile.social');
    Route::post('/settings/social', [ProfileController::class, 'updateSocial'])->name('profile.social.update');
    Route::get('/settings/sessions', [ProfileController::class, 'sessions'])->name('profile.sessions');
    Route::post('/settings/sessions/{id}/revoke', [ProfileController::class, 'revokeSession'])->name('profile.sessions.revoke');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/{id}/follow', [ProfileController::class, 'toggleFollow'])->name('profile.follow');
});

// Legacy Redirects
Route::get('/e{id}', function($id) {
    if(Auth::check() && Auth::id() == $id) {
        return redirect()->route('profile.edit');
    }
    return redirect()->route('profile.show', \App\Models\User::findOrFail($id)->username);
})->where('id', '[0-9]+');

Route::get('/p{id}', function($id) {
    if(Auth::check() && Auth::id() == $id) {
        return redirect()->route('profile.edit');
    }
    return redirect()->route('profile.show', \App\Models\User::findOrFail($id)->username);
})->where('id', '[0-9]+');

Route::get('/uFriends/{id}', function($id) {
    $user = \App\Models\User::findOrFail($id);
    return redirect()->route('profile.followers', $user->username); // Or following, or separate view
})->where('id', '[0-9]+');

Route::get('/uPhotos/{username}', function($username) {
    return redirect()->route('profile.show', ['username' => $username, 'tab' => 'photos']);
});

Route::get('/uBlog/{username}', function($username) {
    return redirect()->route('profile.show', ['username' => $username, 'tab' => 'blog']);
});

Route::get('/ulinks/{username}', function($username) {
    return redirect()->route('profile.show', ['username' => $username, 'tab' => 'links']);
});

Route::get('/uforum/{username}', function($username) {
    $user = \App\Models\User::where('username', $username)->firstOrFail();
    return redirect()->route('forum.index', ['user' => $user->id]);
});

Route::get('/ushop/{username}', function($username) {
    return redirect()->route('store.show', $username);
});

// Public Profile Routes
Route::get('/u/{id}', [ProfileController::class, 'showById'])->where('id', '[0-9]+');
Route::get('/u/{username}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/profile/{username}', [ProfileController::class, 'show']); // Alias
Route::get('/u/{username}/followers', [ProfileController::class, 'followers'])->name('profile.followers');
Route::get('/u/{username}/following', [ProfileController::class, 'following'])->name('profile.following');
Route::get('/followers/{id}', function($id) {
    $user = \App\Models\User::findOrFail($id);
    return redirect()->route('profile.followers', $user->username);
}); // Legacy redirect
Route::get('/following/{id}', function($id) {
    $user = \App\Models\User::findOrFail($id);
    return redirect()->route('profile.following', $user->username);
}); // Legacy redirect

// Legacy Profile Short Link (e.g. /u/123) - Handled if username is numeric or special case
// But ProfileController@show expects username. 
// If {username} is numeric, we might need to find by ID if not found by username.
// Better to keep showById for ID-based lookups or redirect.
Route::get('/u/id/{id}', [ProfileController::class, 'showById'])->name('profile.short');

// Report Routes
Route::get('/report', [App\Http\Controllers\ReportController::class, 'index'])->name('report.index');
Route::post('/report', [App\Http\Controllers\ReportController::class, 'store'])->name('report.store')->middleware('auth');

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/confirm-password', [AdminSecurityController::class, 'showConfirmPasswordForm'])->name('admin.confirm-password.form');
    Route::post('/confirm-password', [AdminSecurityController::class, 'confirmPassword'])->name('admin.confirm-password.store');

    Route::middleware(['admin.password.confirm'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    Route::get('/settings/system', [AdminController::class, 'systemSettings'])->name('admin.settings.system');
    Route::post('/settings/system', [AdminController::class, 'updateSystemSettings'])->name('admin.settings.system.update');
    Route::get('/settings/cookie-notice', [AdminController::class, 'cookieNoticeSettings'])->name('admin.cookie_notice');
    Route::post('/settings/cookie-notice', [AdminController::class, 'updateCookieNoticeSettings'])->name('admin.cookie_notice.update');
    Route::get('/seo', [AdminSeoController::class, 'index'])->name('admin.seo.index');
    Route::get('/seo/settings', [AdminSeoController::class, 'settings'])->name('admin.seo.settings');
    Route::post('/seo/settings', [AdminSeoController::class, 'updateSettings'])->name('admin.seo.settings.update');
    Route::get('/seo/head', [AdminSeoController::class, 'head'])->name('admin.seo.head');
    Route::post('/seo/head', [AdminSeoController::class, 'updateHead'])->name('admin.seo.head.update');
    Route::get('/seo/rules', [AdminSeoController::class, 'rules'])->name('admin.seo.rules');
    Route::post('/seo/rules', [AdminSeoController::class, 'storeRule'])->name('admin.seo.rules.store');
    Route::put('/seo/rules/{rule}', [AdminSeoController::class, 'updateRule'])->name('admin.seo.rules.update');
    Route::delete('/seo/rules/{rule}', [AdminSeoController::class, 'destroyRule'])->name('admin.seo.rules.delete');
    Route::get('/seo/indexing', [AdminSeoController::class, 'indexing'])->name('admin.seo.indexing');
    Route::post('/seo/indexing', [AdminSeoController::class, 'updateIndexing'])->name('admin.seo.indexing.update');
    
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::put('/users/{id}/password', [AdminController::class, 'updateUserPassword'])->name('admin.users.password');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::delete('/users/bulk/delete', [AdminController::class, 'bulkDeleteUsers'])->name('admin.users.bulk_delete');
    Route::get('/admins', [AdminAdminsController::class, 'index'])->name('admin.admins');
    Route::post('/admins', [AdminAdminsController::class, 'store'])->name('admin.admins.store');
    Route::put('/admins/{siteAdmin}', [AdminAdminsController::class, 'update'])->name('admin.admins.update');
    Route::delete('/admins/{siteAdmin}', [AdminAdminsController::class, 'destroy'])->name('admin.admins.delete');
    
    // Banners
    Route::get('/ads', [AdminController::class, 'adsHub'])->name('admin.ads');
    Route::get('/ads/settings', [AdminController::class, 'adsSettings'])->name('admin.ads.settings');
    Route::post('/ads/settings', [AdminController::class, 'updateAdsSettings'])->name('admin.ads.settings.update');
    Route::get('/ads/posts', [AdminStatusPromotionController::class, 'index'])->name('admin.ads.posts.index');
    Route::get('/ads/posts/settings', [AdminStatusPromotionController::class, 'settings'])->name('admin.ads.posts.settings');
    Route::post('/ads/posts/settings', [AdminStatusPromotionController::class, 'updateSettings'])->name('admin.ads.posts.settings.update');
    Route::post('/ads/posts/{promotion}/status', [AdminStatusPromotionController::class, 'updateStatus'])->name('admin.ads.posts.status');
    Route::get('/banners', [AdminController::class, 'banners'])->name('admin.banners');
    Route::get('/banners/{id}/edit', [AdminController::class, 'editBanner'])->name('admin.banners.edit');
    Route::post('/banners/{id}', [AdminController::class, 'updateBanner'])->name('admin.banners.update');
    Route::delete('/banners/{id}', [AdminController::class, 'deleteBanner'])->name('admin.banners.delete');
    Route::delete('/banners/bulk/delete', [AdminController::class, 'bulkDeleteBanners'])->name('admin.banners.bulk_delete');
    
    // Stats
    Route::get('/stats', [AdminController::class, 'stats'])->name('admin.stats');

    // Links
    Route::get('/links', [AdminController::class, 'links'])->name('admin.links');
    Route::post('/links/{id}', [AdminController::class, 'updateLink'])->name('admin.links.update');
    Route::delete('/links/{id}', [AdminController::class, 'deleteLink'])->name('admin.links.delete');
    Route::delete('/links/bulk/delete', [AdminController::class, 'bulkDeleteLinks'])->name('admin.links.bulk_delete');

    // Smart Ads
    Route::get('/smart-ads', [AdminController::class, 'smartAds'])->name('admin.smart_ads');
    Route::get('/smart-ads/{id}/edit', [AdminController::class, 'editSmartAd'])->name('admin.smart_ads.edit');
    Route::post('/smart-ads/{id}', [AdminController::class, 'updateSmartAd'])->name('admin.smart_ads.update');
    Route::delete('/smart-ads/{id}', [AdminController::class, 'deleteSmartAd'])->name('admin.smart_ads.delete');
    Route::delete('/smart-ads/bulk/delete', [AdminController::class, 'bulkDeleteSmartAds'])->name('admin.smart_ads.bulk_delete');
    
    // Visits
    Route::get('/visits', [AdminController::class, 'visits'])->name('admin.visits');
    Route::post('/visits/{id}', [AdminController::class, 'updateVisit'])->name('admin.visits.update');
    Route::delete('/visits/{id}', [AdminController::class, 'deleteVisit'])->name('admin.visits.delete');
    Route::delete('/visits/bulk/delete', [AdminController::class, 'bulkDeleteVisits'])->name('admin.visits.bulk_delete');
    
    // Forum Categories
    Route::get('/forum/categories', [AdminController::class, 'forumCategories'])->name('admin.forum_categories');
    Route::post('/forum/categories', [AdminController::class, 'storeForumCategory'])->name('admin.forum_categories.store');
    Route::post('/forum/categories/{id}', [AdminController::class, 'updateForumCategory'])->name('admin.forum_categories.update');
    Route::delete('/forum/categories/{id}', [AdminController::class, 'deleteForumCategory'])->name('admin.forum_categories.delete');
    Route::get('/community/feed/settings', [AdminCommunityFeedController::class, 'settings'])->name('admin.community.feed.settings');
    Route::post('/community/feed/settings', [AdminCommunityFeedController::class, 'updateSettings'])->name('admin.community.feed.settings.update');
    Route::get('/groups', [AdminGroupController::class, 'index'])->name('admin.groups.index');
    Route::post('/groups/{group}/status', [AdminGroupController::class, 'updateStatus'])->name('admin.groups.status');
    Route::post('/groups/{group}/feature', [AdminGroupController::class, 'toggleFeatured'])->name('admin.groups.feature');
    Route::get('/groups/settings', [AdminGroupController::class, 'settings'])->name('admin.groups.settings');
    Route::post('/groups/settings', [AdminGroupController::class, 'updateSettings'])->name('admin.groups.settings.update');
    Route::get('/forum/settings', [AdminController::class, 'forumSettings'])->name('admin.forum.settings');
    Route::post('/forum/settings', [AdminController::class, 'updateForumSettings'])->name('admin.forum.settings.update');
    Route::get('/forum/moderators', [AdminController::class, 'forumModerators'])->name('admin.forum.moderators');
    Route::post('/forum/moderators', [AdminController::class, 'storeForumModerator'])->name('admin.forum.moderators.store');
    Route::put('/forum/moderators/{id}', [AdminController::class, 'updateForumModerator'])->name('admin.forum.moderators.update');
    Route::delete('/forum/moderators/{id}', [AdminController::class, 'deleteForumModerator'])->name('admin.forum.moderators.delete');
    
    // Directory Categories
    Route::get('/directory/categories', [AdminController::class, 'directoryCategories'])->name('admin.directory_categories');
    Route::post('/directory/categories', [AdminController::class, 'storeDirectoryCategory'])->name('admin.directory_categories.store');
    Route::post('/directory/categories/{id}', [AdminController::class, 'updateDirectoryCategory'])->name('admin.directory_categories.update');
    Route::delete('/directory/categories/{id}', [AdminController::class, 'deleteDirectoryCategory'])->name('admin.directory_categories.delete');
    
    // Site Ads
    Route::get('/site-ads', [AdminController::class, 'siteAds'])->name('admin.site_ads');
    Route::post('/site-ads', [AdminController::class, 'updateSiteAds'])->name('admin.site_ads.update_all');
    Route::post('/site-ads/{id}', [AdminController::class, 'updateSiteAd'])->name('admin.site_ads.update');
    
    // Languages
    Route::get('/languages', [AdminController::class, 'languages'])->name('admin.languages');
    Route::post('/languages', [AdminController::class, 'storeLanguage'])->name('admin.languages.store');
    Route::post('/languages/set-default/{id}', [AdminController::class, 'setDefaultLanguage'])->name('admin.languages.set_default');
    Route::post('/languages/{id}', [AdminController::class, 'updateLanguage'])->name('admin.languages.update');
    Route::delete('/languages/{id}', [AdminController::class, 'deleteLanguage'])->name('admin.languages.delete');
    Route::get('/languages/{id}/terms', [AdminController::class, 'editLanguageTerms'])->name('admin.languages.terms');
    Route::post('/languages/{id}/terms', [AdminController::class, 'updateLanguageTerms'])->name('admin.languages.terms.update');
    Route::get('/languages/{id}/export', [AdminController::class, 'exportLanguage'])->name('admin.languages.export');
    
    // Knowledgebase
    Route::get('/knowledgebase', [AdminController::class, 'knowledgebase'])->name('admin.knowledgebase');
    Route::post('/knowledgebase', [AdminController::class, 'storeKnowledgebase'])->name('admin.knowledgebase.store');
    Route::post('/knowledgebase/{id}', [AdminController::class, 'updateKnowledgebase'])->name('admin.knowledgebase.update');
    Route::delete('/knowledgebase/{id}', [AdminController::class, 'deleteKnowledgebase'])->name('admin.knowledgebase.delete');

    // Emojis
    Route::get('/emojis', [AdminController::class, 'emojis'])->name('admin.emojis');
    Route::post('/emojis', [AdminController::class, 'storeEmoji'])->name('admin.emojis.store');
    Route::delete('/emojis/{id}', [AdminController::class, 'deleteEmoji'])->name('admin.emojis.delete');

    // News
    Route::get('/news', [AdminController::class, 'news'])->name('admin.news');
    Route::post('/news', [AdminController::class, 'storeNews'])->name('admin.news.store');
    Route::post('/news/{id}', [AdminController::class, 'updateNews'])->name('admin.news.update');
    Route::delete('/news/{id}', [AdminController::class, 'deleteNews'])->name('admin.news.delete');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::delete('/reports/{id}', [AdminController::class, 'deleteReport'])->name('admin.reports.delete');

    // Widgets
    Route::get('/widgets', [AdminController::class, 'widgets'])->name('admin.widgets');
    Route::get('/widgets/form', [AdminController::class, 'widgetForm'])->name('admin.widgets.form');
    Route::get('/widgets/{id}/form', [AdminController::class, 'widgetEditForm'])->name('admin.widgets.form.edit');
    Route::post('/widgets/reorder', [AdminController::class, 'reorderWidgets'])->name('admin.widgets.reorder');
    Route::post('/widgets', [AdminController::class, 'storeWidget'])->name('admin.widgets.store');
    Route::post('/widgets/{id}', [AdminController::class, 'updateWidget'])->name('admin.widgets.update');
    Route::delete('/widgets/{id}', [AdminController::class, 'deleteWidget'])->name('admin.widgets.delete');

    // Updates
    Route::get('/updates', [App\Http\Controllers\AdminUpdatesController::class, 'index'])->name('admin.updates');
    Route::post('/updates', [App\Http\Controllers\AdminUpdatesController::class, 'update'])->name('admin.updates.process');
    Route::post('/updates/check', [App\Http\Controllers\AdminUpdatesController::class, 'checkUpdate'])->name('admin.updates.check');

    // Menus
    Route::get('/menus', [AdminController::class, 'menus'])->name('admin.menus');
    Route::post('/menus', [AdminController::class, 'storeMenu'])->name('admin.menus.store');
    Route::post('/menus/{id}', [AdminController::class, 'updateMenu'])->name('admin.menus.update');
    Route::delete('/menus/{id}', [AdminController::class, 'deleteMenu'])->name('admin.menus.delete');

    // Products
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::delete('/products/delete', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');
    Route::get('/products/{id}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::post('/products/{id}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::post('/products/{id}/suspend', [AdminController::class, 'suspendProduct'])->name('admin.products.suspend');

    // Plugins
    Route::get('/plugins', [AdminController::class, 'plugins'])->name('admin.plugins');
    Route::post('/plugins/activate', [AdminController::class, 'activatePlugin'])->name('admin.plugins.activate');
    Route::post('/plugins/deactivate', [AdminController::class, 'deactivatePlugin'])->name('admin.plugins.deactivate');
    Route::post('/plugins/delete', [AdminController::class, 'deletePlugin'])->name('admin.plugins.delete');
    Route::post('/plugins/upload', [AdminController::class, 'uploadPlugin'])->name('admin.plugins.upload');
    Route::post('plugins/upgrade', [AdminController::class, 'upgradePlugin'])->name('admin.plugins.upgrade');
    Route::post('plugins/install-marketplace', [AdminController::class, 'installPluginFromMarketplace'])->name('admin.plugins.install_marketplace');

    Route::get('plugins/thumbnail/{slug}', [AdminController::class, 'pluginThumbnail'])->name('admin.plugins.thumbnail');
    Route::get('plugins/details/{slug}', [AdminController::class, 'pluginDetails'])->name('admin.plugins.details');

    // Themes
    Route::get('/themes', [AdminController::class, 'themes'])->name('admin.themes');
    Route::post('/themes/activate', [AdminController::class, 'activateTheme'])->name('admin.themes.activate');
    Route::post('/themes/upgrade', [AdminController::class, 'upgradeTheme'])->name('admin.themes.upgrade');
    Route::post('/themes/install-marketplace', [AdminController::class, 'installThemeFromMarketplace'])->name('admin.themes.install_marketplace');

    Route::get('themes/thumbnail/{slug}', [AdminController::class, 'themeThumbnail'])->name('admin.themes.thumbnail');
    Route::match(['get', 'post'], '/sitemap/generate', [SitemapController::class, 'generate'])->name('admin.sitemap.generate');

    // Billing
    Route::prefix('billing')->group(function () {
        Route::get('/', [AdminBillingController::class, 'overview'])->name('admin.billing.overview');
        Route::get('/settings', [AdminBillingController::class, 'settings'])->name('admin.billing.settings');
        Route::post('/settings', [AdminBillingController::class, 'updateSettings'])->name('admin.billing.settings.update');
        Route::get('/plans', [AdminBillingController::class, 'plans'])->name('admin.billing.plans');
        Route::post('/plans', [AdminBillingController::class, 'storePlan'])->name('admin.billing.plans.store');
        Route::post('/plans/{plan}', [AdminBillingController::class, 'updatePlan'])->name('admin.billing.plans.update');
        Route::get('/orders', [AdminBillingController::class, 'orders'])->name('admin.billing.orders');
        Route::get('/orders/{order}', [AdminBillingController::class, 'showOrder'])->name('admin.billing.orders.show');
        Route::post('/orders/{order}/review', [AdminBillingController::class, 'reviewOrder'])->name('admin.billing.orders.review');
        Route::get('/transactions', [AdminBillingController::class, 'transactions'])->name('admin.billing.transactions');
        Route::get('/currencies', [AdminBillingController::class, 'currencies'])->name('admin.billing.currencies');
        Route::post('/currencies', [AdminBillingController::class, 'storeCurrency'])->name('admin.billing.currencies.store');
        Route::post('/currencies/{currency}', [AdminBillingController::class, 'updateCurrency'])->name('admin.billing.currencies.update');
        Route::delete('/currencies/{currency}', [AdminBillingController::class, 'deleteCurrency'])->name('admin.billing.currencies.delete');
        Route::post('/currencies/{currency}/base', [AdminBillingController::class, 'setBaseCurrency'])->name('admin.billing.currencies.base');
        Route::get('/gateways', [AdminBillingController::class, 'gateways'])->name('admin.billing.gateways');
        Route::post('/gateways/{gateway}', [AdminBillingController::class, 'updateGateway'])->name('admin.billing.gateways.update');
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/{order}', [AdminOrderController::class, 'show'])->name('admin.orders.show');
        Route::post('/{order}/close', [AdminOrderController::class, 'close'])->name('admin.orders.close');
        Route::post('/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('admin.orders.cancel');
    });

    // Pages
    Route::get('/pages', [AdminPageController::class, 'index'])->name('admin.pages');
    Route::get('/pages/create', [AdminPageController::class, 'create'])->name('admin.pages.create');
    Route::post('/pages', [AdminPageController::class, 'store'])->name('admin.pages.store');
    Route::get('/pages/{id}/edit', [AdminPageController::class, 'edit'])->name('admin.pages.edit');
    Route::put('/pages/{id}', [AdminPageController::class, 'update'])->name('admin.pages.update');
    Route::delete('/pages/{id}', [AdminPageController::class, 'destroy'])->name('admin.pages.delete');
    Route::post('/pages/generate-slug', [AdminPageController::class, 'generateSlug'])->name('admin.pages.generate_slug');

    // Maintenance
    Route::get('/maintenance', [AdminController::class, 'maintenance'])->name('admin.maintenance');
    Route::post('/maintenance/settings', [AdminController::class, 'updateMaintenanceSettings'])->name('admin.maintenance.settings.update');
    Route::post('/maintenance/clear-cache', [AdminController::class, 'clearCache'])->name('admin.maintenance.clear_cache');
    Route::post('/maintenance/migrate', [AdminController::class, 'runMigrations'])->name('admin.maintenance.migrate');
    Route::post('/maintenance/db-repair', [AdminController::class, 'dbRepair'])->name('admin.maintenance.db_repair');
    Route::post('/maintenance/repair-orphaned', [AdminController::class, 'repairOrphanedRecords'])->name('admin.maintenance.repair_orphaned');
    Route::post('/maintenance/repair-orphaned-content', [AdminController::class, 'repairOrphanedContent'])->name('admin.maintenance.repair_orphaned_content');
    Route::post('/maintenance/repair-orphaned-stats', [AdminController::class, 'repairOrphanedStats'])->name('admin.maintenance.repair_orphaned_stats');

    // Security
    Route::get('/security', [AdminSecurityController::class, 'index'])->name('admin.security.index');
    Route::post('/security', [AdminSecurityController::class, 'update'])->name('admin.security.update');
    Route::get('/security/ip-bans', [AdminSecurityController::class, 'ipBans'])->name('admin.security.ip-bans');
    Route::post('/security/ip-bans', [AdminSecurityController::class, 'storeIpBan'])->name('admin.security.ip-bans.store');
    Route::delete('/security/ip-bans/{id}', [AdminSecurityController::class, 'destroyIpBan'])->name('admin.security.ip-bans.delete');
    Route::get('/security/sessions', [AdminSecurityController::class, 'sessions'])->name('admin.security.sessions');
    Route::post('/security/sessions/{id}/revoke', [AdminSecurityController::class, 'revokeSession'])->name('admin.security.sessions.revoke');
    });
});

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.xml');
Route::get('/sitemap/{type}/{page}.xml', [SitemapController::class, 'section'])->name('sitemap.section');

// Tags
Route::get('/tag/{tag}', [TagController::class, 'index'])->name('tag.show');

// Legacy User Routes
Route::get('/b_list', [AdsController::class, 'indexBanners'])->name('legacy.b_list')->middleware('auth');
Route::get('/b_code', [AdsController::class, 'codeBanner'])->name('legacy.b_code')->middleware('auth');
Route::get('/l_list', [AdsController::class, 'indexLinks'])->name('legacy.l_list')->middleware('auth');
Route::get('/l_code', [AdsController::class, 'codeLink'])->name('legacy.l_code')->middleware('auth');
Route::get('/s_code', [SmartAdsController::class, 'code'])->name('legacy.s_code')->middleware('auth');
Route::get('/v_list', [VisitController::class, 'index'])->name('legacy.v_list')->middleware('auth');
Route::get('/r_code', [AdsController::class, 'referrals'])->name('legacy.r_code')->middleware('auth');
Route::get('/referral', [AdsController::class, 'referralList'])->name('legacy.referral')->middleware('auth');
Route::get('/state', [AdsController::class, 'state'])->name('legacy.state')->middleware('auth');
