<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference_number' => $this->reference_number,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'customer_notes' => $this->customer_notes,
            'preferred_date' => $this->preferred_date?->format('Y-m-d'),
            'total_price' => $this->total_price,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'staff_notes' => $this->staff_notes,
            'service_id' => $this->service_id,
            'service_name' => $this->whenLoaded('service', fn () => $this->service?->name),
            'processed_by' => $this->whenLoaded('processedBy', fn () => $this->processedBy ? [
                'id' => $this->processedBy->id,
                'name' => $this->processedBy->name,
            ] : null),
            'values' => $this->whenLoaded('values', function () {
                return $this->values->map(fn ($value) => [
                    'id' => $value->id,
                    'label' => $value->field->label ?? null,
                    'field_key' => $value->field->field_key ?? null,
                    'value' => $value->getValueForDisplay(),
                    'is_file' => $value->isFile(),
                    // No public file URL — see SubmissionFieldValue::getFileUrlAttribute.
                    // Staff download via Admin\SubmissionFileController::download.
                ]);
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i'),
        ];
    }
}
