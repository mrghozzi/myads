# Agents.md — MYADS v4.2.0

> **Purpose:** This file gives AI coding agents a fast, comprehensive understanding of the MYADS project — its architecture, conventions, key files, and rules — so they can work effectively from a fresh chat context.

---

## 1. Project Identity

- **Name:** MYADS v4.2.0
- **Type:** Social network + ad exchange platform for website owners
- **Framework:** Laravel 12 (PHP 8.2+)
- **Database:** MySQL 5.7+ / MariaDB 10.3+
- **Frontend:** Bootstrap 5, Blade templates, vanilla JS (AJAX-heavy, no SPA)
- **Admin Panel:** "Duralux" theme (Bootstrap 5, dark mode)
- **License:** MIT
- **Author:** mrghozzi

---

## 2. What This Project Does

MYADS is a community platform where website owners:
1. **Exchange advertising** — banner ads, text/link ads, visit exchange (surf-to-earn), and Smart Ads (contextual/native).
2. **Socialize** — profiles, follow system, community feed (posts, galleries, link previews, quote reposts, @mentions), private messaging, reactions, comments.
3. **Forum** — categories with visibility controls, topics, moderation (pin/lock), attachments, moderator roles.
4. **Marketplace (Store)** — upload/download scripts, plugins, templates (PTS-based pricing). Wiki-style Knowledgebase per product with Markdown support.
5. **Web Directory** — submit/browse categorized website listings.
6. **Order Requests** — hire service providers with bids, ratings, "Best Offer" selection.
7. **News** — admin-published articles posted to community feed.
8. **Gamification** — points (PTS), badges, quests, point transactions ledger.
9. **SEO Suite** — centralized SEO engine, admin SEO dashboard, dynamic robots.txt, sitemap index, GA4 integration.

---

## 3. Technology Stack & Dependencies

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12, PHP 8.2+ |
| ORM | Eloquent (40+ models) |
| Auth | Laravel Auth + Sanctum (API), Social OAuth via Socialite (Google, Facebook) |
| Templates | Blade (theme-namespaced as `theme::`) |
| CSS/JS | Bootstrap 5, FontAwesome 6, SCEditor (forum), StackEdit (Wiki MDL), custom vanilla JS |
| DB | MySQL/MariaDB via PDO |
| Caching | File-based (configurable) |
| Sessions | File-based (configurable) |
| Mail | SMTP (configurable) |
| Dev tools | PHPUnit 11, Faker, Pint, Pail |

**Key composer packages:** `laravel/framework`, `laravel/socialite`, `laravel/tinker`

---

## 4. Directory Structure

```
myads/
├── app/
│   ├── Console/Commands/  # Artisan commands such as update safety preflight
│   ├── Helpers/           # Hooks.php (WordPress-like action/filter system)
│   ├── helpers.php         # Global helpers: theme_asset(), ads_site(), locale_direction(), is_locale_rtl()
│   ├── Http/
│   │   ├── Controllers/    # 37 controllers (see §5)
│   │   └── Middleware/     # AdminMiddleware, SetLocale, UpdateUserOnline, CheckSystemVersion, InstallerGuard, TrackSeoMetrics
│   ├── Models/             # 40+ Eloquent models (see §6)
│   ├── Providers/          # AppServiceProvider, ThemeServiceProvider, PluginServiceProvider, InstallerServiceProvider
│   ├── Services/           # Business logic services (see §7)
│   ├── Support/            # Value objects, formatters, embed code generators
│   ├── Traits/             # HasPrivacy
│   └── View/Components/    # WidgetColumn
├── bootstrap/
├── config/                 # Standard Laravel config (app, auth, cache, database, etc.)
├── database/
│   ├── migrations/         # 27 migration files (chronological, prefixed by date)
│   ├── seeders/            # DatabaseSeeder.php
│   └── factories/
├── Documents/              # Project documentation (README, API_DOCS, changelogs, guides)
├── installer/              # Visual web installer (views/)
├── lang/                   # 9 languages: ar, de, en, es, fa, fr, it, pt, tr
│   └── {locale}/
│       ├── messages.php    # Main translation file (thousands of keys)
│       ├── auth.php
│       ├── pagination.php
│       ├── passwords.php
│       └── validation.php
├── plugins/                # Plugin directory (each plugin in its own folder)
│   └── arabic-fixer/       # Example bundled plugin
├── public/                 # Web root (when document root points here)
│   ├── assets/
│   ├── themes/             # Symlinked or served theme assets
│   └── upload/             # User uploads (avatars, banners, images)
├── resources/
│   ├── views/              # Minimal (welcome.blade.php, widget component)
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php             # ~500 lines, all web routes (see §8)
│   ├── api.php             # REST API routes (Sanctum-based, alpha)
│   ├── installer.php       # Installer routes
│   └── console.php
├── storage/
├── tests/
│   ├── Feature/            # 26 feature tests
│   ├── Unit/
│   └── Concerns/           # SeedsSiteSettings trait
├── themes/
│   └── default/
│       ├── theme.json      # Theme metadata
│       ├── assets/         # CSS, JS, images (admin-duralux/ for admin)
│       └── views/          # All Blade templates (see §9)
├── upload/                 # Legacy upload directory
├── .env.example            # Environment template
├── .htaccess               # Subdirectory routing to public/
├── composer.json
├── package.json
├── vite.config.js
└── artisan
```

