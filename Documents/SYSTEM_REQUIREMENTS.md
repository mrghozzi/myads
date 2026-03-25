# System Requirements

To run MYADS v4.2.0 efficiently, your server must meet the following hardware and software requirements.

## Server Environment

Since MYADS v4.0 is built on **Laravel**, the requirements follow standard modern PHP applications.

### Minimum Software Requirements
- **PHP:** `8.2.0` or higher
- **Web Server:** Apache (with `mod_rewrite` enabled), Nginx, or LiteSpeed.
- **Database:** MySQL `5.7+` or MariaDB `10.3+`

### Required PHP Extensions
The following PHP extensions must be enabled in your `php.ini`:
- `Ctype` PHP Extension
- `cURL` PHP Extension
- `DOM` PHP Extension
- `Fileinfo` PHP Extension
- `Filter` PHP Extension
- `Hash` PHP Extension
- `Mbstring` PHP Extension
- `OpenSSL` PHP Extension
- `PCRE` PHP Extension
- `PDO` PHP Extension
- `Session` PHP Extension
- `Tokenizer` PHP Extension
- `XML` PHP Extension

### Optional (But Highly Recommended) PHP Extensions
The installer will display a warning if these are missing, but will allow the installation to proceed:
- `GD` PHP Extension (Required for image resizing and cropping).
- `ZIP` PHP Extension (Required if you wish to upload/install Themes and Plugins directly from the Admin Panel via ZIP).

## Hardware Recommendations

For a small to medium-sized community:
- **CPU:** 1-2 vCores
- **RAM:** 1GB - 2GB minimum (2GB+ Recommended for smooth Database operations).
- **Storage:** Minimum 5GB SSD (Disk space heavily depends on user image uploads and store attachments).

## Folder Permissions
The web server (e.g., `www-data` or `apache`) must have **write access** to the following directories:
- `/storage`
- `/storage/app`
- `/storage/framework`
- `/storage/logs`
- `/bootstrap/cache`
- `/upload` (For user avatars, banners, and store files)
