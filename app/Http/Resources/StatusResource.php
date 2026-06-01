<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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

        $hasSaved = false;
        $savedCount = \Illuminate\Support\Facades\DB::table('saved_statuses')
            ->where('status_id', $this->id)
            ->count();

        if ($user) {
            $hasSaved = \Illuminate\Support\Facades\DB::table('saved_statuses')
                ->where('user_id', $user->id)
                ->where('status_id', $this->id)
                ->exists();
        }

        $isOwner = $user ? ((int) $this->uid === (int) $user->id) : false;
        $canEdit = $isOwner || ($user && method_exists($user, 'isAdmin') && $user->isAdmin());
        $canDelete = $canEdit;

        $groupMeta = null;
        if ($this->group_id && $this->relationLoaded('groupRecord') && $this->groupRecord) {
            $groupMeta = [
                'id' => $this->groupRecord->id,
                'name' => $this->groupRecord->name,
                'slug' => $this->groupRecord->slug,
            ];
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
            'group' => $groupMeta,
            'can_edit' => $canEdit,
            'can_delete' => $canDelete,
            'is_owner' => $isOwner,
            'has_liked' => $hasLiked,
            'user_reaction' => $userReaction,
            'grouped_reactions' => collect($groupedReactions)->map(fn($users) => count($users))->toArray(),
            'interaction_subject_id' => $this->interactionSubjectId(),
            'reaction_type' => $this->getReactionType(),
            'has_saved' => $hasSaved,
            'saved_count' => $savedCount,
            'permalink' => method_exists($this->resource, 'promotionDestinationUrl') ? $this->promotionDestinationUrl() : url('/portal'),
            'display_title' => $this->getDisplayTitle(),
            'display_content' => $this->getDisplayContent(),
            'display_image' => $this->getDisplayImage(),
            'media' => $this->getMedia(),
            'gallery_items' => $this->getGallery(),
            'attachments' => $this->getAttachments(),
            'activity_card' => $this->getActivityCard(),
            'related_content' => $this->related_content,
            'repost_record' => $this->relationLoaded('repostRecord') && $this->repostRecord
                ? [
                    'id' => $this->repostRecord->id,
                    'status_id' => $this->repostRecord->status_id,
                    'original_status_id' => $this->repostRecord->original_status_id,
                    'user_id' => $this->repostRecord->user_id,
                    'original_status' => $this->repostRecord->originalStatus 
                        ? new StatusResource($this->repostRecord->originalStatus) 
                        : null,
                  ]
                : null,
            'link_preview' => $this->whenLoaded('linkPreviewRecord'),
            'is_promoted_ad' => $this->is_promoted_ad ?? false,
        ];
    }

    /**
     * Get a meaningful display title — suppressed for text/media posts
     * where ForumTopic.name is just the post content, not a title.
     */
    protected function getDisplayTitle()
    {
        $content = $this->related_content;
        if (!$content) return null;

        $sType = (int) $this->s_type;

        // Text/media posts don't have meaningful separate titles.
        // The ForumTopic.name for these is the post text itself, not a title.
        if (in_array($sType, [2, 4, 10, 11, 12, 13, 14, 100])) {
            return null;
        }

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
     * Get the primary media for multimedia posts (video, audio, clips, music).
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
            14 => 'clips',
            4  => 'image',
            default => null,
        };

        if (!$mediaType) return null;

        // For multimedia posts, the first attachment is the media file
        if (in_array($sType, [10, 11, 12, 13, 14]) && isset($content->attachments) && $content->attachments->count() > 0) {
            $attachment = $content->attachments->first();
            return [
                'id' => $attachment->id,
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
                    $gallery[] = [
                        'id' => $attachment->id,
                        'url' => asset($attachment->file_path),
                        'mime_type' => $attachment->mime_type,
                        'size' => (int) $attachment->file_size,
                    ];
                }
            }
        }

        // Fallback to imageOption if no image attachments
        if (empty($gallery) && isset($content->image_url) && $content->image_url) {
            $gallery[] = [
                'id' => null,
                'url' => asset($content->image_url),
                'mime_type' => 'image/jpeg',
                'size' => 0,
            ];
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
            'id' => $a->id,
            'url' => asset($a->file_path),
            'mime_type' => $a->mime_type,
            'name' => $a->original_name,
            'size' => (int) $a->file_size,
        ])->values()->toArray();
    }

    /**
     * Build a structured activity card for rich content types
     * (Store, Directory, Knowledgebase, Order).
     *
     * Returns null for text/media posts — those use display_content, media, gallery instead.
     */
    protected function getActivityCard(): ?array
    {
        $sType = (int) $this->s_type;
        $content = $this->related_content;

        if (!$content) return null;

        return match ($sType) {
            7867 => $this->buildStoreCard($content),
            1    => $this->buildDirectoryCard($content),
            205  => $this->buildKnowledgebaseCard($content),
            6    => $this->buildOrderCard($content),
            default => null,
        };
    }

    /**
     * Store product activity card.
     */
    private function buildStoreCard($product): array
    {
        $isFree = (int) ($product->o_order ?? 0) <= 0;
        $currentPrice = null;
        $originalPrice = null;

        if (!$isFree) {
            $originalPrice = (string) $product->o_order;
            if ($product->sale && $product->sale->is_active) {
                $currentPrice = (string) $product->sale->sale_price;
            } else {
                $currentPrice = $originalPrice;
                $originalPrice = null; // No sale, no strikethrough
            }
        }

        $badges = [];
        if ($product->type && $product->type->name) {
            $badges[] = ['label' => $product->type->name, 'tone' => 'primary'];
        }
        if ($product->is_suspended ?? false) {
            $badges[] = ['label' => 'Suspended', 'tone' => 'danger'];
        }

        return [
            'kind'         => 'store',
            'title'        => $product->name,
            'description'  => Str::limit($product->o_valuer ?? '', 240),
            'image_url'    => $product->product_image ?? null,
            'primary_url'  => route('store.show', $product->name),
            'external_url' => null,
            'cta_label'    => __('messages.preview'),
            'badges'       => $badges,
            'meta'         => [],
            'price'        => [
                'current'  => $currentPrice,
                'original' => $originalPrice,
                'is_free'  => $isFree,
            ],
        ];
    }

    /**
     * Directory listing activity card.
     */
    private function buildDirectoryCard($listing): array
    {
        $badges = [];
        if ($listing->category) {
            $badges[] = [
                'label' => $listing->category->name,
                'tone'  => 'primary',
            ];
        }

        $imageUrl = $listing->prominent_image ?: null;

        return [
            'kind'         => 'directory',
            'title'        => $listing->name,
            'description'  => Str::limit($listing->txt ?? '', 240),
            'image_url'    => $imageUrl,
            'primary_url'  => route('directory.show', $listing->id),
            'external_url' => $listing->url ?? null,
            'cta_label'    => __('messages.visit_site'),
            'badges'       => $badges,
            'meta'         => [
                ['icon' => 'eye', 'label' => (string) ($listing->vu ?? 0)],
            ],
            'price'        => null,
        ];
    }

    /**
     * Knowledgebase article activity card.
     */
    private function buildKnowledgebaseCard($article): array
    {
        $product = $article->productItem ?? null;
        $author = $article->authorUser ?? null;
        $productSlug = $product->name ?? $article->o_mode ?? '';
        $productName = $productSlug !== '' ? $productSlug : __('messages.knowledgebase');

        $knowledgebaseUrl = ($productSlug !== '' && $article->name)
            ? route('kb.show', ['name' => $productSlug, 'article' => $article->name])
            : '#';

        // Clean markdown from summary
        $rawSummary = html_entity_decode(strip_tags((string) ($article->o_valuer ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $rawSummary = trim((string) preg_replace('/[#>*_`~\[\]\(\)\r\n]+/u', ' ', $rawSummary));
        $summary = Str::limit((string) preg_replace('/\s+/u', ' ', $rawSummary), 240);

        return [
            'kind'         => 'knowledgebase',
            'title'        => $article->name ?? '',
            'description'  => $summary,
            'image_url'    => null,
            'primary_url'  => $knowledgebaseUrl,
            'external_url' => null,
            'cta_label'    => __('messages.preview'),
            'badges'       => [
                ['label' => __('messages.knowledgebase'), 'tone' => 'primary'],
                ['label' => $productName, 'tone' => 'neutral'],
                ['label' => __('messages.published'), 'tone' => 'success'],
            ],
            'meta'         => [
                ['icon' => 'user', 'label' => $author->username ?? __('messages.guest')],
            ],
            'price'        => null,
        ];
    }

    /**
     * Order request activity card.
     */
    private function buildOrderCard($order): array
    {
        $offersCount = $order->offers_count ?? 0;

        return [
            'kind'         => 'order',
            'title'        => $order->title ?? '',
            'description'  => Str::limit(trim(strip_tags($order->description ?? '')), 240),
            'image_url'    => null,
            'primary_url'  => route('orders.show', $order),
            'external_url' => null,
            'cta_label'    => __('messages.view_details'),
            'badges'       => [
                ['label' => $order->displayCategory(), 'tone' => 'primary'],
                ['label' => $order->displayBudget(), 'tone' => 'neutral'],
                ['label' => __('messages.offers') . ': ' . $offersCount, 'tone' => 'neutral'],
                ['label' => $order->displayWorkflowStatus(), 'tone' => 'warning'],
            ],
            'meta'         => [],
            'price'        => null,
        ];
    }
}