---

## 5. Controllers Reference

| Controller | Purpose |
|-----------|---------|
| `AuthController` | Login, register, logout |
| `SocialAuthController` | Google/Facebook OAuth |
| `ForgotPasswordController` / `ResetPasswordController` | Password reset flow |
| `HomeController` | Dashboard (`/home`), point conversion |
| `PortalController` | Community feed (`/portal`), smart feed algorithm |
| `StatusController` | Create posts (text, link, gallery, repost), image upload, link preview |
| `CommentController` | Load, store, delete comments |
| `ReactionController` | Toggle reactions |
| `MentionController` | `@mention` user lookup |
| `ForumController` | Forum index, categories, topics, CRUD, moderation |
| `DirectoryController` | Web directory CRUD, categories, metadata fetch |
| `StoreController` | Store products, knowledgebase, downloads |
| `AdsController` | Banner/link ad management, embed codes, promote, referrals |
| `SmartAdsController` | Smart ads CRUD, embed code |
| `AdsServingController` | Public ad serving endpoints (`bn.php`, `link.php`, `smart.php`, embed scripts) |
| `VisitController` | Visit exchange system |
| `ProfileController` | Profile view/edit, follow, privacy, badges, history |
| `MessageController` | Private messaging |
| `NotificationController` | Notification center, mark-all-read |
| `OrderRequestController` | Order requests CRUD, bidding, rating |
| `NewsController` | Public news pages |
| `ReportController` | Content reporting |
| `TagController` | Tag/hashtag pages |
| `PageController` | Static pages (privacy, terms, custom) |
| `AdminController` | **Main admin controller** — users, ads, forum, directory, store, widgets, menus, plugins, themes, settings, news, reports, emojis, knowledgebase |
| `AdminAdminsController` | Admin ACL management |
| `AdminSeoController` | SEO suite admin |
| `AdminPageController` | Custom pages admin CRUD |
| `AdminUpdatesController` | System update management |
| `SitemapController` | Dynamic sitemap generation |
| `SeoPublicController` | Public robots.txt |
| `CaptchaController` | CAPTCHA image generation |
| `InstallerController` | Visual web installer |

---

## 6. Key Models

