<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'parent_id' => $this->parent_id,
            'parent_name' => $this->whenLoaded('parent', fn () => $this->parent?->name),
            'children_count' => $this->whenCounted('children'),
            'children' => ServiceCategoryResource::collection($this->whenLoaded('children')),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
