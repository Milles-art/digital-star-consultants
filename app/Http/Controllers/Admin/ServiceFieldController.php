<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceFieldRequest;
use App\Http\Requests\Admin\UpdateServiceFieldRequest;
use App\Http\Resources\ServiceFieldResource;
use App\Models\Service;
use App\Models\ServiceField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceFieldController extends Controller
{
    public function index(Service $service)
    {
        $this->authorize('viewAny', ServiceField::class);

        $fields = $service->fields()->orderBy('sort_order')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'service' => [
                    'id' => $service->id,
                    'name' => $service->name,
                ],
                'fields' => ServiceFieldResource::collection($fields)
            ]
        ]);
    }

    public function store(StoreServiceFieldRequest $request, Service $service)
    {
        // Authorization already handled by StoreServiceFieldRequest::authorize()
        $validated = $request->validated();

        $fieldKey = Str::slug($validated['label'], '_');
        $originalKey = $fieldKey;
        $counter = 1;

        while ($service->fields()->where('field_key', $fieldKey)->exists()) {
            $fieldKey = $originalKey . '_' . $counter;
            $counter++;
        }

        $field = $service->fields()->create([
            'label' => $validated['label'],
            'field_key' => $fieldKey,
            'field_type' => $validated['field_type'],
            'options' => $validated['options'] ?? null,
            'placeholder' => $validated['placeholder'] ?? null,
            'help_text' => $validated['help_text'] ?? null,
            'default_value' => $validated['default_value'] ?? null,
            'is_required' => $validated['is_required'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Field created successfully',
            'data' => new ServiceFieldResource($field)
        ], 201);
    }

    public function show(ServiceField $field)
    {
        $this->authorize('view', $field);

        $field->load('service');

        return response()->json([
            'status' => 'success',
            'data' => new ServiceFieldResource($field)
        ]);
    }

    public function update(UpdateServiceFieldRequest $request, ServiceField $field)
    {
        // Authorization already handled by UpdateServiceFieldRequest::authorize()
        $validated = $request->validated();

        if (!empty($validated['label']) && $validated['label'] !== $field->label) {
            $fieldKey = Str::slug($validated['label'], '_');
            $originalKey = $fieldKey;
            $counter = 1;

            while (ServiceField::where('service_id', $field->service_id)
                ->where('field_key', $fieldKey)
                ->where('id', '!=', $field->id)
                ->exists()) {
                $fieldKey = $originalKey . '_' . $counter;
                $counter++;
            }

            $validated['field_key'] = $fieldKey;
        }

        $field->update(array_filter($validated, fn ($value) => !is_null($value)));

        return response()->json([
            'status' => 'success',
            'message' => 'Field updated successfully',
            'data' => new ServiceFieldResource($field)
        ]);
    }

    public function destroy(ServiceField $field)
    {
        $this->authorize('delete', $field);

        if ($field->values()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete field with existing values. Archive instead.'
            ], 422);
        }

        $field->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Field deleted successfully'
        ]);
    }

    public function reorder(Request $request)
    {
        $this->authorize('update', ServiceField::class);

        $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:service_fields,id',
            'fields.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($request->fields as $fieldData) {
            ServiceField::where('id', $fieldData['id'])
                ->update(['sort_order' => $fieldData['sort_order']]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Fields reordered successfully'
        ]);
    }
}
