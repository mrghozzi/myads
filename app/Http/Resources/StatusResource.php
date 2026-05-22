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
            // Depending on the post kind, we may want to attach the related content (e.g. image/video url)
            // 'related_content' => $this->whenLoaded('relatedContent') // Note: related_content is an accessor in Status
        ];
    }
}
