<?php

namespace App\Services;

use App\Models\Status;
use App\Models\StatusMention;
use App\Models\User;
use App\Support\ContentFormatter;

class MentionService
{
    public function __construct(
        private readonly NotificationService $notifications,
        private readonly UserPrivacyService $privacy,
        private readonly V420SchemaService $schema
    ) {
    }

    public function createStatusMentions(User $author, Status $status, ?string $text, string $url): void
    {
        if (!$this->schema->supports('mentions')) {
            return;
        }

        $usernames = ContentFormatter::extractMentionUsernames($text);
        if ($usernames === []) {
            return;
        }

        $mentionedUsers = User::query()
            ->whereIn(\DB::raw('LOWER(username)'), $usernames)
            ->get();

        foreach ($mentionedUsers as $mentionedUser) {
            if ((int) $mentionedUser->id === (int) $author->id) {
                continue;
            }

            if (!$this->privacy->canMention($mentionedUser, $author)) {
                continue;
            }

            StatusMention::create([
                'user_id' => $author->id,
                'mentioned_user_id' => $mentionedUser->id,
                'status_id' => $status->id,
                'username' => $mentionedUser->username,
            ]);

            $this->notifications->send(
                $mentionedUser->id,
                __('messages.mention_notification_post', ['user' => $author->username]),
                $url,
                'comment',
                $author->id
            );
        }
    }

    public function createCommentMentions(User $author, string $commentType, int $commentId, ?string $text, string $url): void
    {
        if (!$this->schema->supports('mentions')) {
            return;
        }

        $usernames = ContentFormatter::extractMentionUsernames($text);
        if ($usernames === []) {
            return;
        }

        $mentionedUsers = User::query()
            ->whereIn(\DB::raw('LOWER(username)'), $usernames)
            ->get();

        foreach ($mentionedUsers as $mentionedUser) {
            if ((int) $mentionedUser->id === (int) $author->id) {
                continue;
            }

            if (!$this->privacy->canMention($mentionedUser, $author)) {
                continue;
            }

            StatusMention::create([
                'user_id' => $author->id,
                'mentioned_user_id' => $mentionedUser->id,
                'comment_type' => $commentType,
                'comment_id' => $commentId,
                'username' => $mentionedUser->username,
            ]);

            $this->notifications->send(
                $mentionedUser->id,
                __('messages.mention_notification_comment', ['user' => $author->username]),
                $url,
                'comment',
                $author->id
            );
        }
    }
}
