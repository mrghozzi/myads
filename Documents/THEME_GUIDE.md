# Modern Theme Architecture Guide — MYADS v4.7.0

MYADS features an advanced, efficient, and developer-friendly **Parent/Child Theme Architecture** built on **Laravel 12 (PHP 8.2+)** and the **Blade** templating engine. The system completely decouples backend business logic from presentation, providing enterprise-grade security, parent/child inheritance, CLI scaffolding, live responsive preview, and deep integration with the `@.superdesign` glassmorphic design system and **Live Theme Customizer**.

---

## 1. Core Architecture & Highlights

1. **Parent/Child Theme Inheritance:**
   - Child themes do **not** need to duplicate the 100+ views of the core platform.
   - By specifying `"parent": "default"` (or another parent theme) in `theme.json`, a child theme inherits all views and assets, only overriding the templates or stylesheets it customizes.
   - **View Resolution Hierarchy:** Active Child Theme (`themes/{child}/views`) $\rightarrow$ Parent Theme (`themes/{parent}/views`) $\rightarrow$ Core Fallback (`themes/default/views`).
   - Plugin view overrides prepended via `View::prependNamespace('theme', ...)` preserve highest precedence.
2. **Multi-Tier Asset Resolution (`theme_asset`):**
   - Automatically resolves assets across child theme $\rightarrow$ parent theme $\rightarrow$ core default assets.
3. **Instant CLI & UI Scaffolding:**
   - Scaffold starter themes via Artisan: `php artisan theme:create <slug> --parent=default`.
   - Create new child themes directly from the Admin Hub via the `#createThemeModal`.
4. **Maximum Security & Integrity Verification:**
   - **ZIP Archive Hardening:** Path traversal protection (`..`), forbidden executable filtering (`.exe`, `.sh`, `.bat`, etc.), and strict manifest validation.
   - **Pre-Activation Syntax Parsing:** Parses theme PHP scripts using `token_get_all(TOKEN_PARSE)` prior to activation to prevent fatal syntax errors from breaking the site.
   - **Safe-Mode Fallback:** If the active theme directory is corrupted or missing, the platform automatically falls back to `default`.
   - **Deletion Safeguards:** Prevents deleting the `default` theme, currently active themes, or parent themes that child themes depend upon.
5. **Live Responsive Theme Preview:**
   - Inspect any installed theme without activating it using `/admin/themes` with responsive viewport toggles (**Desktop**, **Tablet 768px**, **Mobile 375px**), or via the secure `?preview_theme=<slug>` query parameter for administrators.

---

## 2. Directory Layout & Structure

All themes reside in the root `/themes/` directory:

```text
themes/
├── default/               # Core Baseline Parent Theme
│   ├── theme.json         # Required manifest
│   ├── screenshot.png     # Visual preview image
│   ├── assets/            # Theme CSS, JS, fonts, and images
│   └── views/             # Full core Blade template catalog
│
└── my-dark-child/         # Lightweight Child Theme (Only customized files!)
    ├── theme.json         # Declares "parent": "default"
    ├── screenshot.png     # Custom preview card
    ├── assets/
    │   ├── css/
    │   │   └── theme.css  # Custom CSS rules & variable overrides
    │   └── js/
    │       └── theme.js   # Custom JavaScript enhancements
    └── views/
        ├── layouts/
        │   └── master.blade.php  # Optional customized master wrapper
        └── partials/
            └── header.blade.php  # Optional customized navigation bar
```

---

## 3. The `theme.json` Manifest Specification

Every theme must include a `theme.json` in its root folder:

```json
{
    "name": "Dark Space Theme",
    "slug": "dark-space",
    "version": "1.0.0",
    "parent": "default",
    "author": "Creative Web Studio",
    "author_url": "https://github.com/developer",
    "description": "A sleek dark child theme inheriting from default with glassmorphic cards and glowing violet accents.",
    "thumbnail": "screenshot.png",
    "siteweb": "https://example.com/dark-space",
    "min_myads": "4.7.0",
    "max_myads": "4.7.x",
    "ADStn_url": "dark-space-theme",
    "latest": "https://github.com/developer/dark-space-theme/releases/latest"
}
```

### Manifest Fields

| Field | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `name` | string | **Yes** | Display name of the theme. |
| `slug` | string | **Yes** | Unique identifier (lowercase alphanumeric, dashes, underscores). |
| `version` | string | **Yes** | Semantic version string (e.g. `1.0.0`). |
| `parent` | string | No | Parent theme slug to inherit from (default: `null` or `"default"`). |
| `author` | string | No | Creator name or organization. |
| `author_url` | string | No | Website or profile link of author. |
| `description` | string | **Yes** | Summary of the theme aesthetic and features. |
| `thumbnail` | string | No | Relative path to screenshot image (e.g. `screenshot.png`). |
| `min_myads` | string | No | Minimum supported MYADS version (e.g. `4.7.0`). |
| `max_myads` | string | No | Maximum supported MYADS version boundary. |
| `latest` | string | No | GitHub releases URL for free automated updates. |
| `ADStn_url` | string | No | Marketplace slug for premium licensing via ADStn. |

