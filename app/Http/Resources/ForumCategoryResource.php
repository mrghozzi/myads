<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForumCategoryResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->des,
            'icon' => $this->icon,
            'topics_count' => $this->topics_count ?? 0, // Assuming loaded with count
            'visibility' => $this->visibility,
        ];
    }
}
