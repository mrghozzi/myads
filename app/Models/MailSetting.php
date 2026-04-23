<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

class MailSetting extends Model
{
    protected $table = 'mail_settings';

    protected $fillable = [
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
    ];

    /* ------------------------------------------------------------------ */
    /*  Password encryption / decryption                                  */
    /* ------------------------------------------------------------------ */

    /**
     * Encrypt the password before storing it.
     */
    public function setMailPasswordAttribute(?string $value): void
    {
        if ($value === null || $value === '') {
            $this->attributes['mail_password'] = null;
            return;
        }

        $this->attributes['mail_password'] = Crypt::encryptString($value);
    }

    /**
     * Decrypt the password when reading it.
     */
    public function getMailPasswordAttribute(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable $e) {
            // Corrupted or legacy plain-text value — return null for safety
            return null;
        }
    }

    /* ------------------------------------------------------------------ */
    /*  Singleton helpers                                                  */
    /* ------------------------------------------------------------------ */

    /**
     * Return the stored settings row, or a non-persisted instance with
     * fallback values from config('mail') so the form always has data.
     */
    public static function current(): self
    {
        try {
            if (!Schema::hasTable('mail_settings')) {
                return new static(static::defaults());
            }

            return static::query()->first() ?? new static(static::defaults());
        } catch (\Throwable $e) {
            return new static(static::defaults());
        }
    }

    /**
     * Like current(), but creates a row when none exists.
     */
    public static function currentPersisted(): self
    {
        try {
            if (!Schema::hasTable('mail_settings')) {
                return new static(static::defaults());
            }

            return static::query()->first() ?? static::query()->create(static::defaults());
        } catch (\Throwable $e) {
            return new static(static::defaults());
        }
    }

    /**
     * Fallback values sourced from the Laravel config / .env.
     */
    public static function defaults(): array
    {
        return [
            'mail_mailer'       => config('mail.default', 'smtp'),
            'mail_host'         => config('mail.mailers.smtp.host', '127.0.0.1'),
            'mail_port'         => config('mail.mailers.smtp.port', 587),
            'mail_username'     => config('mail.mailers.smtp.username'),
            'mail_encryption'   => config('mail.mailers.smtp.scheme'),
            'mail_from_address' => config('mail.from.address', 'hello@example.com'),
            'mail_from_name'    => config('mail.from.name', 'Example'),
            // password is intentionally omitted from defaults — never copy .env password
        ];
    }
}
