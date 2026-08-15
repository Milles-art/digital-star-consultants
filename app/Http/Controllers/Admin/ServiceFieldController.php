<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceFieldController extends Controller
{
    public function index($serviceId)
    {
        $service = Service::find($serviceId);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $this->authorize('viewAny', ServiceField::class);

        $fields = $service->fields()->orderBy('sort_order')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'service' => [
                    'id' => $service->id,
                    'name' => $service->name,
                ],
                'fields' => $fields
            ]
        ]);
    }

    public function store(Request $request, $serviceId)
    {
        $service = Service::find($serviceId);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $this->authorize('create', ServiceField::class);

        $request->validate([
            'label' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,textarea,number,email,tel,date,time,datetime,select,checkbox,radio,file,hidden,password',
            'options' => 'nullable|array',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'default_value' => 'nullable|string|max:255',
            'is_required' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $fieldKey = Str::slug($request->label, '_');
        $originalKey = $fieldKey;
        $counter = 1;

        while (ServiceField::where('service_id', $serviceId)->where('field_key', $fieldKey)->exists()) {
            $fieldKey = $originalKey . '_' . $counter;
            $counter++;
        }

        $field = ServiceField::create([
            'service_id' => $serviceId,
            'label' => $request->label,
            'field_key' => $fieldKey,
            'field_type' => $request->field_type,
            'options' => $request->options,
            'placeholder' => $request->placeholder,
            'help_text' => $request->help_text,
            'default_value' => $request->default_value,
            'is_required' => $request->is_required ?? true,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Field created successfully',
            'data' => $field
        ], 201);
    }

    public function show($id)
    {
        $field = ServiceField::with('service')->find($id);

        if (!$field) {
            return response()->json([
                'status' => 'error',
                'message' => 'Field not found'
            ], 404);
        }

        $this->authorize('view', $field);

        return response()->json([
            'status' => 'success',
            'data' => $field
        ]);
    }

    public function update(Request $request, $id)
    {
        $field = ServiceField::find($id);

        if (!$field) {
            return response()->json([
                'status' => 'error',
                'message' => 'Field not found'
            ], 404);
        }

        $this->authorize('update', $field);

        $request->validate([
            'label' => 'nullable|string|max:255',
            'field_type' => 'nullable|string|in:text,textarea,number,email,tel,date,time,datetime,select,checkbox,radio,file,hidden,password',
            'options' => 'nullable|array',
            'placeholder' => 'nullable|string|max:255',
            'help_text' => 'nullable|string',
            'default_value' => 'nullable|string|max:255',
            'is_required' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = $request->only([
            'label',
            'field_type',
            'options',
            'placeholder',
            'help_text',
            'default_value',
            'is_required',
            'sort_order'
        ]);

        if ($request->has('label') && $request->label !== $field->label) {
            $fieldKey = Str::slug($request->label, '_');
            $originalKey = $fieldKey;
            $counter = 1;

            while (ServiceField::where('service_id', $field->service_id)
                ->where('field_key', $fieldKey)
                ->where('id', '!=', $field->id)
                ->exists()) {
                $fieldKey = $originalKey . '_' . $counter;
                $counter++;
            }

            $data['field_key'] = $fieldKey;
        }

        $field->update(array_filter($data, function ($value) {
            return !is_null($value);
        }));

        return response()->json([
            'status' => 'success',
            'message' => 'Field updated successfully',
            'data' => $field
        ]);
    }

    public function destroy($id)
    {
        $field = ServiceField::find($id);

        if (!$field) {
            return response()->json([
                'status' => 'error',
                'message' => 'Field not found'
            ], 404);
        }

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
