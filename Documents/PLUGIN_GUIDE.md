# Developer Plugin Guide — MYADS v4.7.0 (Limitless & Secure Architecture)

MYADS features a modular, enterprise-grade **Plugin Architecture** built natively on **Laravel 12 (PHP 8.2+)**. The plugin system is designed around two core principles: **Unlimited Extensibility** and **Guaranteed Platform Safety**.

Developers can hook into core lifecycle events, register independent routes and APIs, execute database migrations, inject dynamic Blade widgets, override any template without modifying core files, register alternative rich text editors, and automate background workflows — all while protected by automated security checks and Safe-Mode crash isolation.

---

## 1. Directory Structure

Plugins reside inside the root `/plugins/` directory. Each plugin must occupy its own subdirectory named after its slug:

```text
plugins/
└── your-plugin-slug/
    ├── plugin.json                 # Required: Plugin manifest
    ├── boot.php                    # Optional: Bootstrapped on every request when active
    ├── helpers.php                 # Optional: Auto-loaded global helper functions
    ├── routes.php                  # Optional: Web & API routes
    ├── activate.php                # Optional: Executed immediately upon activation
    ├── deactivate.php              # Optional: Executed immediately upon deactivation
    ├── uninstall.php               # Optional: Executed upon deletion/uninstallation
    ├── database/
    │   └── migrations/             # Optional: Database migrations auto-executed on activation
    ├── views/                      # Optional: Blade templates (accessible via your-plugin-slug::view)
    │   └── overrides/              # Optional: Dynamic View Overrides (Limitless UI customization)
    │       ├── theme/              # Overrides any frontend theme view
    │       └── admin/              # Overrides any administrative Duralux view
    ├── lang/                       # Optional: Namespaced multi-language translations (ar, en, fr...)
    │   ├── ar/messages.php
    │   └── en/messages.php
    └── src/                        # Optional: PSR-4 Autoloaded PHP classes (Controllers, Models, Services)
        ├── Controllers/
        ├── Middleware/
        ├── Models/
        └── Services/
```

---

## 2. Plugin Manifest (`plugin.json`)

Every plugin **must** provide a valid `plugin.json` manifest at its root. The core loader inspects this manifest for identification, version bounding, licensing, and auto-discovery:

```json
{
    "name": "Auto Announcer Pro",
    "slug": "auto-announcer",
    "version": "1.0.0",
    "description": "Broadcast automated multi-channel announcements to the community feed and admin notices.",
    "author": "Your Name / Organization",
    "author_url": "https://github.com/yourname",
    "thumbnail": "thumbnail.png",
    "siteweb": "https://example.com/auto-announcer",
    "ADStn_url": "auto-announcer-pro",
    "latest": "https://github.com/yourname/auto-announcer/releases/latest",
    "settings_url": "/admin/plugins/auto-announcer/settings",
    "admin_menu": {
        "title": "Announcements",
        "url": "/admin/plugins/auto-announcer",
        "icon": "feather-bell"
    },
    "autoload": {
        "psr-4": {
            "MyAds\\Plugins\\AutoAnnouncer\\": "src/"
        }
    },
    "min_myads": "4.7.0",
    "max_myads": "4.7.x"
}
```

### Manifest Schema Reference

| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `name` | string | **Yes** | Human-readable display name. |
| `slug` | string | **Yes** | Unique identifier (lowercase alphanumeric, dashes allowed). |
| `version` | string | **Yes** | Semantic version string (e.g. `1.0.0`). |
| `description` | string | **Yes** | Summary of plugin capabilities. |
| `author` | string | **Yes** | Developer or studio name. |
| `author_url` | string | No | Author profile or portfolio link. |
| `thumbnail` | string | No | Image filename located in the plugin root. |
| `siteweb` | string | No | Official documentation or homepage. |
| `ADStn_url` | string | No | Central marketplace slug on ADStn (`www.adstn.ovh`) for licensing & update verification. |
| `latest` | string | No | Fallback GitHub latest release URL. |
| `settings_url` | string | No | Direct path to plugin settings in the Admin Panel. |
| `admin_menu` | object | No | Object containing `title`, `url`, and `icon` (Feather or FontAwesome) for sidebar injection. |
| `autoload` | object | No | Custom PSR-4 mapping (if omitted, automatic PSR-4 mapping is applied). |
| `min_myads` | string | No | Minimum platform version required (e.g. `4.7.0`). |
| `max_myads` | string | No | Maximum supported platform version bounding. |
| `requires_plugins`| array | No | List of prerequisite plugin slugs that must be active. |

