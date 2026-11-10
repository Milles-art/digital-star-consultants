<?php

namespace App\Support;

use App\Models\Service;
use Illuminate\Support\Collection;

final class ServiceRequirementReview
{
    public static function applies(array $conditions, array $answers): bool
    {
        foreach ($conditions as $key => $allowed) {
            if (! in_array($answers[$key] ?? null, $allowed, true)) {
                return false;
            }
        }

        return true;
    }

    /** Missing preparation items for staff follow-up, never public validation. */
    public static function missing(Service $service, array $answers, array $files = []): Collection
    {
        $rules = $service->metadata['government_requirements']['fields'] ?? [];

        return $service->fields->filter(function ($field) use ($rules, $answers, $files) {
            $rule = $rules[$field->field_key] ?? [];
            $needed = $rule['review_required'] ?? $field->is_required;
            if (! $needed || ! self::applies($rule['applies_when'] ?? [], $answers)) {
                return false;
            }
            $value = $field->isFileField() ? ($files[$field->field_key] ?? null) : ($answers[$field->field_key] ?? null);

            return blank($value) || (is_string($value) && (str_contains(mb_strtolower($value), 'sijui') || str_contains(mb_strtolower($value), 'nahitaji ushauri')));
        })->values();
    }
}
