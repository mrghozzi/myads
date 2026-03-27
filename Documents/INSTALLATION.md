# Installation Guide

This guide covers the process of installing MYADS v4.0 on your server.

## Installation Methods

### Method 1: The Visual Web Installer (Recommended)

MYADS v4.0 includes a built-in, user-friendly visual installer wizard.

1. **Upload Files:** Upload the entire `myads` folder contents to your web server.
2. **Set Document Root:** 
   - Ensure your domain's document root points to the `public/` directory inside the project.
   - If you are on shared hosting and cannot change the document root, the included `.htaccess` file in the main folder will attempt to route traffic to `/public` automatically.
3. **Set Permissions:** Ensure the `storage/` and `bootstrap/cache/` directories are writable by the web server (usually `chmod -R 775`).
4. **Run Installer:** Open your browser and navigate to `http://yourdomain.com/install`.
5. **Wizard Steps:**
   - **Welcome:** Overview of the installation.
   - **Requirements:** Checks for correct PHP version, extensions, and folder permissions. (GD and ZIP extensions are optional but recommended).
   - **Database Setup:** Define your `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, and `DB_PASSWORD`. This creates the `.env` file.
   - **Migration Setup:** The installer will run `php artisan migrate --force` and inject the default database structures and settings.
   - **Admin Creation:** You will be prompted to create the primary Administrator account (this will be user `id=1`).

### Method 2: Command Line Interface (CLI) / Developers

If you prefer installing via SSH:

1. Clone or download the repository to your server.
2. Install PHP and Node.js dependencies:
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```
3. Prepare the environment file:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Update the `.env` file with your database credentials.
5. Run migrations and seed the database:
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   ```
6. Create the storage symlink:
   ```bash
   php artisan storage:link
   ```
7. Visit `http://yourdomain.com/install` to finish setting up the initial Admin account.

## Upgrading from legacy v3.2.x

Since v4.0 is a complete rewrite to Laravel, the upgrade process preserves your data while moving it to the new architecture.

1. **BACKUP!** Backup your old files and the SQL database entirely.
2. Upload the v4.0 files, overwriting the old system files. **Keep your `upload/` directory intact to preserve user avatars and attachments!**
3. Navigate to `http://yourdomain.com/install`.
4. Provide the **existing database credentials**.
5. The system will detect the old tables (`users`, `options`, etc.), inject the missing structural tables (`setting`, `menu`, `forum_moderators`, etc.), and run the compatibility migrations.
6. Old MD5 user passwords will be supported automatically through Laravel's authentication system (upgraded to Bcrypt upon their next login).

## Updating an existing MYADS installation from Git

1. **Create a full backup first.** Export your database and keep a copy of your project files before touching the codebase.
2. Pull the new release files:
   ```bash
   git pull
   composer install --no-dev --optimize-autoloader
   ```
3. Run the built-in update safety check:
   ```bash
   php artisan myads:update:preflight
   ```
4. If the preflight check passes, enable maintenance mode and apply the schema changes:
   ```bash
   php artisan down
   php artisan migrate --force
   php artisan optimize:clear
   php artisan up
   ```
5. Verify the site in the browser and keep the backup until you confirm everything is healthy.

### Critical safety warning

- Never run `php artisan migrate:fresh`, `php artisan db:wipe`, or `php artisan test` against your live site database.
- The supported upgrade path is additive only: `git pull` + `php artisan myads:update:preflight` + `php artisan migrate --force`.
- If you are unsure, stop and take a new backup before continuing.
