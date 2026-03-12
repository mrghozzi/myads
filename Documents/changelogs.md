# v4.0.1
> **Patch Release** — Installer resilience & shared hosting compatibility fixes.

### 🔧 Installer & Fresh Install
* **Fix**: Auto-create `.env` from `.env.example` if missing on fresh install (`index.php`).
* **Fix**: Auto-create required `storage/` subdirectories on first run (`index.php`).
* **Fix**: Remove stale `storage/installed` marker during fresh install.
* **Fix**: Added `storage/installed` to `.gitignore` to prevent shipping with repo.
* **Fix**: Handle `MissingAppKeyException` gracefully — auto-generates `APP_KEY` and redirects to `/install` (`bootstrap/app.php`).
* **Fix**: Default `APP_KEY` added to `.env.example` to prevent crash before installer runs.
* **Fix**: Enable `APP_DEBUG=true` in `.env.example` for error visibility during install; automatically disabled after install completes.
* **Fix**: Exclude `install/*` routes from CSRF verification to prevent `419 Page Expired` errors during setup (`bootstrap/app.php`).
* **Fix**: Missing `directory` table creation added to database migrations and `processMigrate()` method.

### 🐛 Bug Fixes
* **Fix**: `Status` model — null pointer crash in `getGroupedReactionsAttribute()` when reaction option doesn't exist.
* **Fix**: All `Status` model `$appends` accessors (`date_formatted`, `reactions_count`, `comments_count`, `grouped_reactions`) wrapped in `try-catch` to prevent cascading failures.
* **Fix**: `PortalController` — replaced `CONVERT(... USING utf8mb4)` raw SQL with standard `LIKE` queries for MySQL compatibility on free hosting.
* **Fix**: `DirectoryController::index()` — separated categories from activities into independent `try-catch` blocks so categories still display when activities query fails.
* **Fix**: `DirectoryController::show()` — reject non-numeric IDs with 404 (prevents `/directory/store` and `/directory/ads` from crashing).
* **Fix**: `DirectoryController::store()` — wrapped in `try-catch` with error message fallback.
* **Fix**: `DirectoryController::category()` — wrapped in `try-catch` for resilience.
* **Fix**: `AdminController::index()` — wrapped GitHub API call and stats queries in `try-catch` to prevent dashboard crash on restricted hosting.
* **Fix**: `Directory` model — added `date` to `$fillable` array (was silently dropped on create).
* **Fix**: `InstallerController::finish()` — disable `APP_DEBUG` after installation for security.

# v4.0.0
> **Major Release** — Complete rewrite from plain PHP to the Laravel framework.

### 🏗️ Architecture
* **Rewrite**: Complete migration from plain PHP to **Laravel 12** framework (MVC architecture).
* **Add**: Blade templating engine replacing raw PHP templates (172 old templates → organized Blade views).
* **Add**: **Eloquent ORM** with 27 models replacing raw SQL queries.
* **Add**: 25 dedicated controllers replacing 31 standalone request handlers and 38 PHP files.
* **Add**: Proper routing system (`routes/web.php`) replacing direct PHP file access.
* **Add**: Middleware system (`AdminMiddleware`, `SetLocale`, `UpdateUserOnline`, `CheckSystemVersion`, `InstallerGuard`).
* **Add**: CSRF protection on all forms via Laravel middleware.
* **Add**: Database migrations and seeders for structured schema management.

### 🎨 UI & Theme
* **Add**: **Duralux** admin panel theme (Bootstrap 5) replacing the old admin panel templates.
* **Improvement**: Modern landing page inspired by Google AdSense with animated sections, scroll-reveal effects, and stats counters.
* **Add**: Dark mode / Light mode toggle across the entire application.
* **Add**: RTL (Right-to-Left) support for Arabic language.
* **Improvement**: Responsive design across all pages (desktop, tablet, mobile).
* **Add**: Theme system — support for multiple switchable themes from the admin panel.

### 🔐 Authentication & Security
* **Add**: Laravel authentication system (login, register, logout, remember me).
* **Improvement**: Support for logging in via **Email or Username** automatically.
* **Add**: Social login via **Google** and **Facebook** OAuth.
* **Add**: Mandatory **Terms & Conditions** and **Privacy Policy** agreement on registration.
* **Add**: CAPTCHA verification on registration to prevent bots.
* **Improvement**: Password hashing using bcrypt (Laravel `Hash` facade).
* **Delete**: Insecure `wasp.gq` share and login integration removed.

