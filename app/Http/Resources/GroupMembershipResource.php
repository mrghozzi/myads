<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GroupMembershipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'group_id' => $this->group_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'role' => $this->role,
            'status' => $this->status,
            'requested_at' => $this->requested_at?->diffForHumans(),
            'approved_at' => $this->approved_at?->diffForHumans(),
            'banned_at' => $this->banned_at?->diffForHumans(),
            'created_at' => $this->created_at?->diffForHumans(),
        ];
    }
}

