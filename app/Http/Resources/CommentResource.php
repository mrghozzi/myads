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
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'topic_id' => $this->tid, // tid usually represents the tp_id or topic id
            'text' => $this->txt,
            'date' => $this->date,
            'date_formatted' => $this->date ? Carbon::createFromTimestamp($this->date)->diffForHumans() : '',
        ];
    }
}
