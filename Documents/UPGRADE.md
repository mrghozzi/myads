# MYADS Upgrade & Migration Guide

This document outlines the standard, zero-downtime procedure for upgrading an existing MYADS deployment to **v4.5.5**.

---

## 1. Pre-Upgrade Preparation & Backups

> [!IMPORTANT]
> **Always back up before upgrading.** Never perform an upgrade without a verified database and file backup.

1. **Database Backup:** Create a full SQL dump:
   ```bash
   mysqldump -u myads_user -p myads_db > backup_$(date +%Y%m%d_%H%M%S).sql
   ```
2. **Files Backup:** Create an archive of your custom files and user uploads:
   ```bash
   tar -czf uploads_backup.tar.gz upload/ storage/ app/ .env
   ```

---

## 2. Safe CLI Upgrade Procedure

Follow these commands in sequence from your project root:

```bash
# 1. Pull the latest release code
git pull origin main

# 2. Update PHP dependencies with optimized class mapping
composer install --no-dev --optimize-autoloader

# 3. Run the automated preflight safety check
php artisan myads:update:preflight

# 4. Enable maintenance mode (Admins with allowed IP can still access)
php artisan down

# 5. Execute safe, additive database schema migrations
php artisan migrate --force

# 6. Flush stale cache and re-warm system configurations
php artisan optimize:clear

# 7. Bring the application back online
php artisan up
```

---

## 3. The `myads:update:preflight` Safety Engine

MYADS includes an automated preflight check command (`php artisan myads:update:preflight`) that verifies:
- **Database Connectivity:** Validates active PDO connection and database version.
- **Storage & Cache Permissions:** Ensures `/storage`, `/bootstrap/cache`, and `/upload` are writable.
- **Pending Migrations Analysis:** Checks for pending schema changes and scans for potentially destructive operations.
- **InnoDB Dynamic Row Format:** Ensures high-traffic tables are compatible with compound index migrations.

If the preflight check reports any warning or failure, address the flagged issue before running `php artisan migrate`.

---

## 4. High-Traffic Compound Indexes & MySQL Engine Hardening

Starting with v4.5.3+, MYADS introduces compound indexes on high-traffic tables (`status`, `smart_ads`, `banner`, `link`, `custom_ad_events`) for sub-millisecond query performance.

If your database previously used legacy `MyISAM` or `COMPACT` row formats, the migration automatically applies:
```sql
ALTER TABLE status ENGINE = InnoDB ROW_FORMAT = DYNAMIC;
ALTER TABLE smart_ads ENGINE = InnoDB ROW_FORMAT = DYNAMIC;
ALTER TABLE banner ENGINE = InnoDB ROW_FORMAT = DYNAMIC;
ALTER TABLE link ENGINE = InnoDB ROW_FORMAT = DYNAMIC;
ALTER TABLE custom_ad_events ENGINE = InnoDB ROW_FORMAT = DYNAMIC;
```
This guarantees full `utf8mb4` multibyte unicode support without encountering MySQL 1000-byte index key length errors.

---

## 5. Post-Upgrade Maintenance & Optimization

After bringing the application online:

1. **Orphaned Records Audit:**
   - Log in to the Admin Panel as Super Admin (`id=1`).
   - Navigate to `/admin/maintenance`.
   - Run the **Safe Orphan Records Audit** powered by `OrphanCleanupService` to clean dangling notification or reaction options safely.
2. **Live Theme Customizer Check:**
   - Visit `/admin/themes/customizer` to verify your custom variables and regenerate `custom_variables.css` if necessary.
3. **Real-Time Live Stream Test:**
   - Open the website in a browser to confirm the Server-Sent Events (SSE) stream connects cleanly without proxy buffering errors.

---

## 6. Admin Panel Web Updates (`/admin/updates`)

When upgrading via the Admin Control Panel:
- The system requires explicit checkboxes confirming that you have taken a database and file backup.
- Custom update scripts (`update.php`) are validated against their cryptographic **SHA-256 signature** (`update.php.sha256`) before execution.
- If the preflight validation fails, the visual updater will prevent execution to safeguard the existing database.

---

## 7. Prohibited Production Commands

> [!CAUTION]
> **NEVER** run the following commands on a production database:
> - `php artisan migrate:fresh` (Drops all database tables and deletes all data)
> - `php artisan db:wipe` (Wipes the entire database schema)
> - `php artisan test` (May truncate tables during isolated test suite runs)
