<?php

namespace App\Providers;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register bindings — nothing to register.
     */
    public function register(): void
    {
        //
    }

    /**
     * Apply mail settings from the database onto Laravel's mail config.
     *
     * If the table does not exist (pre-migration) or no row has been saved
     * yet, this method exits silently and the framework falls back to the
     * values in config/mail.php / .env.
     */
    public function boot(): void
    {
        try {
            if (!Schema::hasTable('mail_settings')) {
                return;
            }

            $settings = MailSetting::query()->first();

            if (!$settings || !$settings->exists) {
                return;
            }

            // Map stored encryption values to Laravel 12 / Symfony DSN schemes:
            //   tls  → "smtp"  (STARTTLS — port 587)
            //   ssl  → "smtps" (Implicit TLS — port 465)
            //   null → null    (auto-detect from port)
            $scheme = match ($settings->mail_encryption) {
                'tls'   => 'smtp',
                'ssl'   => 'smtps',
                default => null,
            };

            config([
                'mail.default' => $settings->mail_mailer ?: config('mail.default', 'smtp'),

                'mail.mailers.smtp.host'     => $settings->mail_host ?: config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port'     => $settings->mail_port ? (int) $settings->mail_port : (int) config('mail.mailers.smtp.port', 587),
                'mail.mailers.smtp.username' => $settings->mail_username ?: config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password' => $settings->mail_password ?: config('mail.mailers.smtp.password'),
                'mail.mailers.smtp.scheme'   => $scheme,

                'mail.from.address' => $settings->mail_from_address ?: config('mail.from.address'),
                'mail.from.name'    => $settings->mail_from_name ?: config('mail.from.name'),
            ]);

            // Purge any previously resolved mailer so the fresh config takes effect
            try {
                Mail::purge('smtp');
            } catch (\Throwable) {
                // Mailer may not have been resolved yet — safe to ignore
            }
        } catch (\Throwable $e) {
            // Silently fail — database may be unreachable during install/migration
            // Log the error for debugging on production
            try {
                Log::warning('MailConfigServiceProvider: ' . $e->getMessage());
            } catch (\Throwable) {
                // Logger itself may not be available
            }
        }
    }
}
