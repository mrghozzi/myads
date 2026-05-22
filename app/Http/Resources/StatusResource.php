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
            'media' => $this->getMedia(),
            'gallery' => $this->getGallery(),
            'attachments' => $this->getAttachments(),
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

        // Directory listing screenshot
        if (isset($content->screenshot) && $content->screenshot) {
            return asset('upload/' . $content->screenshot);
        }

        // Store product icon
        if (isset($content->icon) && $content->icon) {
            return asset('upload/store/' . $content->icon);
        }
        
        // Forum topic image via imageOption relation
        if (isset($content->image_url) && $content->image_url) {
            return asset($content->image_url);
        }

        // First image attachment
        if (isset($content->attachments) && $content->attachments && $content->attachments->count() > 0) {
            $firstImage = $content->attachments->first(fn($a) => str_starts_with((string) ($a->mime_type ?? ''), 'image/'));
            if ($firstImage) {
                return asset($firstImage->file_path);
            }
        }

        return null;
    }

    /**
     * Get the primary media for multimedia posts (video, audio, reels, music).
     */
    protected function getMedia(): ?array
    {
        $sType = (int) $this->s_type;
        $content = $this->related_content;

        if (!$content) return null;

        // Map s_type to media type
        $mediaType = match ($sType) {
            10 => 'video',
            11 => 'audio',
            12 => 'file',
            13 => 'music',
            14 => 'reels',
            4  => 'image',
            default => null,
        };

        if (!$mediaType) return null;

        // For multimedia posts, the first attachment is the media file
        if (in_array($sType, [10, 11, 12, 13, 14]) && isset($content->attachments) && $content->attachments->count() > 0) {
            $attachment = $content->attachments->first();
            return [
                'type' => $mediaType,
                'url' => asset($attachment->file_path),
                'mime_type' => $attachment->mime_type,
                'name' => $attachment->original_name,
                'size' => (int) $attachment->file_size,
            ];
        }

        // For image posts, return the image URL
        if ($sType === 4) {
            $imageUrl = $this->getDisplayImage();
            if ($imageUrl) {
                return [
                    'type' => 'image',
                    'url' => $imageUrl,
                    'mime_type' => 'image/jpeg',
                    'name' => null,
                    'size' => 0,
                ];
            }
        }

        return null;
    }

    /**
     * Get the gallery (multiple image URLs) for image posts.
     */
    protected function getGallery(): array
    {
        $content = $this->related_content;
        if (!$content) return [];

        $gallery = [];

        // Collect image attachments
        if (isset($content->attachments) && $content->attachments) {
            foreach ($content->attachments as $attachment) {
                if (str_starts_with((string) ($attachment->mime_type ?? ''), 'image/')) {
                    $gallery[] = asset($attachment->file_path);
                }
            }
        }

        // Fallback to imageOption if no image attachments
        if (empty($gallery) && isset($content->image_url) && $content->image_url) {
            $gallery[] = asset($content->image_url);
        }

        return $gallery;
    }

    /**
     * Get all attachments as a structured array.
     */
    protected function getAttachments(): array
    {
        $content = $this->related_content;
        if (!$content || !isset($content->attachments) || !$content->attachments) {
            return [];
        }

        return $content->attachments->map(fn($a) => [
            'url' => asset($a->file_path),
            'mime_type' => $a->mime_type,
            'name' => $a->original_name,
            'size' => (int) $a->file_size,
        ])->values()->toArray();
    }
}

