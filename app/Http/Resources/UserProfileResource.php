<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Like;

class UserProfileResource extends JsonResource
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
            'username' => $this->username,
            'name' => $this->name,
            'avatar' => $this->avatarUrl(),
            'pts' => $this->pts,
            'verified' => $this->hasVerifiedBadge(),
            'bio' => $this->sig,
            'followers_count' => Like::where('sid', $this->id)->where('type', 1)->count(),
            'following_count' => Like::where('uid', $this->id)->where('type', 1)->count(),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'online' => $this->isOnline(),
        ];
    }
}
