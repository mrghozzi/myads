<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = auth('sanctum')->user();
        $userReaction = null;
        $hasLiked = false;

        $groupedReactions = $this->grouped_reactions ?? [];
        if ($user && is_array($groupedReactions)) {
            foreach ($groupedReactions as $reactionType => $users) {
                foreach ($users as $u) {
                    if ($u->id === $user->id) {
                        $hasLiked = true;
                        $userReaction = $reactionType;
                        break 2;
                    }
                }
            }
        }

        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'type' => $this->s_type,
            'post_kind' => $this->post_kind,
            'text' => $this->txt,
            'date' => $this->date,
            'date_formatted' => $this->date_formatted,
            'reactions_count' => $this->reactions_count,
            'comments_count' => $this->comments_count,
            'reposts_count' => $this->reposts_count,
            'group_id' => $this->group_id,
            'has_liked' => $hasLiked,
            'user_reaction' => $userReaction,
            'grouped_reactions' => collect($groupedReactions)->map(fn($users) => count($users))->toArray(),
            'display_title' => $this->getDisplayTitle(),
            'display_content' => $this->getDisplayContent(),
            'display_image' => $this->getDisplayImage(),
            'related_content' => $this->related_content,
            'repost_record' => $this->whenLoaded('repostRecord'),
            'link_preview' => $this->whenLoaded('linkPreviewRecord'),
        ];
    }

    protected function getDisplayTitle()
    {
        $content = $this->related_content;
        if (!$content) return null;
        
        return $content->title ?? $content->name ?? null;
    }

    protected function getDisplayContent()
    {
        $content = $this->related_content;
        if (!$content) return $this->txt;
        
        return $content->content ?? $content->description ?? $content->txt ?? $this->txt;
    }

    protected function getDisplayImage()
    {
        $content = $this->related_content;
        if (!$content) return null;

        if (isset($content->screenshot)) {
            return asset('upload/' . $content->screenshot);
        }
        if (isset($content->icon)) {
            return asset('upload/store/' . $content->icon);
        }
        
        if (isset($content->image_url) && $content->image_url) {
             return asset('upload/forum/' . $content->image_url);
        }
        if (isset($content->attachments) && $content->attachments && $content->attachments->count() > 0) {
             return asset('upload/forum/' . $content->attachments->first()->name);
        }

        return null;
    }
}
