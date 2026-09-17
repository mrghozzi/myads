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
    "version": "2.2.0",
    "author": "MyAds Core",
    "author_url": "https://github.com/mrghozzi",
    "description": "The official modern starter theme for MYADS featuring Design Tokens, Glassmorphism 2.0, Mobile Navigation Hub, and Universal Drag-and-Drop Media.",
    "thumbnail": "screenshot.png",
    "latest": "https://github.com/mrghozzi/myads-theme-default/releases/latest",
    "min_myads": "4.6.0",
    "max_myads": "4.6.x",
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
| `siteweb` | string | No | Official theme landing page or demo URL. |
| `min_myads` | string | No | Minimum compatible MYADS release (e.g. `4.6.0`). |
| `max_myads` | string | No | Maximum compatible MYADS version bounding (e.g. `4.6.x`). |
| `ADStn_url` | string | No | Marketplace slug for checking premium updates on ADStn. |
| `latest` | string | No | GitHub release URL for free automatic updates. |

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

## 4. Design Standards: `@.superdesign`, Glassmorphism 2.0 & Tokens

Modern MYADS themes follow the `@.superdesign` standard and centralized CSS Design Tokens for an ultra-fast, premium experience:

### 4.1 Design Tokens System (`theme-tokens.css` & `css_d/theme-tokens.css`)
Themes define semantic color variables, elevation depths, border radii, and transitions:
```css
:root {
    --myads-primary: #23d2e2;
    --myads-primary-rgb: 35, 210, 226;
    --myads-primary-glow: rgba(35, 210, 226, 0.35);
    --myads-glass-bg: rgba(255, 255, 255, 0.78);
    --myads-glass-border: rgba(255, 255, 255, 0.65);
    --myads-glass-blur: 16px;
    --myads-radius-md: 14px;
    --myads-radius-pill: 9999px;
}
```

### 4.2 Dark Mode Attribute
Dark mode is controlled at the root `<html>` or `<body>` element:
```html
<html data-theme="css_d" dir="{{ is_locale_rtl() ? 'rtl' : 'ltr' }}">
```
Dark mode overrides are packaged inside `assets/css_d/theme-tokens.css`:
```css
[data-theme="css_d"] {
    --myads-glass-bg: rgba(15, 23, 42, 0.82);
    --myads-glass-border: rgba(255, 255, 255, 0.12);
    --myads-surface: #1e293b;
    --myads-text-main: #f1f5f9;
}
```

### 4.3 Glassmorphism 2.0 & Spring Reaction Pickers
- **Glassmorphism 2.0 (`.reaction-options.reaction-options-dropdown`):** Multi-layered backdrop blur, frosted translucent gradients, and ambient light borders.
- **Spring Reaction Physics:**
  ```css
  .reaction-options-dropdown .reaction-option {
      transition: transform 0.28s cubic-bezier(0.175, 0.885, 0.32, 1.275), filter 0.2s ease !important;
  }
  .reaction-options-dropdown .reaction-option:hover {
      transform: scale(1.35) translateY(-5px) !important;
  }
  ```
- **Comment Markdown Image Rendering:**
  ```css
  .post-comment-text img,
  .forum-rdx-comment-body img {
      max-width: 100% !important;
      max-height: 380px !important;
      height: auto !important;
      border-radius: 8px !important;
      object-fit: contain !important;
  }
  ```

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

## 6. Ergonomic Mobile Bottom Navigation Hub (`THEME-08`)

MYADS v4.6.0 introduces a standardized fixed mobile bottom navigation component (`views/partials/mobile_bottom_nav.blade.php`):

### 6.1 Viewport Activation & Behavior
- **Viewport:** Displayed strictly on mobile and small tablet viewports (`@media (max-width: 767.98px)`).
- **Layout Accommodation:** To prevent bottom content from being obscured, `body` automatically applies `padding-bottom: 70px !important;` under the mobile breakpoint.
- **Elevation:** High `z-index: 9999` with glassmorphic backdrop blur and frosted borders.