### 🌍 Multilingual System
* **Improvement**: Complete translation system rewrite using Laravel's localization architecture.
* **Add**: **9 Languages** fully translated: English, Arabic, French, Spanish, Portuguese, Persian, Turkish, German, and Italian.
* **Add**: **Dynamic Language Discovery** — system automatically detects and adds new language folders to the UI.
* **Add**: Language management from the admin panel (sync, edit terms, export).
* **Add**: Dynamic locale switching via `SetLocale` middleware with session and cookie persistence.

### 📊 Admin Panel
* **Improvement**: Complete admin dashboard redesign with **Duralux** theme.
* **Add**: Admin Header Controls: Language switcher dropdown and "Back to Site" button for quick navigation.
* **Add**: Admin product management page (grid layout, view/delete products).
* **Add**: Admin emoji management (add/delete custom emojis).
* **Add**: Admin navigation menu management (add, edit, delete, reorder menus).
* **Add**: Admin widget management (create, edit, delete sidebar widgets with positioning).
* **Add**: Admin language management (add/edit/delete translation languages and terms).
* **Add**: Admin reports management page.
* **Add**: Admin news management page.
* **Add**: Admin knowledgebase management.
* **Add**: Admin cookie notice settings (enable/disable, colors, position).
* **Add**: Admin system settings page (site name, description, maintenance mode).
* **Add**: Admin theme management (switch between installed themes).
* **Improvement**: Admin stats page with detailed analytics.
* **Improvement**: Admin user management with profile editing.

### 🏪 Store
* **Add**: Market Categories filtering (Script, Templates, Plugins) — click a category to filter products.
* **Add**: Live product counts per category replacing "SOON" badges.
* **Add**: "All Products" reset button when category filter is active.
* **Improvement**: Product cards with seller profile links and category display.

### 🛡️ Legal & Compliance
* **Add**: Static **Privacy Policy** page (`/privacy`) — fully translated (EN/AR).
* **Add**: Static **Terms & Conditions** page (`/terms`) — fully translated (EN/AR).
* **Add**: **GDPR / CCPA Cookie Consent** banner with admin-customizable settings (colors, position, enable/disable).

### 🔌 Plugin System
* **Add**: Plugin architecture — install, activate, deactivate, and delete plugins from the admin panel.
* **Add**: Plugin file upload and auto-discovery system.

### 📢 Promote Page
* **Add**: Dedicated promote page for Banner Ads, Text Ads, Link Ads, and Exchange Visits.
* **Improvement**: Ad creation and management with point-based pricing.

### 🏠 Portal & Widgets
* **Add**: Configurable portal widgets system (sidebar left, sidebar right, content area).
* **Add**: Admin widget form for creating custom HTML/content widgets.
* **Improvement**: Portal page with dynamic widget rendering.

### 📝 Community & Forum
* **Improvement**: Community posts with Blade templates and proper MVC structure.
* **Improvement**: Comment system with emoji support.
* **Improvement**: Reaction system (like, love, dislike, etc.).
* **Improvement**: Forum topics and categories with admin-manageable ordering.
* **Add**: Forum moderation system (pin, lock topics).
* **Add**: Global and category-specific forum moderators.
* **Add**: Forum file attachments support.

### 🔗 Directory
* **Improvement**: Site directory with categorized listings.
* **Add**: Admin directory category management (add, edit, delete, reorder).

### 💬 Messages & Notifications
* **Improvement**: Private messaging system with real-time chat refresh.
* **Improvement**: Notification system for ads, follows, reactions, and comments.

### 📦 Other Changes
* **Add**: Sitemap generation with `SitemapController`.
* **Add**: News section with admin management.
* **Improvement**: User profile pages with follow/unfollow, posts, and activity tabs.
* **Improvement**: Visit exchange system with proper tracking.
* **Add**: Referral system improvements.
* **Delete**: Legacy `wasp.php` integration.
* **Update**: Font Awesome, Bootstrap, and all frontend libraries to latest versions.