| Model | Table | Purpose |
|-------|-------|---------|
| `User` | `users` | Members. `id=1` is super-admin. Has `pts` (points), ad credits (`nbanner`, `nlink`, `nsmart`), verified status |
| `Setting` | `setting` | Single-row site settings (name, description, theme slug, etc.) |
| `Option` | `options` | Key-value config store (plugins active state, misc settings) |
| `Status` | `status` | Community feed posts |
| `StatusLinkPreview` | `status_link_previews` | Link previews for posts |
| `StatusMention` | `status_mentions` | @mention records |
| `StatusRepost` | `status_reposts` | Quote repost records |
| `ForumTopic` | `f_topic` | Forum topics |
| `ForumComment` | `f_comment` | Forum/activity comments |
| `ForumCategory` | `f_cat` | Forum categories (with `visibility` field) |
| `ForumModerator` | `forum_moderators` | Moderator assignments |
| `ForumAttachment` | `forum_attachments` | File attachments |
| `Banner` | `banner` | Banner ads |
| `BannerImpression` | `banner_impressions` | Banner tracking |
| `Link` | `link` | Text/link ads |
| `SmartAd` | `smart_ads` | Smart ad campaigns |
| `SmartAdImpression` | `smart_ad_impressions` | Smart ad tracking |
| `Visit` | `visit` | Visit exchange entries |
| `Directory` | `directory` | Website directory listings |
| `DirectoryCategory` | `directory_cat` | Directory categories |
| `Product` | `product` | Store products |
| `ProductFile` | `product_files` | Store file versions |
| `Knowledgebase` | `knowledgebase` | Wiki-style articles per product (Markdown-based) |
| `Message` | `message` | Private messages |
| `Notification` | `notification` | User notifications |
| `News` | `news` | News articles |
| `Report` | `report` | Content reports |
| `Like` | `like` | Reactions + follows |
| `Menu` | `menu` | Navigation menus |
| `Page` | `pages` | Custom static pages |
| `Badge` / `UserBadge` / `BadgeShowcase` | `badges` / `user_badges` / `badge_showcase` | Gamification badges |
| `PointTransaction` | `point_transactions` | Points ledger |
| `Quest` / `QuestProgress` | `quests` / `quest_progress` | Daily/weekly quests |
| `OrderRequest` | `order_requests` | Service order requests |
| `SiteAdmin` | `site_admins` | Admin ACL (module-scoped permissions) |
| `UserPrivacySetting` | `user_privacy_settings` | Per-member privacy controls |
| `SeoSetting` / `SeoRule` / `SeoDailyMetric` | `seo_settings` / `seo_rules` / `seo_daily_metrics` | SEO engine |

---

## 7. Key Services

| Service | Purpose |
|---------|---------|
| `FeedService` | Hybrid ranking algorithm for `/portal?filter=all` (recency, follows, affinity, social proof, reactions, diversity penalties) |
| `AdminAccessService` | Centralized admin ACL — checks if user can access admin routes by module |
| `PointLedgerService` | Awards/deducts PTS, records transactions |
| `GamificationService` | Badge/quest progress tracking |
| `UserPrivacyService` | Enforces per-member privacy rules across profile, DMs, mentions, reposts |
| `MentionService` | Extracts and stores @mentions, sends notifications |
| `ContentFormatter` | Shared formatter for hashtags, links, Markdown, mentions |
| `LinkPreviewService` | Fetches URL metadata for link posts |
| `NotificationService` | Creates and manages notifications |
| `StatusActivityService` | Status/activity card rendering logic |
| `PluginManager` | Discovers, activates, manages plugins |
| `ThemeManager` | Theme discovery and management |
| `SeoManager` | Centralized SEO context (titles, OG, structured data) |
| `SeoAuditService` / `SeoMetricsService` | SEO analytics and auditing |
| `RobotsTxtService` | Dynamic robots.txt generation |
| `SmartAdAnalyzer` | Analyzes landing pages for Smart Ads metadata |
| `SmartAdGeoResolver` | Geo-targeting for Smart Ads |
| `V420SchemaService` | Graceful fallback detection for incomplete v4.2.0 upgrades |
| `TestingSafetyGuard` | Hard-fails tests unless they are using the isolated SQLite testing database |
| `UpdateSafetyService` | Preflight safety checks for updates: DB connection, writable paths, pending migrations, destructive migration detection |

---

## 8. Routing Patterns

### Route Files
- **`routes/web.php`** (~500 lines) — All web routes
- **`routes/api.php`** — REST API (Sanctum, alpha)
- **`routes/installer.php`** — Installer wizard

### URL Conventions
| Pattern | Example | Purpose |
|---------|---------|---------|
| `/f{id}` | `/f3` | Forum category |
| `/t{id}` | `/t42` | Forum topic |
| `/u/{username}` | `/u/john` | User profile |
| `/dr{id}` | `/dr5` | Directory listing (short) |
| `/quests` | `/quests` | Premium Quests Hub |
| `/cat/{id}` | `/cat/12` | Directory category (legacy alias) |
| `/kb/{name}:{article}` | `/kb/MyScript:setup` | Wiki article (redirects to create if missing) |
| `/kb/{name}?st={article}` | `/kb/MyScript?st=setup` | Wiki-style create/find article |
| `/e{id}`, `/p{id}` | `/e1` | Legacy profile redirects |

