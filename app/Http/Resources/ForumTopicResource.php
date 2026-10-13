<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ForumTopicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->cat,
            'category' => new ForumCategoryResource($this->whenLoaded('category')),
            'title' => $this->name,
            'slug' => $this->slug,
            'content' => $this->txt,
            'author' => new UserResource($this->whenLoaded('user')),
            'last_poster' => new UserResource($this->whenLoaded('lastPoster')),
            'last_posted_at' => $this->last_posted_at,
            'last_posted_at_formatted' => $this->last_posted_at ? Carbon::createFromTimestamp((int) $this->last_posted_at)->diffForHumans() : null,
            'views' => $this->vu ?? 0,
            'replies_count' => (int) ($this->reply ?? $this->comments_count ?? 0),
            'is_pinned' => (bool) $this->is_pinned,
            'is_locked' => (bool) $this->is_locked,
            'created_at' => $this->date ? Carbon::createFromTimestamp((int) $this->date)->diffForHumans() : '',
            'created_at_timestamp' => $this->date,
            'date_formatted' => $this->date ? Carbon::createFromTimestamp((int) $this->date)->diffForHumans() : '',
            'replies' => CommentResource::collection($this->whenLoaded('comments')),
        ];
    }
}
