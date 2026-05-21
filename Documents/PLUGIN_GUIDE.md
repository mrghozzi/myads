# Plugin Guide

MYADS v4.0 introduces a robust, WordPress-like Plugin System. This architecture enables developers to add new features or modify existing behaviors without modifying the core Laravel source code.

## Plugin Structure

Plugins must be placed inside the root `/plugins/` directory.

A typical plugin directory structure looks like this:

```text
plugins/
└── AutoCommenter/
    ├── AutoCommenter.php   # Main plugin controller file
    ├── plugin.json         # Plugin metadata (JSON format)
    ├── helpers.php         # Custom helper functions for the plugin
    ├── controllers/        # Plugin-specific controllers
    ├── views/              # Blade views specific to the plugin
    ├── css/                # Embedded static resources
    ├── thumbnail.png       # Optional: Plugin thumbnail image
    ├── install.sql         # Optional: Executed on installation/activation
    ├── README.md           # Optional: Core documentation (rendered in admin)
    ├── changelogs.md       # Optional: Release history (rendered in admin)
    └── screenshots.md      # Optional: Visual previews (rendered in admin)
```

## The `config.json` File

Every plugin MUST have a `plugin.json` file in its root directory. This tells the core system everything it needs to know to load the plugin.

```json
{
    "name": "Auto Commenter Pro",
    "slug": "auto-commenter",
    "description": "Automatically comment on new topics.",
    "version": "1.0.0",
    "author": "mrghozzi",
    "author_url": "https://github.com/mrghozzi",
    "thumbnail": "thumbnail.png",
    "siteweb": "https://www.example.com",
    "ADStn_url": "Monetization_Pro",
    "latest": "https://github.com/mrghozzi/auto-commenter/releases/latest",
    "min_myads": "4.2.5"
}
```

### Metadata Properties:
- `name`: Display name of the plugin.
- `slug`: Unique identifier (should match the folder name).
- `version`: Current version string.
- `author`: Author name.
- `author_url`: (Optional) URL to the author's profile.
- `thumbnail`: (Optional) Filename of the thumbnail image in the plugin root.
- `siteweb`: (Optional) Official website for the plugin.
- `ADStn_url`: (Optional) The product slug on the ADStn marketplace. If set, the update engine queries the central ADStn marketplace endpoint (`https://www.adstn.ovh/api/marketplace/extensions/plugins`) to check for updates and verify licensing dynamically using this slug.
- `latest`: (Optional) GitHub release URL for automatic update checks and changelog display (ignored if `ADStn_url` is defined).
- `min_myads`: (Optional) Minimum compatible MyAds version (e.g., "4.2.0").

The main file should be named after the plugin or contain the logic needed. It is required by the core application on boot if the plugin is activated by the administrator.

## Hooks and Filters

MYADS v4.0 uses a Hook/Event system inspired by WordPress to allow plugins to interact with the core logic.

### 1. `add_filter('hook_name', function)`
Use filters to modify variables before the core system renders or processes them.

```php
add_filter('post_content_render', function($content) {
    return str_replace(':smile:', '😄', $content);
});
```

### 2. `add_action('hook_name', function)`
Use actions to inject code, trigger API calls, or echo HTML at specific points in the application lifecycle.

```php
add_action('after_user_register', function($userId) {
    // Send a welcome email via an external API.
    MyAPI::sendWelcome($userId);
});
```

## Admin Management

- **Installation:** The Administrator can visit the Plugins page (`/admin/plugins`), click "Upload Plugin", and upload a `.zip` archive containing the plugin structure. The system automatically extracts, validates the `plugin.json`, and installs it.
- **Activation:** The admin can toggle the "Active" status from the Dashboard. Only activated plugins will be loaded by the Service Provider.
- **Deactivation:** Active plugins MUST be deactivated before they can be deleted to ensure system stability.
- **Updates:** If a `latest` link is provided in `plugin.json`, the system will use the GitHub API to check for new versions and display the changelog in a modal.

