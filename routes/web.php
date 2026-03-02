<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\DirectoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\AdsController;
use App\Http\Controllers\AdsServingController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\CommentController;
use App\Http\Middleware\AdminMiddleware;

Route::post('/reaction/toggle', [ReactionController::class, 'toggle'])->name('reaction.toggle')->middleware('auth');
Route::post('/comment/load', [CommentController::class, 'load'])->name('comment.load');
Route::post('/comment/store', [CommentController::class, 'store'])->name('comment.store')->middleware('auth');
Route::post('/comment/delete', [CommentController::class, 'destroy'])->name('comment.delete')->middleware('auth');

Route::post('/status/create', [App\Http\Controllers\StatusController::class, 'create'])->name('status.create')->middleware('auth');
Route::post('/status/upload-image', [App\Http\Controllers\StatusController::class, 'uploadImage'])->name('status.upload_image')->middleware('auth');

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
    return view('theme::welcome');
})->name('index');

Route::get('/home', [HomeController::class, 'index'])->name('dashboard')->middleware('auth');
Route::post('/home', [HomeController::class, 'convertPoints'])->name('dashboard.convert')->middleware('auth');

// Portal Routes
Route::get('/portal', [PortalController::class, 'index'])->name('portal.index');

// Message Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{id}', [MessageController::class, 'show'])->name('messages.show');
    Route::get('/messages/{id}/load', [MessageController::class, 'load'])->name('messages.load');
    Route::post('/messages/{id}/send', [MessageController::class, 'send'])->name('messages.send');
    Route::get('/notification', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notification/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark_all_read');
    Route::get('/notif/{id}', [NotificationController::class, 'show'])->name('notifications.show');
});

// Forum Routes
Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
Route::get('/f{id}', [ForumController::class, 'category'])->name('forum.category');
Route::get('/t{id}', [ForumController::class, 'topic'])->name('forum.topic');
Route::get('/post', [ForumController::class, 'create'])->name('forum.create');
Route::post('/post', [ForumController::class, 'store'])->name('forum.store')->middleware('auth');
Route::get('/editor/{id}', [ForumController::class, 'edit'])->name('forum.edit')->middleware('auth');
Route::post('/editor/{id}', [ForumController::class, 'update'])->name('forum.update')->middleware('auth');
Route::post('/forum/delete', [ForumController::class, 'destroy'])->name('forum.delete')->middleware('auth');
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

    // Referrals
    Route::get('/ads/referrals', [AdsController::class, 'referrals'])->name('ads.referrals');

    // Promote
    Route::get('/ads/promote', [AdsController::class, 'promote'])->name('ads.promote');
});

// Ads Serving & Tracking (Legacy Compatibility)
Route::get('/bn.php', [AdsServingController::class, 'bannerScript'])->name('ads.script');
Route::get('/link.php', [AdsServingController::class, 'linkScript'])->name('ads.link.script');
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

// Store Routes
Route::get('/store', [StoreController::class, 'index'])->name('store.index');
Route::middleware(['auth'])->group(function () {
    Route::get('/store/create', [StoreController::class, 'create'])->name('store.create');
});
Route::get('/store/{name}', [StoreController::class, 'show'])->name('store.show');
Route::get('/download/{hash}', [StoreController::class, 'downloadByHash'])->name('store.download.hash');
Route::get('/kb/captcha', [StoreController::class, 'knowledgebaseCaptcha'])->name('kb.captcha');
Route::post('/kb/store', [StoreController::class, 'knowledgebaseStore'])->name('kb.store');
Route::post('/kb/approve', [StoreController::class, 'knowledgebaseApprove'])->name('kb.approve');
Route::get('/kb/{name}:{article}', [StoreController::class, 'knowledgebaseShow'])->name('kb.show')->where('name', '[A-Za-z0-9_]+');
Route::get('/edk/{name}:{article}', [StoreController::class, 'knowledgebaseEdit'])->name('kb.edit')->where('name', '[A-Za-z0-9_]+');
Route::get('/pgk/{name}:{article}', [StoreController::class, 'knowledgebasePending'])->name('kb.pending')->where('name', '[A-Za-z0-9_]+');
Route::get('/hkd/{name}:{article}', [StoreController::class, 'knowledgebaseHistory'])->name('kb.history')->where('name', '[A-Za-z0-9_]+');
Route::get('/kb/{name}', [StoreController::class, 'knowledgebaseIndex'])->name('kb.index')->where('name', '[A-Za-z0-9_]+');
Route::middleware(['auth'])->group(function () {
    // Route::get('/store/create', [StoreController::class, 'create'])->name('store.create'); // Moved up to fix conflict
    Route::post('/store/store', [StoreController::class, 'store'])->name('store.store');
    Route::post('/store/delete', [StoreController::class, 'destroy'])->name('store.delete');
    Route::get('/store/{name}/update', [StoreController::class, 'update'])->name('store.update');
    Route::post('/store/{name}/update', [StoreController::class, 'storeUpdate'])->name('store.update.store');
    Route::post('/store/download/{id}', [StoreController::class, 'download'])->name('store.download');
    Route::post('/store/upload-zip', [StoreController::class, 'uploadZip'])->name('store.upload_zip');
    Route::post('/store/verify-name', [StoreController::class, 'verifyName'])->name('store.verify_name');
    Route::post('/store/categories', [StoreController::class, 'loadCategories'])->name('store.categories');
});

// Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/settings', [ProfileController::class, 'edit'])->name('settings'); // Alias for settings
    Route::get('/history', [ProfileController::class, 'history'])->name('profile.history');
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
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('admin.settings.update');
    Route::get('/settings/system', [AdminController::class, 'systemSettings'])->name('admin.settings.system');
    Route::post('/settings/system', [AdminController::class, 'updateSystemSettings'])->name('admin.settings.system.update');
    Route::get('/settings/cookie-notice', [AdminController::class, 'cookieNoticeSettings'])->name('admin.cookie_notice');
    Route::post('/settings/cookie-notice', [AdminController::class, 'updateCookieNoticeSettings'])->name('admin.cookie_notice.update');
    
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::put('/users/{id}/password', [AdminController::class, 'updateUserPassword'])->name('admin.users.password');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // Banners
    Route::get('/banners', [AdminController::class, 'banners'])->name('admin.banners');
    Route::get('/banners/{id}/edit', [AdminController::class, 'editBanner'])->name('admin.banners.edit');
    Route::post('/banners/{id}', [AdminController::class, 'updateBanner'])->name('admin.banners.update');
    Route::delete('/banners/{id}', [AdminController::class, 'deleteBanner'])->name('admin.banners.delete');
    
    // Stats
    Route::get('/stats', [AdminController::class, 'stats'])->name('admin.stats');

    // Links
    Route::get('/links', [AdminController::class, 'links'])->name('admin.links');
    Route::post('/links/{id}', [AdminController::class, 'updateLink'])->name('admin.links.update');
    Route::delete('/links/{id}', [AdminController::class, 'deleteLink'])->name('admin.links.delete');
    
    // Visits
    Route::get('/visits', [AdminController::class, 'visits'])->name('admin.visits');
    Route::post('/visits/{id}', [AdminController::class, 'updateVisit'])->name('admin.visits.update');
    Route::delete('/visits/{id}', [AdminController::class, 'deleteVisit'])->name('admin.visits.delete');
    
    // Forum Categories
    Route::get('/forum/categories', [AdminController::class, 'forumCategories'])->name('admin.forum_categories');
    Route::post('/forum/categories', [AdminController::class, 'storeForumCategory'])->name('admin.forum_categories.store');
    Route::post('/forum/categories/{id}', [AdminController::class, 'updateForumCategory'])->name('admin.forum_categories.update');
    Route::delete('/forum/categories/{id}', [AdminController::class, 'deleteForumCategory'])->name('admin.forum_categories.delete');
    
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

    // Plugins
    Route::get('/plugins', [AdminController::class, 'plugins'])->name('admin.plugins');
    Route::post('/plugins/activate', [AdminController::class, 'activatePlugin'])->name('admin.plugins.activate');
    Route::post('/plugins/deactivate', [AdminController::class, 'deactivatePlugin'])->name('admin.plugins.deactivate');
    Route::post('/plugins/delete', [AdminController::class, 'deletePlugin'])->name('admin.plugins.delete');
    Route::post('/plugins/upload', [AdminController::class, 'uploadPlugin'])->name('admin.plugins.upload');
    Route::post('/plugins/upgrade', [AdminController::class, 'upgradePlugin'])->name('admin.plugins.upgrade');

    // Themes
    Route::get('/themes', [AdminController::class, 'themes'])->name('admin.themes');
    Route::post('/themes/activate', [AdminController::class, 'activateTheme'])->name('admin.themes.activate');
    Route::get('/sitemap/generate', [\App\Http\Controllers\SitemapController::class, 'generate'])->name('admin.sitemap.generate');
});

Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'generate'])->name('sitemap.xml');

// Legacy User Routes
Route::get('/b_list', [AdsController::class, 'indexBanners'])->name('legacy.b_list')->middleware('auth');
Route::get('/b_code', [AdsController::class, 'codeBanner'])->name('legacy.b_code')->middleware('auth');
Route::get('/l_list', [AdsController::class, 'indexLinks'])->name('legacy.l_list')->middleware('auth');
Route::get('/l_code', [AdsController::class, 'codeLink'])->name('legacy.l_code')->middleware('auth');
Route::get('/v_list', [VisitController::class, 'index'])->name('legacy.v_list')->middleware('auth');
Route::get('/r_code', [AdsController::class, 'referrals'])->name('legacy.r_code')->middleware('auth');
Route::get('/referral', [AdsController::class, 'referralList'])->name('legacy.referral')->middleware('auth');
Route::get('/state', [AdsController::class, 'state'])->name('legacy.state')->middleware('auth');