### 6.2 Key Features
1. **Dynamic Route Indicators:** Highlights active navigation buttons based on current route names (`portal.*`, `news.*`, `directory.*`, `messages.*`, `profile.*`).
2. **Real-Time Unread Badges:** Bound to live SSE counters for incoming private messages and system notifications.
3. **Elevated Quick-Post FAB:** An elevated center Floating Action Button with gradient accents triggering the status composer or opening the quick-publish modal.

---

## 7. Universal Drag-and-Drop & Clipboard Image Paste (`Ctrl+V`)

Themes must provide seamless Drag & Drop and Clipboard Image Paste across three critical areas:

### 7.1 Status Post Composer
- Wrap the textarea inside a dropzone container:
  ```blade
  <div class="composer-refresh__editor composer-dropzone" id="composer-editor-shell">
      <textarea id="composer-text" ...></textarea>
      <div class="composer-dropzone-indicator">
          <i class="fa fa-cloud-upload-alt"></i>
          <span>{{ __('messages.drop_files_here') }}</span>
      </div>
  </div>
  ```
- Listen for `dragenter`, `dragover`, `dragleave`, `drop`, and `paste` events. Pasted images from the clipboard (`e.clipboardData.items`) are converted to `File` objects and appended into the gallery input without page reloads.

### 7.2 Private Messages Composer
- Add the drop indicator inside `<footer class="messages-composer" data-message-composer>`:
  ```blade
  <div class="messages-composer-drop-indicator" aria-hidden="true">
      <i class="fa fa-cloud-arrow-up"></i>
      <span>{{ __('messages.drop_files_here') }}</span>
  </div>
  ```
- Handled in `assets/js/messages-app.js` with client-side file size validation (max 5 MB).

### 7.3 Comments System & Inline Media
- In `views/partials/activity/comments.blade.php`:
  1. Add image button `<button data-comment-media-btn="{{ $id }}"><i class="fa fa-image"></i></button>`.
  2. Add hidden file input `<input type="file" id="comment_media_file_{{ $id }}" accept=".jpg,.jpeg,.png,.gif,.webp,.bmp">`.
  3. Add drop indicator `.forum-rdx-comment-dropzone-indicator`.
  4. Add preview card `.forum-rdx-comment-media-preview` displaying the thumbnail, file name, file size, and remove button.
  5. In `views/layouts/master.blade.php`, `postComment(id, type)` transmits a `FormData` object with `formData.append('attachment', mediaFile)` to `POST /comment/store`.

---

## 8. Creating a New Theme

Follow these steps to create a custom theme:

1. **Duplicate** the default theme folder:
   ```bash
   cp -r themes/default themes/dark_ocean
   ```
2. **Update** `themes/dark_ocean/theme.json` with your custom `name`, `slug: "dark_ocean"`, `"min_myads": "4.6.0"`, and metadata.
3. **Customize** stylesheets in `css/`, templates in `views/`, and assets in `img/`.
4. **Activate** the theme in the Admin Panel:
   - Navigate to **Admin Panel -> Settings -> Theme Manager**.
   - Locate `dark_ocean` in the installed themes grid.
   - Click **Activate**.
5. **Customize visually** via **Admin Panel -> Settings -> Theme Customizer**.

---

## 9. Paid Theme Licensing & Auto-Updates

Premium themes distributed through the **ADStn Marketplace** (`www.adstn.ovh`) utilize unified license validation:

1. **Manifest Configuration:** Set `ADStn_url` in `theme.json`.
2. **License Storage:** Administrator activates the license key in `/admin/themes`, stored in `options` with `o_type = 'theme_license'`.
3. **Automated Checks:** `ThemeManager` securely queries `https://www.adstn.ovh/api/marketplace/extensions/themes` sending `slug`, `version`, `license_key`, and `domain`.
4. **Authorized Updates:** If a new release is available, the admin panel enables 1-click update download and extraction.

