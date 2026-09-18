<?php

namespace App\Models;

use App\Support\SecuritySettings;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';
    protected $primaryKey = 'id_msg';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'us_env', // Sender ID
        'us_rec', // Receiver ID
        'msg',
        'attachment_path',
        'attachment_name',
        'attachment_size',
        'time',
        'state',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'us_env');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'us_rec');
    }

    public function getTextAttribute()
    {
        return $this->decodeMessage($this->attributes['msg'] ?? null);
    }

    public function setTextAttribute($value)
    {
        $this->setMessageContent($value);
    }

    public function setMsgAttribute($value)
    {
        $this->setMessageContent($value);
    }

    private function setMessageContent($value): void
    {
        $text = (string) $value;

        if ($text === '') {
            $this->attributes['msg'] = $text;
            return;
        }

        if (Str::startsWith($text, 'enc:')) {
            $this->attributes['msg'] = $text;
            return;
        }

        if (!$this->shouldEncryptNewMessages()) {
            $this->attributes['msg'] = $text;
            return;
        }

        try {
            $this->attributes['msg'] = 'enc:' . Crypt::encryptString($text);
        } catch (\Throwable) {
            $this->attributes['msg'] = $text;
        }
    }

    public function getIdAttribute()
    {
        return $this->attributes['id_msg'] ?? null;
    }

    public function getMsgAttribute($value)
    {
        return $this->decodeMessage($value);
    }

    public function isEncryptedPayload(): bool
    {
        return Str::startsWith((string) $this->getRawOriginal('msg'), 'enc:');
    }

    public static function encodeConversationRouteKey(User|int $viewer, User|int $partner): string
    {
        $viewerId = $viewer instanceof User ? (int) $viewer->getKey() : (int) $viewer;
        $partnerId = $partner instanceof User ? (int) $partner->getKey() : (int) $partner;

        if ($viewerId <= 0 || $partnerId <= 0) {
            return (string) $partnerId;
        }

        $userA = min($viewerId, $partnerId);
        $userB = max($viewerId, $partnerId);

        $payload = json_encode([
            'user_a' => $userA,
            'user_b' => $userB,
            'viewer_id' => $viewerId,
            'partner_id' => $partnerId,
        ]);

        if (!is_string($payload) || $payload === '') {
            return (string) $partnerId;
        }

        try {
            return self::base64UrlEncode(Crypt::encryptString($payload));
        } catch (\Throwable) {
            return (string) $partnerId;
        }
    }

    public static function decodeConversationRouteKey(string|int $value, User|int|null $viewer = null): ?int
    {
        $normalized = trim((string) $value);
        if ($normalized === '') {
            return null;
        }

        if (ctype_digit($normalized)) {
            $encryptionEnabled = (bool) SecuritySettings::get('private_message_encryption_enabled', 0);
            if (!$encryptionEnabled) {
                return (int) $normalized;
            }

            return null;
        }

        $expectedViewerId = $viewer instanceof User ? (int) $viewer->getKey() : ($viewer !== null ? (int) $viewer : null);

        try {
            $decoded = self::base64UrlDecode($normalized);
            if ($decoded === null) {
                return null;
            }

            $payload = json_decode(Crypt::decryptString($decoded), true);
            if (!is_array($payload)) {
                return null;
            }

            $partnerId = (int) ($payload['partner_id'] ?? 0);
            $payloadViewerId = (int) ($payload['viewer_id'] ?? 0);
            $userA = (int) ($payload['user_a'] ?? 0);
            $userB = (int) ($payload['user_b'] ?? 0);

            if ($expectedViewerId !== null) {
                // Symmetric pair check: if expected viewer is user_a, partner is user_b (and vice versa)
                if ($userA > 0 && $userB > 0) {
                    if ($expectedViewerId === $userA) {
                        return $userB;
                    }
                    if ($expectedViewerId === $userB) {
                        return $userA;
                    }
                }

                // Legacy asymmetric check (viewer_id & partner_id) - support either participant
                if ($expectedViewerId === $payloadViewerId && $partnerId > 0) {
                    return $partnerId;
                }
                if ($expectedViewerId === $partnerId && $payloadViewerId > 0) {
                    return $payloadViewerId;
                }

                return null;
            }

            if ($partnerId > 0) {
                return $partnerId;
            }
            if ($userB > 0) {
                return $userB;
            }

            return null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function decodeMessage(?string $value): ?string
    {
        if ($value === null || !Str::startsWith($value, 'enc:')) {
            return $value;
        }

        try {
            return Crypt::decryptString(Str::after($value, 'enc:'));
        } catch (\Throwable) {
            // Never return raw ciphertext to users or AI assistants
            return __('messages.encrypted_message_unavailable');
        }
    }

    private function shouldEncryptNewMessages(): bool
    {
        return (bool) SecuritySettings::get('private_message_encryption_enabled', 0);
    }

    private static function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $value): ?string
    {
        $normalized = strtr($value, '-_', '+/');
        $remainder = strlen($normalized) % 4;
        if ($remainder > 0) {
            $normalized .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode($normalized, true);

        return $decoded === false ? null : $decoded;
    }
}
