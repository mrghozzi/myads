<?php

namespace App\Providers;

use App\Models\MailSetting;
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

            config([
                'mail.default' => $settings->mail_mailer,

                'mail.mailers.smtp.host'     => $settings->mail_host,
                'mail.mailers.smtp.port'     => (int) $settings->mail_port,
                'mail.mailers.smtp.username' => $settings->mail_username,
                'mail.mailers.smtp.password' => $settings->mail_password, // decrypted via accessor
                'mail.mailers.smtp.scheme'   => $settings->mail_encryption,

                'mail.from.address' => $settings->mail_from_address,
                'mail.from.name'    => $settings->mail_from_name,
            ]);
        } catch (\Throwable $e) {
            // Silently fail — database may be unreachable during install/migration
        }
    }
}
