<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_category_id' => $this->service_category_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'formatted_price' => $this->formatted_price,
            'duration' => $this->duration,
            'duration_minutes' => $this->duration_minutes,
            'is_active' => $this->is_active,
            'is_free' => $this->is_free,
            'sort_order' => $this->sort_order,
            'category_id' => $this->whenLoaded('category', fn () => $this->category?->id),
            'category_name' => $this->whenLoaded('category', fn () => $this->category?->name),
            'category_slug' => $this->whenLoaded('category', fn () => $this->category?->slug),
            'fields' => ServiceFieldResource::collection($this->whenLoaded('fields')),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
