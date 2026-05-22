<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ConversationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userId = Auth::id();
        $partner = $this->us_env == $userId ? $this->receiver : $this->sender;

        return [
            'id' => $this->id_msg, // Latest message ID
            'partner' => new UserResource($partner),
            'latest_message' => $this->text,
            'date' => $this->time,
            'date_formatted' => $this->time ? Carbon::createFromTimestamp($this->time)->diffForHumans() : '',
            'is_read' => $this->state == 1,
            'is_sent_by_me' => $this->us_env == $userId,
        ];
    }
}
