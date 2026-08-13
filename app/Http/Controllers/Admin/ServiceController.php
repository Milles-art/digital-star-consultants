<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * List all services
     * GET /admin/services
     */
    public function index(Request $request)
    {
        $query = Service::with(['category']);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('service_category_id', $request->category_id);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $services = $query->orderBy('sort_order')->get();

        return response()->json([
            'status' => 'success',
            'data' => $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'slug' => $service->slug,
                    'description' => $service->description,
                    'price' => $service->price,
                    'formatted_price' => $service->formatted_price,
                    'duration_minutes' => $service->duration_minutes,
                    'duration' => $service->duration,
                    'sort_order' => $service->sort_order,
                    'is_active' => $service->is_active,
                    'category_id' => $service->service_category_id,
                    'category_name' => $service->category->name ?? null,
                    'fields_count' => $service->fields()->count(),
                    'submissions_count' => $service->submissions()->count(),
                    'created_at' => $service->created_at->format('Y-m-d H:i'),
                ];
            })
        ]);
    }

    /**
     * Create a new service
     * POST /admin/services
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_category_id' => 'required|exists:service_categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'metadata' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Check if slug exists
        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;

        while (Service::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $service = Service::create([
            'service_category_id' => $request->service_category_id,
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'price' => $request->price,
            'duration_minutes' => $request->duration_minutes,
            'metadata' => $request->metadata,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Service created successfully',
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'price' => $service->price,
                'formatted_price' => $service->formatted_price,
                'duration_minutes' => $service->duration_minutes,
                'duration' => $service->duration,
                'sort_order' => $service->sort_order,
                'is_active' => $service->is_active,
                'category_id' => $service->service_category_id,
                'created_at' => $service->created_at->format('Y-m-d H:i'),
            ]
        ], 201);
    }

    /**
     * Show a single service
     * GET /admin/services/{id}
     */
    public function show($id)
    {
        $service = Service::with(['category', 'fields'])
            ->find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'price' => $service->price,
                'formatted_price' => $service->formatted_price,
                'duration_minutes' => $service->duration_minutes,
                'duration' => $service->duration,
                'is_free' => $service->is_free,
                'metadata' => $service->metadata,
                'sort_order' => $service->sort_order,
                'is_active' => $service->is_active,
                'category_id' => $service->service_category_id,
                'category_name' => $service->category->name ?? null,
                'fields' => $service->fields->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'label' => $field->label,
                        'field_key' => $field->field_key,
                        'field_type' => $field->field_type,
                        'type_label' => $field->type_label,
                        'is_required' => $field->is_required,
                        'sort_order' => $field->sort_order,
                    ];
                }),
                'submissions_count' => $service->submissions()->count(),
                'created_at' => $service->created_at->format('Y-m-d H:i'),
                'updated_at' => $service->updated_at->format('Y-m-d H:i'),
            ]
        ]);
    }

    /**
     * Update a service
     * PUT /admin/services/{id}
     */
    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $request->validate([
            'service_category_id' => 'nullable|exists:service_categories,id',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'duration_minutes' => 'nullable|integer|min:1',
            'metadata' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'service_category_id',
            'name',
            'description',
            'price',
            'duration_minutes',
            'metadata',
            'sort_order',
            'is_active'
        ]);

        // If name is updated, update slug too
        if ($request->has('name') && $request->name !== $service->name) {
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $counter = 1;

            while (Service::where('slug', $slug)->where('id', '!=', $service->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $data['slug'] = $slug;
        }

        $service->update(array_filter($data, function ($value) {
            return !is_null($value);
        }));

        return response()->json([
            'status' => 'success',
            'message' => 'Service updated successfully',
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'price' => $service->price,
                'formatted_price' => $service->formatted_price,
                'duration_minutes' => $service->duration_minutes,
                'duration' => $service->duration,
                'sort_order' => $service->sort_order,
                'is_active' => $service->is_active,
                'category_id' => $service->service_category_id,
                'updated_at' => $service->updated_at->format('Y-m-d H:i'),
            ]
        ]);
    }

    /**
     * Delete a service
     * DELETE /admin/services/{id}
     */
    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        // Check if service has submissions
        if ($service->submissions()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete service with submissions. Archive instead.'
            ], 422);
        }

        // Check if service has fields
        if ($service->fields()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete service with fields. Delete fields first.'
            ], 422);
        }

        $service->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Service deleted successfully'
        ]);
    }

    /**
     * Toggle service active status
     * POST /admin/services/{id}/toggle-active
     */
    public function toggleActive($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $service->is_active = !$service->is_active;
        $service->save();

        $status = $service->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'status' => 'success',
            'message' => "Service {$status} successfully",
            'data' => [
                'id' => $service->id,
                'name' => $service->name,
                'is_active' => $service->is_active,
            ]
        ]);
    }
}