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
            'title' => $this->name,
            'content' => $this->txt,
            'author' => new UserResource($this->whenLoaded('user')),
            'views' => $this->vu,
            'replies_count' => $this->reply,
            'is_pinned' => $this->is_pinned,
            'is_locked' => $this->is_locked,
            'created_at' => $this->date,
            'date_formatted' => $this->date ? Carbon::createFromTimestamp($this->date)->diffForHumans() : '',
        ];
    }
}