# v3.2.0
* **Fix**: Multiple security bugs.
* **Delete**: Share and login from `wasp.gq`
* **Add**: Share via telegram
* **Add**: Added `<nav>` to divide posts in the member's profile
* **Add**: Friends page (following from both sides)
* **Improvement**: Multi-language system
* **Fix**: Update system to the latest version
* **Fix**: Multiple page issue in the forum
* **Improvement**: Visit Exchange System
* **Add**: Auto-copy feature when clicking on text boxes in `b_code.php`, `l_code.php` and `r_code.php` pages
* **Improvement**: message list
* **Add**: notifications when ads are blocked, enabled, or removed
* **Add**: a copy link button for community posts
* **Add**: The ability to publish works in the store in exchange for points (PTS).
* **Improvement**: UI and security in state and template files
# v3.1.0
* **Improvement**: Site directory
* **Update**: privacy policy
* **Fix**: Error in advertisement manager template
* **Improvement**: admin_nav & admin_home template
* **Addition**: Follow all posts or posts from those you follow in the community
* **Improvement**: Private message template
* **Improvement**: Community posts and images template
* **Fix**: Bugs and vulnerabilities
* **Addition**: The ability to arrange forum categories
* **Added**: Description of forum categories
# v3.0.6-beta
* Fix: security bugs.
* Added: PTS History.
* Fix: Syntax error in language files.
* Fix: Error in site directory notifications.
* Added: Button to report ads.
* Improve the template /follow.php
* Change: Twitter logo
* Update: Font Awesome library to version v6.4.2
* Improvement: The appearance of forum topics in the portal
# v3.0.5-beta
* Fix: Message template and add `reloadChat()` to even see incoming messages without resorting to reloading the page.
* Fix: Wrongly translated dates.
* Improvement: List of ad banners.
* Fix: Error with writing editor in store knowledge base.
* Fix: Error when a white page appears when entering a non-existent member page.
* Fix: The problem of entering a member profile whose name consists of numbers only.
* Add: Earn points by posting in the community and participating in posts and comments.
* Add: Stats Box widgets.
* Fix: Problem showing posts.
# v3.0.4-beta
* Add: French language
* fix: some bugs
# v3.0.3-beta
* Improvement: multi-language
* Fix: Some errors in templates
* Add: Number of views for text ads
# v3.0.2-beta
* Addition: Find out who interacted with the posts in the community
* Fix: Some errors in templates
# v3.0.1-beta
* Fix: Error on product update page
* Addition: The ability to change the user's password from the admin panel
* Fix: Error in the number of posts by the user on his personal page
* Fix: Some errors in templates
* FIX: Error editing ads when activating manager mode
* Fix: Error showing some components about visitors assigned to members (such as: the number of points and the edit box for posts)
* Improvement: Multilingualism
* Fix: Error calculating the number of incoming notifications and messages
* Addition: The option to block educational links from members and visitors to the site
* Improvement: Posts from plugin codes
* Fix: Error in admin panel when long links or long names appear
* Add: The ability to add footer ads.
* Add: human verification code when registering a new account
* Add: Widgets (suggested users) can be controlled from the admin panel widgets page
# v3.0.0-beta
* ***[Update]*** : in general appearance (updated all templates and updated Bootstrap library to v4.4.1
* ***[Add]*** : Publish text posts
* ***[Addition]*** : The system for interacting with publications (👍❤️👎☺️😄😮😡😢)
* ***[Delete]*** : Sharing posts between members to be rebuilt again in future versions
* ***[Improvement]*** : posting images within the community and removing the (beta) tag
* ***[Improvement]*** : the store, adding sections and deleting the (beta) tag
* ***[Improvement]*** : notifications and messages system
* ***[Improvement]*** : the community comments system and adding the ability to comment on links
* ***[Update]*** : Forum and Website Directory
* ***[Update]*** : User's personal page
* ***[Update]*** : Sitemap and rebuilding
# v2.4.6
* Fix : A problem with responsive ad banners appearing
* Various fixes and script preparation for the transition to the 3rd generation
# v2.4.5
* Fix some bugs during installation
* Some minor tweaks
# v2.4.4
* Fix : the problem of editing posts.
* Fix : multiple pages.
* Improve : the appearance of posts.
* Addition: Automatic update of the latest "myads" updates.