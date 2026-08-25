<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceFieldResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'label' => $this->label,
            'field_key' => $this->field_key,
            'field_type' => $this->field_type,
            'type_label' => $this->type_label,
            'options' => $this->options,
            'placeholder' => $this->placeholder,
            'help_text' => $this->help_text,
            'default_value' => $this->default_value,
            'is_required' => $this->is_required,
            'sort_order' => $this->sort_order,
        ];
    }
}
