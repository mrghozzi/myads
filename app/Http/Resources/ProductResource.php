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
            'description' => $this->product_description,
            'price' => $this->product_price, // Points (PTS)
            'sales' => 0, // Not available easily
            'thumbnail' => $this->product_image,
            'seller' => new UserResource($this->whenLoaded('user')),
            'is_featured' => false,
            'category_id' => $this->product_category,
            'created_at' => null,
            'date_formatted' => '',
        ];
    }
}