---

## 3. Automatic PSR-4 Autoloading & Helpers

MYADS automatically registers PSR-4 autoloading for active plugins without requiring manual `spl_autoload_register` calls:

1. **Declared Mapping:** If `"autoload": { "psr-4": { ... } }` is provided in `plugin.json`, it is registered directly.
2. **Automatic Convention:** If omitted, MYADS automatically maps:
   - `MyAds\Plugins\<StudlySlug>\` $\rightarrow$ `plugins/<slug>/src/`
   - `Plugins\<StudlySlug>\` $\rightarrow$ `plugins/<slug>/src/`
3. **Global Helpers:** If `plugins/<slug>/helpers.php` exists, it is automatically `require_once`'d on application boot.

---

## 4. Global Hooks, Actions & Filters API

MYADS implements a complete, WordPress-grade Hook engine accessible via global functions, the `\App\Helpers\Hooks` facade, and custom Blade directives.

### 4.1 Global Helper Functions

```php
// Register an Action Hook
add_action(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void

// Execute an Action Hook
do_action(string $hook, ...$args): void

// Register a Filter Hook
add_filter(string $hook, callable $callback, int $priority = 10, int $accepted_args = 1): void

// Apply a Filter Hook
apply_filters(string $hook, mixed $value, ...$args): mixed

// Inspection & Removal
has_action(string $hook, ?callable $callback = null): bool
has_filter(string $hook, ?callable $callback = null): bool
remove_action(string $hook, callable $callback, int $priority = 10): bool
remove_filter(string $hook, callable $callback, int $priority = 10): bool
```

### 4.2 Blade Directives

You can invoke actions and filters directly within Blade templates cleanly:

```blade
{{-- Execute an Action Hook in Blade --}}
@action('post_card_footer', $activity)

{{-- Apply and render a Filter Hook in Blade --}}
@filter('post_content_render', $text, $activity)
```

---

## 5. Comprehensive Hook Catalog

The table below lists the core hook points available across the MYADS ecosystem:

### 5.1 Posts, Feeds & Content Lifecycle

| Hook Name | Type | Arguments | Description |
| :--- | :--- | :--- | :--- |
| `post_created` | Action | `$status` | Fired when any post/status is created anywhere across the platform (web, API, admin). |
| `post_updated` | Action | `$status` | Fired when a post/status is updated. |
| `post_deleted` | Action | `$status` | Fired when a post/status is deleted. |
| `post_card_header` | Action | `$activity` | Injected inside the post card header before user status metadata. |
| `post_card_footer` | Action | `$activity` | Injected immediately below the post body before content actions. |
| `post_card_actions` | Action | `$activity` | Injected inside the reaction/comment action bar. |
| `post_card_options` | Action | `$activity` | Injected inside the post options dropdown. |
| `post_content_render` | Filter | `$content, $activity` | Filters post text before rendering (BBCode, shortcodes, badges). |

### 5.2 Users, Authentication & Profiles

| Hook Name | Type | Arguments | Description |
| :--- | :--- | :--- | :--- |
| `user_registered` | Action | `$user` | Fired immediately after a new member account is created. |
| `user_logged_in` | Action | `$user` | Fired on successful member authentication (session, remember token). |
| `user_logged_out` | Action | `$user` | Fired on member sign-out. |
| `user_updated` | Action | `$user` | Fired when profile information is modified. |
| `user_profile_header` | Action | `$user` | Injected on the member profile page between header cover and navigation. |
| `user_profile_navigation_tabs` | Action | `$user, $activeTab` | Injected inside the member profile horizontal navigation tabs slider. |
| `user_profile_content` | Action | `$user` | Injected at the bottom of the member profile main content grid. |

### 5.3 Navigation & Frontend Layouts

| Hook Name | Type | Arguments | Description |
| :--- | :--- | :--- | :--- |
| `theme_master_head_end` | Action | none | Injected right before `</head>` on the main frontend theme. |
| `theme_master_before_body_close` | Action | none | Injected right before `</body>` on the main frontend theme. |
| `theme_sidebar_nav_start` | Action | none | Injected at the start of the collapsed desktop navigation bar. |
| `theme_sidebar_nav_end` | Action | none | Injected at the end of the collapsed desktop navigation bar. |
| `theme_sidebar_menu_start` | Action | none | Injected at the start of the expandable desktop side menu. |
| `theme_sidebar_menu_end` | Action | none | Injected at the end of the expandable desktop side menu. |
| `registered_plugin_widgets` | Filter | `$widgets, $place` | Injects dynamic widgets into `<x-widget-column>` columns. |

### 5.4 Administrative Panel (Duralux)

| Hook Name | Type | Arguments | Description |
| :--- | :--- | :--- | :--- |
| `admin_sidebar_menu` | Action | none | Injects custom menu items into the admin navigation sidebar. |
| `admin_head` | Action | none | Injected inside `<head>` in the administrative layout. |
| `admin_master_before_body_close` | Action | none | Injected before `</body>` in the administrative layout. |
| `admin_notifications` | Filter | `$notifications, $user` | Injects custom alerts into the admin header notifications dropdown. |
| `admin_ticket_reply_header` | Action | `$ticket` | Injected into the support ticket reply screen. |

### 5.5 Rich Text Editor Engine (`RichTextEditorService`)

| Hook Name | Type | Arguments | Description |
| :--- | :--- | :--- | :--- |
| `registered_rich_text_editors` | Filter | `$editors` | Registers custom WYSIWYG editors (TinyMCE, CKEditor, EasyMDE). |
| `render_custom_editor_assets` | Action | `$activeEditor` | Injects required CSS/JS CDN assets for the active editor. |
| `render_custom_editor_js` | Action | `$elementId, $activeEditor` | Injects client-side JavaScript initialization for editor instances. |

---

## 6. Limitless UI Customization: Dynamic View Overrides

In MYADS v4.7.0, plugins can override **any template or view in the entire platform** without editing core code.

### How Dynamic View Overriding Works

Place replacement `.blade.php` files inside `plugins/<slug>/views/overrides/`:

1. **Frontend Theme Views:** Put replacement views inside `views/overrides/theme/`.  
   *Example:* `plugins/my-plugin/views/overrides/theme/forum/video.blade.php` overrides the default `theme::forum.video` watch page.
2. **Administrative Views:** Put replacement views inside `views/overrides/admin/`.  
   *Example:* `plugins/my-plugin/views/overrides/admin/admin/dashboard.blade.php` overrides the admin dashboard.
3. **General Views:** Any view placed in `views/overrides/` is prepended to Laravel's global view finder.

> **Zero Risk:** When your plugin is enabled, your overridden view takes precedence. If the plugin is deactivated or uninstalled, MYADS instantly and automatically falls back to the original core view.

---

## 7. Database Migrations & Models

Plugins can create independent database tables or alter existing schemas safely.

### Migrations Directory
Store migration files inside `plugins/<slug>/database/migrations/`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plugin_announcements')) {
            Schema::create('plugin_announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('message');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('plugin_announcements');
    }
};
```

- When the administrator activates the plugin via the Admin Panel, migrations are run automatically.
- Migration rollback is executed automatically when the plugin is uninstalled.

---

## 8. Multi-Layer Security Architecture

MYADS enforces strict security policies to protect the platform against malicious, corrupted, or faulty extensions:

### 8.1 Pre-Activation PHP Syntax Validation
Before activating any plugin, `PluginManager::validatePluginSyntax()` parses all `.php` files using `token_get_all(TOKEN_PARSE)`.
- If an extension contains syntax errors (e.g. parse errors, unclosed brackets), activation is aborted immediately with an explicit error message.
- Prevents White Screen of Death (WSOD) or fatal compile crashes.

### 8.2 Safe-Mode Crash Quarantine
If an active plugin encounters an unhandled runtime error during `boot.php`:
1. `PluginServiceProvider` catches the exception in an isolated block.
2. The error and stack trace are logged to `storage/logs/laravel.log`.
3. The faulty plugin is automatically quarantined (`o_valuer = 0` in database).
4. The crash diagnostic is cached and surfaced to the administrator in `/admin/plugins`.
5. **The rest of the website remains completely online and unaffected.**

### 8.3 Archive Upload Inspection (`FileUploadSecurityService`)
When plugins are installed via ZIP package upload:
- **Path Traversal Protection:** Rejects archives containing directory traversal sequences (`..`, leading `/` or `\`).
- **Executable Script Blacklisting:** Blocks standalone executable scripts (`.sh`, `.bat`, `.cmd`, `.exe`, `.cgi`).
- **Zip Bomb Mitigation:** Verifies archive integrity, file size, and uncompressed ratios.

---

## 9. Paid Licensing & ADStn Marketplace Integration

For commercial extensions distributed via the central **ADStn Marketplace** (`www.adstn.ovh`):

1. **Marketplace Slug:** Declare `"ADStn_url": "your-product-slug"` in `plugin.json`.
2. **License Activation:** In `activate.php`, verify the customer's purchase key:

```php
use App\Models\Option;

$licenseKey = request('license_key');
$response = http_secure()->post('https://www.adstn.ovh/api/license/verify', [
    'license_key' => $licenseKey,
    'domain' => request()->getHost(),
    'plugin' => 'your-plugin-slug',
]);

if ($response->successful() && $response->json('valid')) {
    Option::updateOrCreate(
        ['name' => 'your-plugin-slug_license', 'o_type' => 'plugins'],
        ['o_valuer' => hash('sha256', $licenseKey . config('app.key'))]
    );
}
```

3. **Secure Automated Updates:** MYADS uses `http_secure()` with production SSL verification to check for updates against the central marketplace endpoint.

---

## 10. Step-by-Step Tutorial: Building a Plugin

Let's build a functional plugin called **VIP Member Badges**:

### Step 1: Create the Directory
```text
plugins/vip-badges/
├── plugin.json
├── boot.php
├── routes.php
└── views/
    └── widget.blade.php
```

### Step 2: Define `plugin.json`
```json
{
    "name": "VIP Member Badges",
    "slug": "vip-badges",
    "version": "1.0.0",
    "description": "Showcases custom VIP member badges on user profiles and feed cards.",
    "author": "MYADS Developer",
    "min_myads": "4.7.0"
}
```

### Step 3: Implement `boot.php`
```php
<?php

use App\Helpers\Hooks;

// Injected into member profiles
Hooks::add_action('user_profile_header', function ($user) {
    if ($user->isAdmin() || $user->pts >= 1000) {
        echo '<div class="vip-badge-ribbon"><i class="fa fa-star"></i> VIP Verified Member</div>';
    }
});

// Injected into post cards
Hooks::add_action('post_card_footer', function ($activity) {
    if ($activity->user && $activity->user->pts >= 1000) {
        echo '<span class="badge bg-warning text-dark"><i class="fa fa-gem"></i> VIP Contributor</span>';
    }
});

// Dynamic widget in the sidebar
Hooks::add_filter('registered_plugin_widgets', function (array $widgets, string $place) {
    if ($place === 'home_sidebar') {
        $widgets[] = [
            'id' => 'vip_badges_widget',
            'title' => 'VIP Members Showcase',
            'view' => 'vip-badges::widget',
            'sort_order' => 5,
        ];
    }
    return $widgets;
});
```

### Step 4: Create the Widget View (`views/widget.blade.php`)
```blade
<div class="widget-box">
    <p class="widget-box-title">🏆 Top VIP Members</p>
    <div class="widget-box-content">
        <p>Support our high-reputation webmasters!</p>
    </div>
</div>
```

Activate the plugin in **Admin Panel $\rightarrow$ Plugins** to see the VIP badges and widget appear across the platform!