---

## 4. Parent/Child Theme Inheritance in Practice

### 4.1 How It Works
When a child theme declares `"parent": "default"`:
1. When rendering `view('theme::home')`, Laravel's view finder first searches `themes/dark-space/views/home.blade.php`.
2. If the child theme has not created that file, Laravel automatically falls back to `themes/default/views/home.blade.php`.
3. If an active plugin overrides `theme::home`, the plugin's template takes precedence over both child and parent.

### 4.2 Asset Fallback (`theme_asset`)
The global `theme_asset($path)` helper automatically resolves static assets:
```blade
{{-- Resolves from themes/dark-space/assets/css/theme.css if it exists;
     otherwise falls back to themes/default/assets/css/theme.css --}}
<link rel="stylesheet" href="{{ theme_asset('css/theme.css') }}">
```

---

## 5. Theme Scaffolding

### 5.1 Artisan CLI Command
To scaffold a new child theme with all standard folders and a pre-configured `theme.json`:

```bash
php artisan theme:create modern-blue --name="Modern Blue" --parent=default --author="Alex"
```

This automatically generates:
- `themes/modern-blue/theme.json`
- `themes/modern-blue/views/`
- `themes/modern-blue/assets/css/theme.css`
- `themes/modern-blue/assets/js/theme.js`
- `public/themes/modern-blue/assets/`

### 5.2 Admin Web UI Scaffolding
Administrators can also create themes directly from the browser:
1. Go to **Admin Panel $\rightarrow$ Themes** (`/admin/themes`).
2. Click **Create Starter Theme** in the top hero header.
3. Fill in the Theme Name, Slug, select a Parent Theme from the dropdown, and submit.
4. The theme is immediately available in the grid for customization and activation.

---

## 6. Live Responsive Preview Mode

Administrators can test how their site looks under any installed theme before switching:
- **In Admin Panel:** Click the **Preview** button (`feather-eye`) on any theme card. A fullscreen modal opens featuring device viewport switches:
  - **Desktop** (100% width)
  - **Tablet** (768px width)
  - **Mobile** (375px width)
  - **Open in New Tab** (`target="_blank"`)
- **Direct Link:** Visiting `https://your-site.com/?preview_theme=dark-space` as an administrator will render the site using the requested theme for that request without altering the database setting.

---

## 7. Security & Archive Validation Standards

When installing themes via ZIP upload (`POST /admin/themes/upload`), the system performs rigorous security checks:
1. **Directory Traversal Defense:** Every file path in the ZIP is inspected for `../` or absolute drive paths.
2. **Executable Extension Blacklist:** Disallowed extensions (`.exe`, `.bat`, `.sh`, `.cmd`, `.pif`, `.scr`, `.msi`, `.dll`) are strictly blocked.
3. **Manifest Inspection:** The ZIP must contain a valid `theme.json` with a valid slug and required fields.
4. **Syntax Validation:** Scans all `.php` files inside the theme using `token_get_all(TOKEN_PARSE)` to catch parse errors before activation.

---

## 8. Lifecycle Hooks

Themes can listen for and respond to system events via the MYADS `Hooks` subsystem:

| Action Hook | Arguments | Description |
| :--- | :--- | :--- |
| `theme_installed` | `string $slug, array $manifest` | Fires immediately when a theme ZIP is uploaded and verified. |
| `theme_activated` | `string $slug, array $manifest` | Fires when an administrator switches the active theme. |
| `theme_deleted` | `string $slug` | Fires after a theme is permanently removed. |
| `theme_created` | `string $slug, array $manifest` | Fires when a new starter theme is scaffolded via CLI or UI. |

---

## 9. Live Theme Customizer Integration

Every theme works automatically with the **Live Theme Customizer** (`/admin/themes/customizer`):
- Controls for brand colors, surfaces, typography (`Inter`, `Cairo`, `Tajawal`, etc.), border radii, and glassmorphic blurs.
- Automatically compiles dynamic variables to `public/themes/{theme}/custom_variables.css`.
- Master layouts include this stylesheet dynamically:
  ```blade
  @if(file_exists(public_path('themes/' . active_theme() . '/custom_variables.css')))
      <link rel="stylesheet" href="{{ theme_asset('custom_variables.css') }}">
  @endif
  ```