### Middleware Groups
- **`auth`** — Standard Laravel auth
- **`admin`** — `AdminMiddleware` (checks `AdminAccessService`)
- **Global middleware** — `SetLocale`, `UpdateUserOnline`, `CheckSystemVersion`, `InstallerGuard`, `TrackSeoMetrics`

### Admin Routes
All under `/admin` prefix with `['auth', 'admin']` middleware.

---

## 9. Theme & View System

### View Namespacing
All views use `theme::` namespace. Example:
```blade
return view('theme::home', $data);
@include('theme::partials.header.nav')
```

### View Directory (themes/default/views/)
```
layouts/       → master.blade.php (public), admin.blade.php, app.blade.php
partials/      → header/, activity/, ajax/, forum/, status/, widgets/
admin/         → All admin panel views (Duralux theme)
auth/          → login, register, password reset
portal/        → Community feed
forum/         → Forum pages
directory/     → Directory pages
store/         → Store pages
ads/           → Ad management views + serving views
profile/       → Profile, settings, privacy, badges, history
messages/      → Private messaging
notifications/ → Notification center
orders/        → Order requests
news/          → News pages
visits/        → Visit exchange
pages/         → Static pages (privacy, terms, custom)
```

### Theme Assets Helper
```blade
{{ theme_asset('css/styles.css') }}
<!-- Resolves to: /themes/{active_theme}/assets/css/styles.css -->
```

---

## 10. Localization (i18n)

- **9 languages:** `en`, `ar`, `de`, `es`, `fa`, `fr`, `it`, `pt`, `tr`
- **RTL languages:** `ar`, `fa` — detected via `locale_direction()` / `is_locale_rtl()`
- **Main file:** `lang/{locale}/messages.php` (thousands of keys)
- **Usage:** `__('messages.key_name')` or `@lang('messages.key_name')`
- **Switching:** Via `SetLocale` middleware, `?lang=xx` URL param, session + cookie persistence
- **RTL CSS:** Dedicated RTL override stylesheets for public theme, dark theme, and Duralux admin

---

## 11. Plugin System

- **Location:** `/plugins/{PluginName}/`
- **Metadata:** `plugin.json` (name, slug, version, author, thumbnail, latest, min_myads)
- **Boot file:** `boot.php` — loaded by `PluginServiceProvider` when activated
- **Activation state:** Stored in `options` table (`o_type = 'plugins'`)
- **Hooks:** WordPress-like `add_action()` / `add_filter()` via `App\Helpers\Hooks`
- **Admin:** Upload ZIP, activate/deactivate, GitHub update checks, changelog modal
- **Safety:** Active plugins cannot be deleted; compatibility locking via `min_myads`

---

## 12. Authentication & Authorization

- **Login:** Email or username (auto-detected)
- **Social:** Google and Facebook OAuth via Socialite (optional, config-based)
- **Registration defaults:** Standard signups start with avatar `upload/avatar.png` and cover `upload/cover.jpg`. Social signups keep the provider avatar but also start with cover `upload/cover.jpg`.
- **Hashing:** Bcrypt (with legacy MD5 auto-upgrade on login)
- **Admin:** `user_id=1` is **permanent super-admin** (cannot be removed)
- **Admin ACL:** `site_admins` table with module-scoped permissions, managed via `/admin/admins`
- **CAPTCHA:** On registration
- **CSRF:** Laravel middleware on all forms (installer routes excluded)
- **API Auth:** Sanctum bearer tokens

---

## 13. Key Architectural Patterns

### Points System (PTS)
- Central virtual currency for the platform
- Earned by: posting, commenting, reactions, referrals, visit exchange, quest completion
- Spent on: ad credits (banner, link, smart), store purchases
- Tracked via `PointLedgerService` and `point_transactions` table

