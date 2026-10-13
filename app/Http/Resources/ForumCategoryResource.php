<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForumCategoryResource extends JsonResource
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
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'color' => $this->color ?: '#615dfa',
            'description' => $this->txt ?: ($this->des ?? ''),
            'icon' => $this->icons ?: ($this->icon ?? 'fa-comments'),
            'topics_count' => $this->topics_count ?? $this->forum_topics_count ?? ($this->topic_count ?? 0),
            'visibility' => $this->visibility,
            'children' => ForumCategoryResource::collection($this->whenLoaded('children')),
            'subcategories' => ForumCategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
