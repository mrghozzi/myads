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
        $originalPrice = (int) ($this->o_order ?? 0);
        $isOnSale = (bool) $this->has_active_sale;
        $salePrice = ($isOnSale && $this->sale) ? (int) $this->sale->sale_price : null;
        $currentPrice = $isOnSale ? $salePrice : $originalPrice;

        return [
            'id' => $this->id,
            'title' => $this->name,
            'description' => $this->product_description,
            'price' => $originalPrice, // Base price (PTS)
            'original_price' => $originalPrice,
            'sale_price' => $salePrice,
            'current_price' => $currentPrice,
            'is_on_sale' => $isOnSale,
            'sales' => 0,
            'thumbnail' => $this->product_image,
            'seller' => new UserResource($this->whenLoaded('user')),
            'is_featured' => false,
            'category_id' => $this->product_category,
            'created_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            'date_formatted' => $this->updated_at ? $this->updated_at->diffForHumans() : '',
        ];
    }
}
