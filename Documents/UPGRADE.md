# MYADS Upgrade Guide

This is the supported upgrade path for an existing MYADS installation.

## Before anything

1. Create a full SQL backup of your database.
2. Create a backup of your project files, especially custom themes, uploads, and local modifications.
3. Do **not** continue until both backups are complete and restorable.

## Safe upgrade steps

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan myads:update:preflight
php artisan down
php artisan migrate --force
php artisan optimize:clear
php artisan up
```

## What the preflight command checks

- Active database connection
- Writable paths needed by the updater
- Pending migrations
- Destructive operations inside `up()` for pending migrations

If the preflight check fails, stop and fix the reported issue before migrating.

## Never do this on a live database

- `php artisan migrate:fresh`
- `php artisan db:wipe`
- `php artisan test`

Those commands are for isolated development and testing environments only.

## Admin panel updates

The `/admin/updates` screen now requires explicit confirmation that you created:

- a database backup
- a file backup

It will also block the update if the safety preflight fails.
