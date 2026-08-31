# Theme Guide

MYADS features a modular, responsive Theme Architecture built on **Laravel 12 (PHP 8.2+)** and the **Blade** templating engine. The system cleanly decouples backend business logic from presentation, providing built-in dark mode support, the `@.superdesign` glassmorphic design system, and an interactive **Live Theme Customizer**.

---

## 1. Theme Architecture & File Layout

All frontend themes reside inside the root `/themes/` directory. The primary baseline theme is `/themes/default/`.

```text
themes/
└── default/
    ├── theme.json         # Theme manifest and metadata (required JSON)
    ├── screenshot.png     # Visual preview image (recommended 800x600)
    ├── css/               # Stylesheets and custom variables
    │   ├── custom_variables.css # Compiled dynamically by Theme Customizer
    │   └── styles.css
    ├── js/                # Client-side JavaScript scripts
    ├── img/               # Theme icons, logos, and illustration assets
    ├── lang/              # Optional: Theme-specific translation overrides
    │   ├── ar/messages.php
    │   └── en/messages.php
    ├── views/             # Blade Templates (mapped to the 'theme::' namespace)
    │   ├── layouts/       # Master layouts (master.blade.php, portal.blade.php)
    │   ├── partials/      # Reusable UI components (header, footer, widgets, modals)
    │   ├── home.blade.php # Main community timeline feed
    │   ├── profile/       # Member profile, videos, clips, and social views
    │   ├── video/         # Video Hub and YouTube-style watch pages
    │   ├── clips/         # Vertical Shorts/Clips feed
    │   ├── forum/         # Community forum and topic discussion views
    │   ├── store/         # Product marketplace and knowledgebase views
    │   ├── directory/     # Web directory listings
    │   └── developer/     # Developer platform & OAuth application views
    ├── README.md          # Optional: Documentation displayed in admin modal
    ├── changelogs.md      # Optional: Version history displayed in admin modal
    └── screenshots.md     # Optional: Markdown screenshots for admin preview
```

---

## 2. The `theme.json` Manifest

Every theme **must** include a `theme.json` file in its root directory. This manifest provides metadata for identification, versioning, and marketplace auto-updates.

```json
{
    "name": "Default Theme",
    "slug": "default",
    "version": "1.2.0",
    "author": "MyAds Core",
    "author_url": "https://github.com/mrghozzi",
    "description": "The official modern starter theme for MYADS featuring the @.superdesign design system.",
    "thumbnail": "screenshot.png",
    "latest": "https://github.com/mrghozzi/myads-theme-default/releases/latest",
    "min_myads": "4.5.0",
    "ADStn_url": "myads-theme-default",
    "siteweb": "https://www.example.com"
}
```

### Metadata Properties

| Property | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `name` | string | **Yes** | Display name of the theme. |
| `slug` | string | **Yes** | Unique identifier (must match the folder name). |
| `version` | string | **Yes** | Current semantic version string (e.g. `1.2.0`). |
| `author` | string | **Yes** | Author or organization name. |
| `author_url` | string | No | Link to author website or GitHub profile. |
| `description` | string | **Yes** | Short description of theme features and aesthetics. |
| `thumbnail` | string | No | Preview image file in theme root (e.g. `screenshot.png`). |
| `min_myads` | string | No | Minimum compatible MYADS release (e.g. `4.5.0`). |
| `ADStn_url` | string | No | Marketplace slug for checking premium updates on ADStn. |
| `latest` | string | No | GitHub release URL for free automatic updates. |
| `siteweb` | string | No | Official theme landing page or demo URL. |

---

## 3. Live Theme Customizer (`THEME-07`)

Starting in MYADS v4.5.3, site administrators can visually customize the active theme directly from the Admin Panel via `/admin/themes/customizer`, powered by `ThemeCustomizerService`.

