<?php

namespace App\Services;

use App\Models\Message;
use App\Models\User;
use App\Support\SecuritySettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MessageConversationService
{
    public const PAGE_SIZE = 25;
    public const CONVERSATION_PAGE_SIZE = 9;

    public function conversationSummaries(User $user): array
    {
        $messages = Message::where('us_rec', $user->id)
            ->orWhere('us_env', $user->id)
            ->orderBy('time', 'desc')
            ->orderBy('id_msg', 'desc')
            ->get();

        $partnerIds = [];
        foreach ($messages as $message) {
            $partnerId = $this->partnerIdForMessage($message, $user);
            if (!in_array($partnerId, $partnerIds, true)) {
                $partnerIds[] = $partnerId;
            }
        }

        $partners = User::whereIn('id', $partnerIds)->get()->keyBy('id');
        $unreadMap = array_flip($this->unreadPartnerIds($user));

        $conversations = [];
        $added = [];
        foreach ($messages as $message) {
            $partnerId = $this->partnerIdForMessage($message, $user);
            if (isset($added[$partnerId])) {
                continue;
            }

            $partner = $partners->get($partnerId);
            if (!$partner) {
                continue;
            }

            $added[$partnerId] = true;
            $conversations[] = [
                'user' => $partner,
                'message' => $message,
                'unread' => isset($unreadMap[$partnerId]),
                'route_key' => $this->conversationRouteKey($user, $partner),
            ];
        }

        return $conversations;
    }

    public function paginatedConversations(User $user, int $perPage = self::CONVERSATION_PAGE_SIZE): array
    {
        $conversations = $this->conversationSummaries($user);
        $page = LengthAwarePaginator::resolveCurrentPage();
        $collection = collect($conversations);

        $paged = new LengthAwarePaginator(
            $collection->forPage($page, $perPage)->values(),
            $collection->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return [$paged, $conversations];
    }

    public function conversationQuery(User $user, User $partner): Builder
    {
        return Message::query()->where(function ($conversation) use ($user, $partner) {
            $conversation->where(function ($query) use ($user, $partner) {
                $query->where('us_env', $user->id)
                    ->where('us_rec', $partner->id);
            })->orWhere(function ($query) use ($user, $partner) {
                $query->where('us_env', $partner->id)
                    ->where('us_rec', $user->id);
            });
        });
    }

    public function recentMessages(User $user, User $partner, int $limit = self::PAGE_SIZE): Collection
    {
        return $this->conversationQuery($user, $partner)
            ->orderBy('id_msg', 'desc')
            ->limit($limit)
            ->get()
            ->sortBy('id_msg')
            ->values();
    }

    public function olderMessages(User $user, User $partner, int $beforeId, int $limit = self::PAGE_SIZE): Collection
    {
        return $this->conversationQuery($user, $partner)
            ->where('id_msg', '<', $beforeId)
            ->orderBy('id_msg', 'desc')
            ->limit($limit)
            ->get()
            ->sortBy('id_msg')
            ->values();
    }

    public function newerMessages(User $user, User $partner, int $afterId): Collection
    {
        return $this->conversationQuery($user, $partner)
            ->where('id_msg', '>', $afterId)
            ->orderBy('id_msg', 'asc')
            ->get();
    }

    public function hasOlderMessages(User $user, User $partner, ?int $oldestId): bool
    {
        if (!$oldestId) {
            return false;
        }

        return $this->conversationQuery($user, $partner)
            ->where('id_msg', '<', $oldestId)
            ->exists();
    }

    public function boundaryContext(User $user, User $partner, Collection $messages): array
    {
        $firstMessage = $messages->first();
        if (!$firstMessage) {
            return [
                'hasPreviousConversationMessage' => false,
                'precedingMessageEncrypted' => false,
            ];
        }

        $previousMessage = $this->conversationQuery($user, $partner)
            ->where('id_msg', '<', $firstMessage->id_msg)
            ->orderBy('id_msg', 'desc')
            ->first();

        return [
            'hasPreviousConversationMessage' => $previousMessage !== null,
            'precedingMessageEncrypted' => $previousMessage?->isEncryptedPayload() ?? false,
        ];
    }

    public function markConversationAsRead(User $user, User $partner): int
    {
        return Message::where('us_env', $partner->id)
            ->where('us_rec', $user->id)
            ->where('state', '!=', 0)
            ->update(['state' => 0]);
    }

    public function markAllAsRead(User $user): int
    {
        return Message::where('us_rec', $user->id)
            ->where('state', '!=', 0)
            ->update(['state' => 0]);
    }

    public function unreadPartnerIds(User $user): array
    {
        return Message::where('us_rec', $user->id)
            ->where('state', '!=', 0)
            ->groupBy('us_env')
            ->pluck('us_env')
            ->all();
    }

    public function unreadConversationCount(User $user): int
    {
        return count($this->unreadPartnerIds($user));
    }

    public function latestVisibleMessageId(User $user): int
    {
        return (int) Message::where('us_rec', $user->id)
            ->orWhere('us_env', $user->id)
            ->max('id_msg');
    }

    public function latestIncomingToast(User $user, int $afterId = 0): ?array
    {
        $message = Message::where('us_rec', $user->id)
            ->where('us_env', '!=', $user->id)
            ->where('id_msg', '>', $afterId)
            ->orderBy('id_msg', 'desc')
            ->first();

        if (!$message) {
            return null;
        }

        $sender = User::find($message->us_env);
        if (!$sender) {
            return null;
        }

        $preview = trim(strip_tags((string) ($message->text ?? '')));
        if ($preview === '' && !empty($message->attachment_path)) {
            $preview = __('messages.file');
        }

        return [
            'id' => (int) $message->id_msg,
            'title' => __('messages.new_message'),
            'body' => $sender->username . ': ' . (string) str($preview)->limit(80),
            'url' => route('messages.show', $this->conversationRouteKey($user, $sender)),
            'avatar_url' => $sender->avatarUrl(),
            'sender' => $sender->username,
        ];
    }

    public function resolvePartner(string|int $id, User $user): ?User
    {
        $partnerId = Message::decodeConversationRouteKey($id, $user);
        $partner = $partnerId ? User::find($partnerId) : null;
        if ($partner) {
            return $partner;
        }

        // Fallback: try resolving by username (mobile app may pass username directly)
        $byUsername = User::where('username', $id)->first();
        if ($byUsername) {
            return $byUsername;
        }

        if ((bool) SecuritySettings::get('private_message_encryption_enabled', 0)) {
            return null;
        }

        $message = Message::whereKey($id)
            ->where(function ($query) use ($user) {
                $query->where('us_rec', $user->id)
                    ->orWhere('us_env', $user->id);
            })
            ->first();

        if (!$message) {
            return null;
        }

        $partnerId = $this->partnerIdForMessage($message, $user);

        return User::find($partnerId);
    }

    public function conversationRouteKey(User|int $viewer, User|int $partner): string
    {
        return Message::encodeConversationRouteKey($viewer, $partner);
    }

    private function partnerIdForMessage(Message $message, User $user): int
    {
        return (int) ($message->us_env == $user->id ? $message->us_rec : $message->us_env);
    }
}
