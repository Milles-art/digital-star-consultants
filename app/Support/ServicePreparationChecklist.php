<?php

namespace App\Support;

use App\Models\Service;
use Illuminate\Support\Collection;

/** Customer-facing preparation list, independent of optional intake validation. */
final class ServicePreparationChecklist
{
    public static function documents(Service $service): Collection
    {
        $rules = $service->metadata['government_requirements']['fields'] ?? [];

        return $service->fields->where('field_type', 'file')->map(function ($field) use ($service, $rules) {
            if (($rules[$field->field_key]['preparation_visible'] ?? true) === false) return null;
            $item = [
                'key' => $field->field_key, 'label' => $field->label, 'help' => $field->help_text,
                'conditions' => $rules[$field->field_key]['applies_when'] ?? [],
            ];
            if ($service->slug === 'nida-registration-correction') {
                if ($field->field_key === 'upload_father_birth_certificate'
                    && $service->fields->contains('field_key', 'upload_parent_birth_certificate')) {
                    return null; // One-parent evidence already includes the father.
                }
                $labels = __('requirements.nida_documents');
                if (isset($labels[$field->field_key])) {
                    $item['label'] = $labels[$field->field_key];
                }
                if ($field->field_key === 'upload_supporting_docs') {
                    $item['help'] = null;
                    $item['conditions'] = [];
                }
            } elseif (in_array($field->field_key, ['upload_supporting_docs', 'upload_documents'], true)) {
                // A generic extra-upload slot is not a specific prerequisite.
                return null;
            }

            return $item;
        })->filter()->values();
    }
}
