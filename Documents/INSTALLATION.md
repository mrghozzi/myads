# Installation & Setup Guide

This guide provides instructions for installing and configuring **MYADS** on your server using either the graphical Web Installer or the Command Line Interface (CLI).

---

## 1. Prerequisites Checklist

Before proceeding, ensure your environment meets the [System Requirements](SYSTEM_REQUIREMENTS.md):
- **PHP 8.2.0 or higher** with required extensions (`bcmath`, `curl`, `fileinfo`, `intl`, `mbstring`, `openssl`, `pdo_mysql`, `gd`, `zip`).
- **MySQL 5.7+ / 8.0+** or **MariaDB 10.3+** with `InnoDB` engine and `utf8mb4` collation.
- Web server with URL rewrite support (Apache, Nginx, or LiteSpeed).
- Writable permissions on `/storage`, `/bootstrap/cache`, and `/upload`.

---

## 2. Installation Methods

### Method 1: The Visual Web Installer (Recommended)

MYADS includes a secure visual setup wizard at `/install`.

1. **Upload Files:** Extract the MYADS archive into your web server root directory.
2. **Configure Document Root:** Point your web server document root to the `/public` folder inside the project.
3. **Set Permissions:**
   ```bash
   chmod -R 775 storage bootstrap/cache upload public/themes
   ```
4. **Launch Installer:** Open your browser and navigate to:
   ```text
   http://yourdomain.com/install
   ```
5. **Follow Wizard Steps:**
   - **Step 1 (Welcome):** System overview and license agreement.
   - **Step 2 (Requirements):** Automated verification of PHP version, extensions, and directory write permissions.
   - **Step 3 (Database Setup):** Enter your MySQL database host, database name, username, and password. (Connection errors are securely masked).
   - **Step 4 (Database Migration):** The installer executes schema migrations and default database seeding automatically.
   - **Step 5 (Administrator Setup):** Create the primary Super Admin account (`id=1`).
6. **Completion:** The installer generates cryptographic locks to prevent unauthorized re-installation.

---

### Method 2: Command Line Interface (CLI / Advanced)

For developers and VPS administrators:

1. **Clone or Extract Project:**
   ```bash
   cd /var/www/myads
   ```

2. **Install PHP & Node Dependencies:**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```

3. **Configure Environment File:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Edit `.env` Database Credentials:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=myads_db
   DB_USERNAME=myads_user
   DB_PASSWORD=your_secure_password
   ```

5. **Run Migrations and Database Seeders:**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```

6. **Create Public Storage Symlink:**
   ```bash
   php artisan storage:link
   ```

7. **Clear & Warm Up Cache:**
   ```bash
   php artisan optimize:clear
   ```

8. **Finish Administrator Setup:** Navigate to `https://yourdomain.com/install` to finalize the super admin account.

---

## 3. Web Server Virtual Host Configuration

### Nginx Configuration Example

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/myads/public;
    index index.php index.html;

    # SSL Certificates (Managed by Let's Encrypt / Certbot)
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Real-Time SSE Stream Endpoint
    location /live/stream {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Connection '';
        proxy_http_version 1.1;
        chunked_transfer_encoding off;
        proxy_buffering off;
        proxy_cache off;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 4. Post-Installation Configuration

### 4.1 Automated Scheduler (Cron Job)
Configure the system cron job to run the Laravel schedule every minute for scheduled ad payouts, point ledger reconciliation, and database cleanup:

```bash
* * * * * cd /var/www/myads && php artisan schedule:run >> /dev/null 2>&1
```

### 4.2 Cloudflare & Reverse Proxy SSL Trust
MYADS v4.5.5 automatically configures proxy trust in `bootstrap/app.php` (`$middleware->trustProxies(at: '*')`). When running behind Cloudflare or a reverse proxy, ensure SSL mode is set to **Full (Strict)**.

### 4.3 Emergency Maintenance Bypass
Visit `/admin/maintenance` to manage site availability. You can configure:
- **Allowed IP Whitelist:** Allows admins and developers to access the site while 503 maintenance mode is active.
- **Emergency Secret Token:** Use a 1-click secret bypass link (`https://yourdomain.com/?bypass_token=...`) to access the admin console during emergencies.

---

## 5. Upgrading an Existing Installation

1. **Create full database and file backups.**
2. Pull latest code and install dependencies:
   ```bash
   git pull
   composer install --no-dev --optimize-autoloader
   ```
3. **Execute Preflight Safety Check:**
   ```bash
   php artisan myads:update:preflight
   ```
4. **Apply Migrations Safely:**
   ```bash
   php artisan down
   php artisan migrate --force
   php artisan optimize:clear
   php artisan up
   ```
