# System Requirements

To run **MYADS** efficiently, your server environment must meet the following hardware, database, PHP, and web server specifications.

---

## 1. Core Software Environment

MYADS v4.5.5 is engineered on **Laravel 12** and requires a modern PHP runtime environment.

### 1.1 PHP Version
- **PHP 8.2.0 or higher** (Fully compatible with PHP 8.2, 8.3, and 8.4).

### 1.2 Web Server
- **Apache** (with `mod_rewrite` and `mod_headers` enabled).
- **Nginx** (with proper URL rewriting and SSE streaming configuration).
- **LiteSpeed / OpenLiteSpeed**.
- **Caddy Server**.

### 1.3 Database Management System
- **MySQL 5.7+ / 8.0+** OR **MariaDB 10.3+**.
- **Database Engine:** `InnoDB` with `ROW_FORMAT=DYNAMIC`.
- **Character Set & Collation:** `utf8mb4` with `utf8mb4_unicode_ci` (required for emoji support and high-traffic compound indexes).

---

## 2. Required PHP Extensions

The following standard PHP extensions must be enabled in your `php.ini`:

| Extension | Purpose |
| :--- | :--- |
| `BCMath` | Precision arithmetic for billing, subscriptions, and point transactions. |
| `Ctype` | Character type checking. |
| `cURL` | External HTTP requests, marketplace updates, and OAuth communication. |
| `DOM` / `XML` | XML parsing and RSS feeds. |
| `Fileinfo` | MIME-type detection for secure user uploads. |
| `Filter` | Safe data sanitization and email/URL filtering. |
| `Hash` / `OpenSSL` | Secure cryptographic operations, session encryption, and password hashing. |
| `Intl` | Internationalization, number formatting, and locale handling. |
| `JSON` | Handling API payloads, developer scopes, and theme manifests. |
| `Mbstring` | Multibyte string processing for Arabic and multilingual content. |
| `PCRE` | Regular expressions. |
| `PDO` & `pdo_mysql` | Secure database abstraction and query execution. |
| `Session` | User session state management. |
| `Tokenizer` | Blade template compilation. |

---

## 3. Optional & Recommended Extensions & Binaries

| Component | Status | Purpose |
| :--- | :--- | :--- |
| `GD` / `Imagick` | **Recommended** | Image cropping, avatar manipulation, and banner resizing. |
| `ZIP` | **Recommended** | Direct 1-click theme and plugin installation and updates from `.zip` files. |
| `FFmpeg` Binary | **Optional** | Video duration calculation, automated video thumbnail generation, and transcoding diagnostics in `/admin/settings/ffmpeg`. |
| `OPcache` | **Recommended** | Accelerated PHP bytecode execution for production environments. |
| `Redis` | **Optional** | High-throughput caching and queue drivers for enterprise installations. |

---

## 4. Real-Time Streaming & Reverse Proxy Requirements

### 4.1 Server-Sent Events (SSE) Streaming (`/live/stream`)
MYADS features a real-time event streaming engine. For uninterrupted SSE delivery:
- **Nginx:** Disable proxy buffering on the live endpoint:
  ```nginx
  location /live/stream {
      proxy_pass http://127.0.0.1:8000;
      proxy_set_header Connection '';
      proxy_http_version 1.1;
      chunked_transfer_encoding off;
      proxy_buffering off;
      proxy_cache off;
  }
  ```
- **Apache:** Ensure `mod_proxy` disables output buffering for streaming routes (`X-Accel-Buffering: no`).

### 4.2 Cloudflare & Reverse Proxy SSL Trust
MYADS includes `$middleware->trustProxies(at: '*')` to seamlessly trust incoming `X-Forwarded-Proto: https` and `CF-Visitor` headers, eliminating redirect loops and method conversion on `POST` requests.

---

## 5. Hardware Specifications

### Standard Community (Up to 10,000 active members):
- **CPU:** 1–2 vCPUs
- **RAM:** 2 GB minimum (4 GB recommended)
- **Storage:** 10 GB+ SSD / NVMe

### High-Traffic Advertising Network (100,000+ daily impressions):
- **CPU:** 4+ vCPUs
- **RAM:** 8 GB+ RAM
- **Storage:** 50 GB+ NVMe SSD
- **Caching:** Redis / Memcached

---

## 6. Directory Write Permissions

The web server process (`www-data`, `nginx`, or `apache`) must have read and write permissions (`chmod -R 775` or `chmod -R 755`) to:

- `/storage/` (and all subdirectories: `app/`, `framework/`, `logs/`)
- `/bootstrap/cache/`
- `/upload/` (User avatars, banners, attachments, and store media)
- `/public/themes/` (Compiled custom CSS variables from Theme Customizer)