### Smart Feed Algorithm (FeedService)
- `/portal?filter=all` uses hybrid ranking: recency decay, follow boost (+20), social proof (+10), author/content affinity, reaction weight, comment weight, repost count, capped view boost, diversity penalties
- `/portal?filter=me` stays chronological (followed accounts + self)
- 5-minute cache

### Deletion Pattern
- Unified 'in-place' confirmation system across all modules
- Centralized in `master.blade.php`
- Replaces content with themed confirmation box, supports type-specific API routing

### Schema Compatibility
- `V420SchemaService` — graceful fallback when upgrade tables are missing
- Controllers/views degrade gracefully with translated upgrade notices instead of 500 errors

### Ad Serving
- Legacy endpoints preserved: `bn.php`, `link.php`, `smart.php`
- Modern embed scripts: `/embed/banner.js`, `/embed/link.js`, `/embed/smart.js`
- Slot-based injection (not `document.write`)
- Repeat-window avoidance for banners

### Wiki/Markdown System (v4.2.0)
- **Engine:** Client-side rendering using `marked.js` and `DOMPurify`.
- **Editor:** Integrated **StackEdit** for premium Markdown editing experience.
- **Missing Pages:** Wiki-style redirection for non-existent pages (auto-prompts creation).
- **Storage:** Raw Markdown stored in `options.o_valuer`.
- **Review System:** Side-by-side AJAX preview for historical/pending versions using a `340px 1fr` grid (`.kb-review-layout`).
 - **Security:** Strict HTML sanitization on the client side to prevent XSS.

### Product Suspension System
- **Mechanism:** Uses `Option` records with `o_type = 'store_status'` and `name = 'suspended'`.
- **Visibility:** `Product::visible()` and `Status::visible()` scopes are overridden to integrate suspension logic—hidden from the public, but visible to owners and admins.
- **Sitemap:** `SitemapController` respects product visibility, automatically excluding suspended products from the public XML index.
- **Enforcement:** `StoreController` blocks unauthorized viewing and downloading of suspended products.
- **UI:** Includes a high-visibility suspension notice on the product page, status badges in the store listing, and "Suspended" badges on community activity cards.

---

## 14. Environment Configuration

Key `.env` variables:
```env
APP_NAME=myads
APP_URL=http://localhost
APP_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

SESSION_DRIVER=file
CACHE_STORE=file

# Social Login (optional — leave empty to disable)
FACEBOOK_CLIENT_ID=
FACEBOOK_CLIENT_SECRET=
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
```

---

## 15. Testing

- **Framework:** PHPUnit 11
- **Location:** `tests/Feature/` (26 test files), `tests/Concerns/`
- **Run:** `php artisan test --env=testing` or `composer test`
- **Isolation:** Tests must run only against `.env.testing` with `sqlite` and `database/testing.sqlite`. Never point tests at a MySQL/MariaDB site database.
- **Coverage areas:** Installer flow, forum features, banner/link/smart ads, store, SEO, locale/RTL, notifications, profile/follow, directory, v4.2.0 features (community, privacy, ACL, translations, upgrade fallbacks)

---

## 16. Development Commands

```bash
# Install dependencies
composer install
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate --force

# Dev server (concurrent: server + queue + logs + vite)
composer dev

# Run tests
composer test
# or
php artisan test --env=testing

# Run update safety preflight before upgrading
php artisan myads:update:preflight

# Generate sitemap
# Via admin panel or: GET /admin/sitemap/generate

# Storage symlink
php artisan storage:link
```

---

## 17. Rules for Agents

### ⚠️ CRITICAL RULES

1. **Never modify files in `vendor/`** — these are managed by Composer.
2. **Never commit `.env`** — it contains secrets. Use `.env.example` for templates.
3. **User ID 1 is sacred** — it's the permanent super-admin. Never delete or demote it.
4. **Always use `theme::` namespace** for view references in controllers (e.g., `view('theme::home')`).
5. **Always wrap new DB queries in try-catch** if they reference tables that may not exist on older installs (follow `V420SchemaService` pattern).
6. **Never use `document.write`** in ad serving — use slot-based injection.
7. **Never run tests or destructive DB commands against a live site database.** Any `php artisan test`, `migrate:fresh`, `db:wipe`, or similar command requires a verified isolated test database first.
8. **Release migrations must be non-destructive in `up()`.** Table drops, column drops, truncates, and destructive raw SQL belong only in explicit manual recovery paths, never in normal upgrade migrations.
9. **Codex incident note (2026-03-27):** A previous non-isolated test run rebuilt a real MYADS database. Future agents must treat DB isolation and backup requirements as hard safety gates, not suggestions.

