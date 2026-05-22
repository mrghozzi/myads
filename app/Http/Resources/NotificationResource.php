<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class NotificationResource extends JsonResource
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
            'title' => $this->name,
            'url' => $this->nurl,
            'logo' => $this->logo ? asset($this->logo) : null,
            'time' => $this->time,
            'date_formatted' => $this->time ? Carbon::createFromTimestamp($this->time)->diffForHumans() : '',
            'is_read' => $this->state == 1,
        ];
    }
}