### 3.1 Customizer Features
- **Color Palette Controls:** Customize primary brand colors, accent highlights, top navigation headers, card background surfaces, and text colors.
- **Typography Engine:** Select from curated Google Fonts and Arabic typography stacks (`Inter`, `Cairo`, `Tajawal`, `Roboto`, `Outfit`, or native `System UI`).
- **Surface & Shape Customization:** Adjust component border radiuses (`4px` to `24px`) and glassmorphic blur / opacity layers.
- **Real-Time Responsive Split-Screen Preview:** Instant live preview across **Desktop**, **Tablet**, and **Mobile** viewports via `postMessage` CSS variable injection without page reloads.
- **Dynamic CSS Compilation:** On save, settings are compiled into `public/themes/{theme}/custom_variables.css` and persisted in the `options` table.

### 3.2 Master Layout Integration
Master templates automatically link the custom stylesheet if present:

```blade
@if(file_exists(public_path('themes/' . active_theme() . '/custom_variables.css')))
    <link rel="stylesheet" href="{{ theme_asset('css/custom_variables.css') }}">
@endif
```

---

## 4. Design Standards: `@.superdesign` & Dark Mode

Modern MYADS themes follow the `@.superdesign` standard for a sleek, premium experience:

### 4.1 Dark Mode Attribute
Dark mode is controlled at the root `<html>` or `<body>` element:
```html
<html data-theme="css_d" dir="{{ is_locale_rtl() ? 'rtl' : 'ltr' }}">
```
Themes should style dark mode variables using the CSS selector:
```css
[data-theme="css_d"] {
    --bg-surface: #0f172a;
    --text-primary: #f8fafc;
    --border-color: rgba(255, 255, 255, 0.08);
}
```

### 4.2 Core UI Elements
- **Glassmorphism:** Use subtle frosted backgrounds: `background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(12px);`.
- **RTL & LTR Support:** Use bidirectional flex/grid layouts and logical margins (`margin-inline-start`, `padding-inline-end`).

---

## 5. Blade Namespacing & Helpers

### 5.1 The `theme::` View Namespace
MYADS registers the active theme folder as the `theme::` view namespace. You can include partials and extend layouts seamlessly:

```blade
@extends('theme::layouts.master')

@section('content')
    <div class="container my-4">
        @include('theme::partials.header.nav')
        @include('theme::partials.alerts')
        
        <x-widget-column place="home_sidebar" />
    </div>
@endsection
```

### 5.2 The `theme_asset()` Helper
Link to theme stylesheets, scripts, and images using the global `theme_asset()` helper:

```blade
<!-- Resolves to: https://domain.com/themes/default/css/styles.css -->
<link rel="stylesheet" href="{{ theme_asset('css/styles.css') }}">

<!-- Resolves to: https://domain.com/themes/default/js/main.js -->
<script src="{{ theme_asset('js/main.js') }}" defer></script>
```

---

## 6. Creating a New Theme

Follow these steps to create a custom theme:

1. **Duplicate** the default theme folder:
   ```bash
   cp -r themes/default themes/dark_ocean
   ```
2. **Update** `themes/dark_ocean/theme.json` with your custom `name`, `slug: "dark_ocean"`, and metadata.
3. **Customize** stylesheets in `css/`, templates in `views/`, and assets in `img/`.
4. **Activate** the theme in the Admin Panel:
   - Navigate to **Admin Panel -> Settings -> Theme Manager**.
   - Locate `dark_ocean` in the installed themes grid.
   - Click **Activate**.
5. **Customize visually** via **Admin Panel -> Settings -> Theme Customizer**.

---

## 7. Paid Theme Licensing & Auto-Updates

Premium themes distributed through the **ADStn Marketplace** (`www.adstn.ovh`) utilize unified license validation:

1. **Manifest Configuration:** Set `ADStn_url` in `theme.json`.
2. **License Storage:** Administrator activates the license key in `/admin/themes`, stored in `options` with `o_type = 'theme_license'`.
3. **Automated Checks:** `ThemeManager` securely queries `https://www.adstn.ovh/api/marketplace/extensions/themes` sending `slug`, `version`, `license_key`, and `domain`.
4. **Authorized Updates:** If a new release is available, the admin panel enables 1-click update download and extraction.
