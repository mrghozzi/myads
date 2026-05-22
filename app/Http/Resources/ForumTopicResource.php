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
            'category_id' => $this->id_cat,
            'title' => $this->title,
            'content' => $this->text,
            'author' => new UserResource($this->whenLoaded('author')),
            'views' => $this->vu,
            'replies_count' => $this->nrep,
            'is_pinned' => $this->ep == 1,
            'is_locked' => $this->fermer == 1,
            'created_at' => $this->date,
            'date_formatted' => $this->date ? Carbon::createFromTimestamp($this->date)->diffForHumans() : '',
        ];
    }
}
