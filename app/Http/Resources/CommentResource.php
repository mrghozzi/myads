<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->whenLoaded('user');
        $userResource = $user ? new UserResource($user) : null;

        return [
            'id' => $this->id,
            'user' => $userResource,
            'author' => $userResource,
            'topic_id' => $this->tid,
            'parent_id' => $this->parent_id,
            'text' => $this->txt,
            'content' => $this->txt,
            'is_pinned' => (bool) $this->is_pinned,
            'is_best_answer' => (bool) $this->is_best_answer,
            'date' => $this->date,
            'created_at' => $this->date ? Carbon::createFromTimestamp($this->date)->diffForHumans() : '',
            'date_formatted' => $this->date ? Carbon::createFromTimestamp($this->date)->diffForHumans() : '',
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
