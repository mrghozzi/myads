<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ProductResource extends JsonResource
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
            'description' => $this->des,
            'price' => $this->prix, // Points (PTS)
            'sales' => $this->vente,
            'thumbnail' => $this->img ? asset('upload/' . $this->img) : null,
            'seller' => new UserResource($this->whenLoaded('seller')),
            'is_featured' => $this->ep == 1,
            'category_id' => $this->cat,
            'created_at' => $this->date,
            'date_formatted' => $this->date ? Carbon::createFromTimestamp($this->date)->diffForHumans() : '',
        ];
    }
}