## Paid Plugin Licensing Protocol

Starting with MYADS v4.3.4, a standard licensing and activation framework is supported for paid plugins. This ensures that third-party purchases can be verified and bound to the client's host domain.

### 1. Centralized License Table & API Verification
The central store site (`www.adstn.ovh`) records the generated license key when a product is purchased or downloaded:
- Database table: `product_licenses`
- Verification API route: `/api/license/verify`
  - Method: `POST`
  - Parameters:
    - `license_key`: The key provided to the customer (e.g. `ADSTN-XXXX-XXXX-XXXX`).
    - `domain`: The host domain of the client site (e.g. `clientdomain.com`).
    - `plugin`: The slug of the product.
  - Return:
    ```json
    {
      "success": true,
      "message": "License successfully verified and activated."
    }
    ```

### 2. Client Middleware Protection
Every paid plugin must define and register a Route Middleware (e.g. `PluginNameLicenseGuard`) to intercept and restrict access to its routes unless a valid license is saved locally.

- **Signature Check**: To avoid calling the remote server on every request, cache the license locally in the `options` table with a secure host signature:
  ```php
  $signature = hash('sha256', $licenseKey . request()->getHost() . 'myads_secret_salt');
  ```
- **Validation Logic**: Inside the middleware, compare the local signature with the expected one. If invalid:
  - If the user is an administrator, redirect to the license activation screen (`/admin/plugin-slug/license`).
  - Otherwise, return a `403 Forbidden` response.

### 3. Activation Screen
Provide a dedicated activation page in the admin panel sub-menu allowing the administrator to input their license key and send a POST request to the store's verification API.

### 4. Paid Plugin Secure Auto-Updates
When a paid plugin defines the `ADStn_url` metadata property in its `plugin.json` manifest, the platform's update engine will bypass public GitHub repository checks and interact with the configured marketplace endpoint.

- **Update Check Request**: The client sends a `POST` request to `ADStn_url` containing the following payload:
  ```json
  {
      "slug": "plugin-slug",
      "version": "1.0.0",
      "license_key": "ADSTN-XXXX-XXXX-XXXX",
      "domain": "clientdomain.com"
  }
  ```
- **Update Check Response**: The marketplace endpoint validates the key and domain, and returns:
  ```json
  {
      "success": true,
      "version": "1.1.0",
      "download_url": "https://www.adstn.ovh/api/marketplace/extensions/download?slug=plugin-slug",
      "changelog": "List of changes and features..."
  }
  ```
- **Secure Download**: During the plugin upgrade process, the platform automatically signs and appends the stored `license_key` and matching `domain` query parameters to the `download_url` to authorize and download the zip package.

---

## Plugin Localization (i18n)

MYADS plugins must support multi-language routing and rendering using Laravel's localization system. By default, MYADS loads translation resources dynamically for each active plugin.

### 1. File Structure
Translation files should be placed in `lang/{locale}/messages.php` inside the plugin directory:
```text
plugins/PluginName/
└── lang/
    ├── ar/
    │   └── messages.php
    └── en/
        └── messages.php
```

### 2. Loading the Namespace
In the plugin's main entry point (e.g., `boot.php` or `PluginName.php`), register the translation namespace:
```php
\Illuminate\Support\Facades\Lang::addNamespace('plugin-slug', __DIR__ . '/lang');
```

### 3. Usage in Views and Controllers
Access the localized strings using the double-colon namespace syntax `__('plugin-slug::messages.key')`:
- **Blade Views**: `{{ __('plugin-slug::messages.welcome_title') }}`
- **PHP/Controllers**: `__('plugin-slug::messages.error_unauthorized')`

### 4. Keep Keys in Sync
To maintain platform consistency, translation files for all active languages (especially Arabic `ar` and English `en`) must contain identical keys to prevent UI breakages when users toggle language modes.

