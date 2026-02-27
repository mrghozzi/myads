# Plugin Guide

MYADS v4.0 introduces a robust, WordPress-like Plugin System. This architecture enables developers to add new features or modify existing behaviors without modifying the core Laravel source code.

## Plugin Structure

Plugins must be placed inside the root `/plugins/` directory.

A typical plugin directory structure looks like this:

```text
plugins/
└── AutoCommenter/
    ├── AutoCommenter.php   # Main plugin controller file
    ├── config.json         # Plugin metadata (JSON format)
    ├── helpers.php         # Custom helper functions for the plugin
    ├── controllers/        # Plugin-specific controllers
    ├── views/              # Blade views specific to the plugin
    ├── css/                # Embedded static resources
    └── install.sql         # Optional: Executed on installation/activation
```

## The `config.json` File

Every plugin MUST have a `config.json` file in its root directory. This tells the core system everything it needs to know to load the plugin.

```json
{
    "name": "Auto Commenter Pro",
    "description": "Automatically comment on new topics.",
    "version": "1.0.0",
    "author": "mrghozzi",
    "main": "AutoCommenter.php",
    "active": false
}
```

## The Main Plugin File

The main file defined in the `config.json` `main` property (e.g., `AutoCommenter.php`) acts as the plugin's entry point.
It is required by the core application on boot if the plugin is activated by the administrator.

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

- **Installation:** The Administrator can visit the Plugins page (`/admin/plugins`), click "Upload Plugin", and upload a `.zip` archive containing the plugin structure. The system automatically extracts, validates the `config.json`, and installs it.
- **Activation:** The admin can toggle the "Active" status from the Dashboard. Only `active: true` plugins will be loaded by the Service Provider.