### Code Conventions

10. **Translations:** Never hardcode English strings in Blade views or controllers. Use `__('messages.key_name')`. Add keys to ALL 9 language files.
11. **RTL:** Always test UI changes in both LTR and RTL. Use `locale_direction()` helper. Add RTL overrides when needed.
12. **Routes:** Name all routes. Follow existing naming convention: `module.action` (e.g., `forum.create`, `admin.users.edit`).
13. **Models:** Place in `app/Models/`. Define `$fillable` or `$guarded`. Specify `$table` name explicitly (many use legacy table names like `f_topic`, `f_cat`).
14. **Services:** Business logic goes in `app/Services/`, not controllers. Controllers should be thin.
15. **Support classes:** Value objects, formatters, settings bags go in `app/Support/`.
16. **Middleware:** Registered in `bootstrap/app.php`. Admin routes use `['auth', 'admin']`.
17. **Admin views:** Follow Duralux design patterns (card-based, `hstack`, `gap-3`).
18. **Blade partials:** Reuse existing partials. Activity cards: `partials/activity/`. Forum: `partials/forum/`.

### Migration Conventions

19. **Migration filenames:** Use date prefix format `YYYY_MM_DD_HHMMSS_description.php`.
20. **Never modify existing migrations** — create new ones for schema changes.
21. **Repair migrations:** For upgrade-safe schema changes, use `Schema::hasTable()` / `Schema::hasColumn()` checks.

### Plugin/Theme Development

22. **Plugins** must have `plugin.json` and a `boot.php` entry point.
23. **Themes** must have `theme.json` and follow the view directory structure of `themes/default/views/`.
24. **Hooks:** Use `add_action()` / `add_filter()` from `App\Helpers\Hooks` — never monkey-patch core files.

### Testing

25. **Write feature tests** for new features in `tests/Feature/`.
26. **Use `SeedsSiteSettings` trait** when tests need the `setting` table seeded.
27. **Validate PHP syntax** after edits: `php -l <file>`.

### Security

28. **Sanitize all user input.** Use Laravel validation and Blade `{{ }}` escaping.
29. **Admin routes** must always be in the `admin` prefix group with `AdminMiddleware`.
30. **File uploads:** Validate MIME types, limit sizes, store in `public/upload/`.
31. **CSRF:** Never disable CSRF except for installer routes and ad serving endpoints.

---

## 18. Common Tasks Quick Reference

### Common Tasks Quick Reference

| Task | How |
|------|-----|
| Add a new widget type | Update `$allowedTypes` in `AdminController@widgetForm` and `storeWidget` validation → add option to `themes/default/views/admin/widgets.blade.php` → add case in `resources/views/components/widget-column.blade.php` → create partial in `themes/default/views/partials/widgets/` |
|------|-----|
| Add a new page | Create controller method → add route in `web.php` → create Blade in `themes/default/views/` → add translation keys to all 9 locales |
| Add admin section | Add routes under `admin` prefix → create admin Blade in `themes/default/views/admin/` → update `AdminAccessService` module list if ACL-gated |
| Add translation key | Edit `lang/{locale}/messages.php` in ALL 9 directories |
| Create migration | `php artisan make:migration description` → use `Schema::hasTable()` guards for safety |
| Add new model | `php artisan make:model Name` → specify `$table`, `$fillable` → place in `app/Models/` |
| Modify theme | Edit files in `themes/default/views/` and `themes/default/assets/` |
| Create plugin | Make folder in `plugins/`, add `plugin.json` + `boot.php`, activate from admin |
| Test changes | `php artisan test --filter=TestName` |
| Check RTL | Switch locale to `ar` or `fa` via `?lang=ar` |

---

## 19. Related Documentation

