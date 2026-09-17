# Plugin Guide

MYADS features a robust, extensible Plugin Architecture built natively on **Laravel 12 (PHP 8.2+)**. This system enables developers to create independent, modular extensions that hook into core lifecycle events, register routes, inject Blade widgets, provide custom rich text editors, and modify behaviors without modifying core source code.

---

## 1. Plugin Directory Structure

Plugins are stored inside the root `/plugins/` directory. Each plugin must reside in its own subdirectory named after its slug:

```text
plugins/
└── AutoCommenter/
    ├── AutoCommenter.php   # Main plugin boot / entry point file
    ├── plugin.json         # Plugin manifest (required JSON format)
    ├── helpers.php         # Optional: Custom helper functions
    ├── controllers/        # Optional: Plugin-specific controllers
    ├── views/              # Optional: Blade views specific to the plugin
    ├── lang/               # Optional: Multilingual translation files
    │   ├── ar/messages.php
    │   └── en/messages.php
    ├── css/                # Optional: Embedded stylesheets
    ├── js/                 # Optional: Embedded JavaScript files
    ├── thumbnail.png       # Optional: Plugin thumbnail image (recommended)
    ├── install.sql         # Optional: Executed on initial activation
    ├── README.md           # Optional: Core documentation (rendered in admin)
    ├── changelogs.md       # Optional: Release history (rendered in admin)
    └── screenshots.md      # Optional: Visual previews (rendered in admin)
```

---

## 2. The `plugin.json` Manifest

Every plugin **must** include a valid `plugin.json` file in its root folder. The core loader relies on this manifest to discover, identify, and manage the plugin.

```json
{
    "name": "Auto Commenter Pro",
    "slug": "auto-commenter",
    "description": "Automatically post contextual comments on newly published community topics.",
    "version": "1.0.0",
    "author": "mrghozzi",
    "author_url": "https://github.com/mrghozzi",
    "thumbnail": "thumbnail.png",
    "siteweb": "https://www.example.com",
    "ADStn_url": "auto-commenter-pro",
    "latest": "https://github.com/mrghozzi/auto-commenter/releases/latest",
    "settings_url": "/admin/plugins/auto-commenter/settings",
    "admin_menu": [
        {
            "title": "Auto Commenter",
            "url": "/admin/plugins/auto-commenter",
            "icon": "fa fa-comments"
        }
    ],
    "min_myads": "4.6.0",
    "max_myads": "4.6.x"
}
```

### Manifest Properties

| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `name` | string | **Yes** | Human-readable display name of the plugin. |
| `slug` | string | **Yes** | Unique alphanumeric identifier (should match folder name). |
| `version` | string | **Yes** | Semantic version string (e.g. `1.0.0`). |
| `description` | string | **Yes** | Short summary explaining what the plugin does. |
| `author` | string | **Yes** | Developer or organization name. |
| `author_url` | string | No | Website or GitHub profile link of the author. |
| `thumbnail` | string | No | Thumbnail image filename in plugin root (e.g. `thumbnail.png`). |
| `siteweb` | string | No | Official documentation or marketing website URL. |
| `ADStn_url` | string | No | Marketplace slug on the central ADStn marketplace (`www.adstn.ovh`) for automated licensing and update checks. |
| `latest` | string | No | GitHub latest release URL for update checks (fallback if `ADStn_url` is omitted). |
| `settings_url` | string | No | Direct URL to plugin configuration in Admin Panel (e.g. `/admin/plugins/{slug}/settings`). |
| `admin_menu` | array | No | Array of administration navigation items (`title`, `url`, `icon`, `permission`) dynamically registered in the sidebar. |
| `min_myads` | string | No | Minimum compatible MYADS version (e.g. `4.6.0`). |
| `max_myads` | string | No | Maximum compatible MYADS version bounding (e.g. `4.6.x`). |

---

## 3. Dynamic Plugin Architecture & Auto-Discovery

MYADS v4.5+ uses a zero-hardcoding dynamic plugin discovery engine inside `PluginServiceProvider`:

