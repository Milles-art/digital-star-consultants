<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Service::class);

        $query = Service::with(['category']);

        if ($request->filled('category_id')) {
            $query->where('service_category_id', $request->category_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $services = $query->orderBy('sort_order')->get();

        return response()->json([
            'status' => 'success',
            'data' => $services
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Service::class);

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
            'data' => $service
        ], 201);
    }

    public function show($id)
    {
        $service = Service::with(['category', 'fields'])->find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $this->authorize('view', $service);

        return response()->json([
            'status' => 'success',
            'data' => $service
        ]);
    }

    public function update(Request $request, $id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $this->authorize('update', $service);

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
            'data' => $service
        ]);
    }

    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $this->authorize('delete', $service);

        if ($service->submissions()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete service with submissions. Archive instead.'
            ], 422);
        }

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

    public function toggleActive($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found'
            ], 404);
        }

        $this->authorize('update', $service);

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
