<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubmissionResource extends JsonResource
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
            'reference_number' => $this->reference_number,
            'service_id' => $this->service_id,
            'service_name' => $this->service?->name,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'customer_notes' => $this->customer_notes,
            'preferred_date' => $this->preferred_date?->format('Y-m-d'),
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'payment_status' => $this->payment_status,
            'total_price' => $this->total_price,
            'staff_notes' => $this->staff_notes,
            'processed_by_id' => $this->processed_by,
            'processed_by_name' => $this->processedBy?->name,
            'created_at' => $this->created_at->toIso8601String(),
            'completed_at' => $this->completed_at?->toIso8601String(),
        ];
    }
}
