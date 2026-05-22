<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_msg,
            'sender' => new UserResource($this->whenLoaded('sender')),
            'receiver' => new UserResource($this->whenLoaded('receiver')),
            'text' => $this->text, // Uses getTextAttribute to auto-decrypt
            'date' => $this->time,
            'date_formatted' => $this->time ? Carbon::createFromTimestamp($this->time)->diffForHumans() : '',
            'is_read' => $this->state == 1,
            // 'attachment_path' => $this->attachment_path, // If attachments are needed later
            // 'attachment_name' => $this->attachment_name,
        ];
    }
}
