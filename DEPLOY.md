# MyAds v4.0.0 — Deployment Guide

## Requirements
- PHP 8.1+
- MySQL 5.7+
- Extensions: PDO, mbstring, tokenizer, xml, ctype, json, bcmath, openssl, fileinfo, gd, zip

---

## Fresh Install (byet.host)

### 1. Upload Files
1. Create a MySQL database from your byet.host control panel (VistaPanel)
2. Upload all project files to `htdocs/` via FileManager or FTP
3. Make sure `.htaccess` is in the root

### 2. Run Installer
1. Navigate to `http://yourdomain.byethost.com/install`
2. Follow the wizard:
   - **Welcome** → Next
   - **Requirements** → Check all requirements pass
   - **Database** → Enter your byet.host MySQL credentials:
     - Host: `sqlXXX.byethost.com` (check your control panel)
     - Port: `3306`
     - Database: your database name
     - Username: your database username
     - Password: your database password
     - App URL: `http://yourdomain.byethost.com`
   - **Migrate** → Click "Run Migrations"
   - **Admin Setup** → Create admin account
   - **Finish** → Done!

### 3. Post-Install
- Go to `/admin` to manage your site
- Configure settings, languages, themes, etc.

---

## Upgrade from v3.x

### 1. Backup Old Data
1. Export your old MySQL database from phpMyAdmin
2. Download your `upload/` folder

### 2. Upload New Files
1. Upload all v4.0.0 files to a new directory (e.g., `htdocs/`)
2. Copy your old `upload/` folder to the new project's `upload/` directory

### 3. Configure Database
1. Edit `.env` file and set your database credentials (same database as old version)

### 4. Run Upgrade
1. Navigate to `http://yourdomain.byethost.com/install/update`
2. Click "Start Upgrade"
3. The system will automatically:
   - Add missing columns (timestamps, Laravel auth columns)
   - Create new tables (sessions, cache, password_resets, etc.)
   - Add cookie consent settings
   - Seed default data (store categories, languages, version)
   - Copy old upload files
   - Generate APP_KEY
   - Update version to 4.0.0

### 4. Verify
- Login with your existing credentials
- Check `/admin` panel
- Verify all data migrated correctly

---

## Folder Structure
```
htdocs/
├── .htaccess          # Routes all requests to public/
├── index.php          # Entry point (loads public/index.php)
├── public/            # Public assets
├── app/               # Application code
├── config/            # Configuration files
├── database/          # Migrations & seeders
├── installer/         # Installation wizard
├── lang/              # Translation files (9 languages)
├── routes/            # Route definitions
├── storage/           # Logs, cache, sessions
├── themes/            # Theme files
├── upload/            # User uploads
└── vendor/            # Dependencies (included)
```

---

## Troubleshooting

### Blank page after install
- Check `storage/logs/laravel.log` for errors
- Ensure `storage/` and `bootstrap/cache/` are writable (chmod 775)

### 500 Error
- Verify `.htaccess` files are uploaded
- Check PHP version (must be 8.1+)

### Database connection failed
- Verify database host (usually `sqlXXX.byethost.com`)
- Check username and password in `.env`

### Symlink error
- byet.host may not support symlinks. The installer has a fallback mechanism
