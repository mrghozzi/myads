# Theme Guide

MYADS v4.0 introduces a modular theme system, cleanly separating the frontend user interface from the backend logic using Laravel's **Blade** templating engine.

## Theme Architecture

Themes are located in the `/themes/` directory at the root of your application.
The default theme provided is `/themes/default/`.

A standard theme directory looks like this:

```text
themes/
└── default/
    ├── theme.json         # Theme metadata (JSON format)
    ├── screenshot.png     # Theme preview image (recommended)
    ├── css/               # Compiled CSS or raw CSS
    ├── js/                # JS scripts
    ├── img/               # Theme images and icons
    ├── lang/              # Theme-specific translation overrides (optional)
    └── views/             # Blade Templates
        ├── layouts/       # Master layouts (master.blade.php)
        ├── partials/      # Reusable components (header, footer, widgets)
        ├── home.blade.php # Main community feed
        ├── profile/       # User profiles
        ├── forum/         # Forum pages
        └── directory/     # Site directory pages
    └── assets/            # (Optional) Source assets for public distribution
```

## Theme Metadata: `theme.json`

Every theme SHOULD have a `theme.json` file in its root directory for proper identification and versioning.

```json
{
    "name": "Default Theme",
    "slug": "default",
    "version": "1.0.0",
    "author": "MyAds Core",
    "author_url": "https://example.com/author",
    "description": "The default starter theme for MyAds.",
    "thumbnail": "screenshot.png",
    "latest": "https://github.com/mrghozzi/myads-theme-default/releases/latest",
    "min_myads": "4.2.5",
    "ADStn_url": "myads-theme-default",
    "siteweb": "https://example.com/theme-website"
}
```

### Properties:
- `name`: Display name of the theme.
- `slug`: Unique identifier (should match the folder name).
- `version`: Current version string.
- `author`: Author name.
- `author_url`: (Optional) URL to the author's profile.
- `description`: A brief summary of the theme.
- `thumbnail`: (Optional) Filename of the preview image. Fallback: `screenshot.png`.
- `latest`: (Optional) GitHub release URL for automatic update checks and changelog display.
- `min_myads`: (Optional) Minimum compatible MyAds version (e.g., "4.2.0").
- `ADStn_url`: (Optional) Marketplace slug or full API URL for checking premium updates.
- `siteweb`: (Optional) URL to the theme's official website.

## Theme Documentation (Markdown)

Just like plugins, themes support markdown files to provide rich documentation directly in the admin panel Details modal:

- `README.md`: The primary description and documentation.
- `changelogs.md`: A log of version updates and fixes.
- `screenshots.md`: Markdown image embeds showing the theme interface. Example: `![Home Page](./img/screenshot1.png)`

> **Note:** Any relative image links used inside these markdown files will be automatically resolved and served securely via the `admin.themes.asset` route.

## Paid Theme Licensing Protocol

To support premium themes, the MyAds platform implements a unified licensing and updates mechanism connected to the ADStn marketplace. This is identical to the plugin licensing protocol.

1.  **Metadata Tagging:** Premium themes must declare the `ADStn_url` in their `theme.json` to instruct the update manager to query the marketplace API.
2.  **License Key Storage:** When a user enters their license key in the admin settings, it is stored in the `options` table with the following parameters:
    -   `o_type`: `theme_license`
    -   `name`: `{theme_slug}_key` (e.g., `dark_ocean_key`)
    -   `o_valuer`: The license key string.
3.  **Auto-Updates Check:** The `ThemeManager` routinely calls the API endpoint `https://www.adstn.ovh/api/marketplace/extensions/themes`. It securely sends the theme `slug`, current `version`, active `license_key`, and the installation `domain`.
4.  **Authorized Download:** If the license is active and a new version exists, the API returns a temporary, secure `download_url` for a one-click update from the Admin Panel.

## How Templates Work

The platform utilizes Laravel's View namespacing.
When you select "default" as your theme in the admin panel, the system defines the `theme::` namespace pointing to `/themes/default/views/`.

Example of including a partial inside a theme:
```blade
@include('theme::partials.header.nav')
```

This makes creating sub-themes or completely new themes incredibly simple, as you only need to respect the file structure and the `theme::` namespace will automatically route to your active theme folder.

## Creating a New Theme

1. **Duplicate** the `/themes/default/` folder and rename it (e.g., `/themes/dark_ocean/`).
2. **Edit** the CSS/JS inside your new folder to change the aesthetics.
3. **Modify** the Blade templates in `views/` to alter the HTML structure.
4. **Activate** the new theme:
   - Go to your Admin Panel (`/admin`).
   - Navigate to **Settings -> Theme Manager**.
   - Your new theme `dark_ocean` will appear in the list.
   - Click "Activate".

## Useful Theme Assets Helper

To link to a CSS, JS, or image file directly within your active theme folder, use the global helper `theme_asset()` exactly as you would use Laravel's `asset()`.

```blade
<!-- This will resolve to http://domain.com/themes/default/css/styles.css -->
<link rel="stylesheet" href="{{ theme_asset('css/styles.css') }}">
```
