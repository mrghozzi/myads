# Theme Guide

MYADS v4.0 introduces a modular theme system, cleanly separating the frontend user interface from the backend logic using Laravel's **Blade** templating engine.

## Theme Architecture

Themes are located in the `/themes/` directory at the root of your application.
The default theme provided is `/themes/default/`.

A standard theme directory looks like this:

```text
themes/
└── default/
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
```

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
