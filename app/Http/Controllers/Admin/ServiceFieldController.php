<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceField;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceFieldController extends Controller
{
    use AuthorizesRequests;

    public function index(Service $service): View|JsonResponse
    {
        $this->authorize('viewAny', ServiceField::class);

        $fields = $service->fields()->orderBy('sort_order')->get();

        if (! request()->expectsJson()) {
            return view('admin.fields.index', compact('service', 'fields'));
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'service' => $service->only(['id', 'name']),
                'fields' => $fields,
            ],
        ]);
    }

    public function store(Request $request, Service $service): JsonResponse
    {
        $this->authorize('create', ServiceField::class);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'field_key' => ['required', 'string', 'max:100', 'alpha_dash'],
            'field_type' => ['required', 'in:text,textarea,email,number,select,radio,file,date,checkbox'],
            'is_required' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'placeholder' => ['nullable', 'string', 'max:255'],
        ]);

        $field = $service->fields()->create([
            'label' => $validated['label'],
            'field_key' => $validated['field_key'],
            'field_type' => $validated['field_type'],
            'is_required' => $validated['is_required'] ?? false,
            'options' => $validated['options'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'placeholder' => $validated['placeholder'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Field created',
            'data' => $field,
        ], 201);
    }

    public function update(Request $request, ServiceField $field): JsonResponse
    {
        $this->authorize('update', $field);

        $validated = $request->validate([
            'label' => ['sometimes', 'string', 'max:255'],
            'field_key' => ['sometimes', 'string', 'max:100', 'alpha_dash'],
            'field_type' => ['sometimes', 'in:text,textarea,email,number,select,radio,file,date,checkbox'],
            'is_required' => ['nullable', 'boolean'],
            'options' => ['nullable', 'array'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'placeholder' => ['nullable', 'string', 'max:255'],
        ]);

        $field->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Field updated',
            'data' => $field->fresh(),
        ]);
    }

    public function destroy(ServiceField $field): JsonResponse
    {
        $this->authorize('delete', $field);

        $field->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Field deleted',
        ]);
    }
}