- **Dynamic File Discovery:** In local and testing environments, the provider inspects `/plugins/*` automatically.
- **Database Option Resolution:** In production, active plugins stored in the `options` table (`o_type = 'plugin'`) are resolved dynamically by matching their registered slug against physical `plugin.json` manifests.
- **Isolated Bootstrapping:** Active plugins load their entry files on application boot without any manual registration in core service providers.

---

## 4. Hooks, Actions, and Filters

MYADS provides a powerful WordPress-inspired Hook system via the `Hooks` facade and global helper functions.

### 4.1 Filters (`add_filter` / `apply_filters`)
Filters allow plugins to intercept and modify variables or rendered output before they are displayed or processed by the system.

```php
// Registering a filter in your plugin
add_filter('post_content_render', function (string $content) {
    // Replace custom BBCode or add smiley shortcodes
    return str_replace(':smile:', '😄', $content);
});
```

### 4.2 Actions (`add_action` / `do_action`)
Actions allow plugins to execute custom logic, fire background jobs, or render HTML at specific application lifecycle moments.

```php
// Registering an action in your plugin
add_action('after_user_register', function ($userId) {
    // Send external notification or initialize custom user rewards
    app(\App\Services\ExternalNotifier::class)->notify($userId);
});

// Adding custom administrative sidebar menu links dynamically
add_action('admin_sidebar_menu', function () {
    echo '<li class="sidebar-item"><a class="sidebar-link" href="' . url('/admin/my-plugin') . '"><i class="fa fa-cogs"></i> <span>Custom Tool</span></a></li>';
});
```

---

## 5. Rich Text Editor Extensibility

Starting in v4.5.2, plugins can provide custom WYSIWYG / Rich Text editors (such as TinyMCE, CKEditor, or EasyMDE) utilizing the `RichTextEditorService` hook engine.

### Integration Hooks

1. **`registered_rich_text_editors`**: Adds your custom editor to the available editor selector in `/admin/settings`.
2. **`render_custom_editor_assets`**: Injects required external CSS/JS libraries into page headers.
3. **`render_custom_editor_js`**: Injects client-side initialization script for the editor instance.

```php
// Example: Registering TinyMCE 7 from a plugin
add_filter('registered_rich_text_editors', function (array $editors) {
    $editors['tinymce'] = [
        'name' => 'TinyMCE 7 (Plugin Editor)',
        'description' => 'Advanced WYSIWYG editor with full formatting and media support.',
        'version' => '7.6.0'
    ];
    return $editors;
});

add_action('render_custom_editor_assets', function ($activeEditor) {
    if ($activeEditor === 'tinymce') {
        echo '<script src="https://cdn.jsdelivr.net/npm/tinymce@7/tinymce.min.js"></script>';
    }
});

add_action('render_custom_editor_js', function ($activeEditor, $selector) {
    if ($activeEditor === 'tinymce') {
        echo "<script>tinymce.init({ selector: '{$selector}', plugins: 'link image lists table code' });</script>";
    }
});
```

> **Automatic Fallback:** If a custom editor plugin is deactivated, MYADS automatically falls back to the default `quill` editor to prevent editor failures.

---

## 6. Dynamic Blade Widgets

Plugins can register custom interactive widgets into any active layout column using the `registered_plugin_widgets` filter on the `<x-widget-column>` component:

```php
add_filter('registered_plugin_widgets', function (array $widgets, string $place) {
    if ($place === 'home_sidebar' || $place === 'global_sidebar') {
        $widgets[] = [
            'id' => 'my_plugin_weather_widget',
            'title' => __('my-plugin::messages.weather_widget_title'),
            'view' => 'my-plugin::widgets.weather',
            'sort_order' => 10,
        ];
    }
    return $widgets;
}, 10, 2);
```

---

## 7. Administrative Management & Diagnostics

### 7.1 Admin Console Controls (`/admin/plugins`)
- **Upload & Install:** Administrators can upload a packaged `.zip` archive. The system validates the `plugin.json` schema, unzips to `/plugins/{slug}`, and sets permissions.
- **Activation & Deactivation:** Toggling an extension updates the `options` table immediately. Active plugins must be deactivated prior to deletion.
- **One-Click Auto-Updates:** Update checks compare current manifest version with GitHub or ADStn marketplace releases.