| Document | Path |
|----------|------|
| Project Overview | `Documents/README.md` |
| Installation Guide | `Documents/INSTALLATION.md` |
| Upgrade Guide | `Documents/UPGRADE.md` |
| System Requirements | `Documents/SYSTEM_REQUIREMENTS.md` |
| API Documentation | `Documents/API_DOCS.md` |
| Theme Guide | `Documents/THEME_GUIDE.md` |
| Plugin Guide | `Documents/PLUGIN_GUIDE.md` |
| Changelog | `Documents/changelogs.md` |
| Security Policy | `SECURITY.md` |

---

---

## 19A. Security Suite Update (2026-03-27)

- **Controllers:** Added `AdminSecurityController` for `/admin/security`, `/admin/security/ip-bans`, `/admin/security/sessions`, and `/admin/confirm-password`.
- **Models:** Added `SecurityIpBan` (`security_ip_bans`) and `SecurityMemberSession` (`security_member_sessions`). `User` now supports `public_uid` for public shortcut links, and `Message` supports optional encrypted storage for new private messages.
- **Services & Support:** Added `SecuritySettings`, `SecurityPolicyService`, `SecurityThrottleService`, `SecuritySessionService`, `LocalUrlSafetyInspector`, and `UrlSafetyInspectorInterface`.
- **Middleware:** Added `BlockBannedIp`, `TrackMemberSecuritySession`, and `RequireAdminPasswordConfirmation`. Global web middleware now includes IP blocking and member session tracking.
- **Routes & ACL:** Added admin ACL module `security`, Duralux admin navigation for Security, admin password confirmation routes, IP ban routes, and session revoke routes. Public shortcut route `/u/id/{id}` now supports canonical redirects toward `public_uid` when enabled.
- **Schema:** Added migration `2026_03_27_120000_add_security_suite_tables.php` creating `security_ip_bans`, `security_member_sessions`, and `users.public_uid` with upgrade-safe guards.
- **Security Behavior:** Centralized link/domain safety now applies to registration, login throttling, posts, comments, forum topics, private messages, banner/link/smart/visit ads, and link previews. Private messages can be stored as `enc:` payloads using Laravel `Crypt`, `/messages/{id}` now prefers opaque encrypted conversation identifiers while still accepting legacy numeric IDs, and mixed legacy/encrypted threads display a lightweight separator when encrypted replies begin.
- **Public Profile Shortcuts:** Header and sidebar avatar/profile shortcuts now consistently use `route('profile.short', $user->publicRouteIdentifier())`, so enabling public member IDs updates those public-facing links immediately.
- **Testing:** Added `tests/Feature/SecuritySuiteFeatureTest.php` covering ACL for the security module, admin password confirmation middleware behavior, private message encryption, and `public_uid` shortcut routing.

---

## 19B. Update Safety & Testing Isolation (2026-03-27)

- **Commands:** Added `myads:update:preflight` to validate database connectivity, writable paths, pending migrations, and destructive migration patterns before upgrades.
- **Services & Support:** Added `TestingSafetyGuard`, `UpdateSafetyService`, and `UpdatePreflightReport`.
- **Testing Isolation:** Added tracked `.env.testing`, switched automated tests to `database/testing.sqlite`, and hard-failed the test bootstrap if the environment is not isolated from the live database.
- **Updater Safety:** `/admin/updates` now blocks execution unless the operator confirms database and file backups and the preflight report passes.
- **Documentation:** Added `Documents/UPGRADE.md` and strengthened backup warnings in the installation guide.

---

## 20. Maintaining This File

> **⚠️ RULE: This `Agents.md` file MUST be kept up to date.**

Whenever you make changes that affect any of the following, **update this file accordingly before finishing the task:**

- Adding, renaming, or removing **controllers, models, services, or middleware**
- Adding or changing **routes or URL patterns**
- Adding new **database tables or migrations**
- Adding new **plugins, themes, or languages**
- Changing **authentication, authorization, or admin ACL logic**
- Introducing new **architectural patterns or conventions**
- Adding or modifying **environment variables**
- Changing the **directory structure**
- Adding new **rules or coding conventions**
- Updating the **project version**

If in doubt, update it. An outdated `Agents.md` causes future agents to make wrong assumptions and waste time. Keeping it accurate is as important as keeping the code itself correct.

---

*Last updated: 2026-03-27 — MYADS v4.2.0*