### 7.2 Hooks & Plugins Inspector (`/admin/plugins/inspector`)
Administrators can inspect all active action and filter hooks in real-time, view callback handlers, and monitor active editor extensions.

### 7.3 System Resource Footprint (`/admin/system-monitor`)
The diagnostic engine measures disk size, registered hooks, custom routes, and calculates an estimated performance impact rating (🟢 Low, 🟡 Medium, 🟠 High).

### 7.4 Dynamic Administration Sidebar Menus (`getActiveAdminMenus`)
In MYADS v4.6.0, `PluginManager::getActiveAdminMenus()` inspects active plugins and caches their declared `admin_menu` and `settings_url` entries. The administration layout renders these links automatically:
- Menu declarations are validated against the current administrator's permission level.
- Avoids manual edits to core admin layout files.
- Active menus are cached under `active_admin_menus` and invalidated automatically on plugin activation, deactivation, or update.

---

## 8. Paid Plugin Licensing & Marketplace Protocol

For commercial or premium plugins sold via the **ADStn Marketplace** (`www.adstn.ovh`):

### 8.1 Verification API (`/api/license/verify`)
When activated, the plugin prompts the administrator for their license key and sends a verification request:

```json
POST https://www.adstn.ovh/api/license/verify
{
    "license_key": "ADSTN-XXXX-XXXX-XXXX",
    "domain": "clientdomain.com",
    "plugin": "auto-commenter"
}
```

### 8.2 Local Cryptographic Verification
Store the verified license locally and compute an SHA-256 signature to prevent tampering:

```php
$signature = hash('sha256', $licenseKey . request()->getHost() . config('app.key'));
```

Protect routes using a dedicated middleware comparing the stored signature via constant-time `hash_equals()`.

### 8.3 Secure Marketplace Updates (`ADStn_url`)
When `ADStn_url` is declared in `plugin.json`, update requests query the central marketplace using `http_secure()` with full SSL validation:

```json
POST https://www.adstn.ovh/api/marketplace/extensions/plugins
{
    "slug": "auto-commenter",
    "version": "1.0.0",
    "license_key": "ADSTN-XXXX-XXXX-XXXX",
    "domain": "clientdomain.com"
}
```

---

## 9. Plugin Localization (i18n)

Plugins must provide multi-language support matching MYADS supported locales (e.g. Arabic `ar`, English `en`, French `fr`).

### Directory Layout
```text
plugins/AutoCommenter/
└── lang/
    ├── ar/
    │   └── messages.php
    └── en/
        └── messages.php
```

### Namespace Registration
In your plugin's main initialization file:

```php
\Illuminate\Support\Facades\Lang::addNamespace('auto-commenter', __DIR__ . '/lang');
```

### Usage in Code & Blade
- **Blade Views:** `{{ __('auto-commenter::messages.widget_title') }}`
- **PHP Controllers:** `__('auto-commenter::messages.success_notice')`

---

## 10. Secure File & Media Handling in Plugins

Plugins that process user-uploaded media or files must adhere to the platform's multi-layered security protocol:

### 10.1 Binary MIME Inspection (`FileUploadSecurityService`)
Never rely solely on client-provided file extensions or HTTP `Content-Type` headers. Use `FileUploadSecurityService`:

```php
use App\Services\FileUploadSecurityService;

$security = app(FileUploadSecurityService::class);
$result = $security->validateUpload($request->file('attachment'), [
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'webp', 'gif'],
    'max_size_kb' => 5120, // 5 MB
]);

if (!$result['valid']) {
    return response()->json(['error' => $result['error']], 422);
}
```

### 10.2 Automated WebP Conversion
To minimize bandwidth and disk consumption, convert raster uploads to WebP with an 85% quality factor:

```php
// Check GD support for WebP
if (function_exists('imagewebp')) {
    $image = imagecreatefromstring(file_get_contents($uploadedFile->getRealPath()));
    if ($image !== false) {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        imagewebp($image, $destinationPath, 85);
        imagedestroy($image);
    }
}
```
