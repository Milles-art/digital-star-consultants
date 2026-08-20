<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Service::class);

        $query = Service::with('category');

        if ($request->filled('category_id')) {
            $query->where('service_category_id', $request->category_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $services = $query->orderBy('sort_order')->get();

        return response()->json([
            'status' => 'success',
            'data' => ServiceResource::collection($services)
        ]);
    }

    public function store(StoreServiceRequest $request)
    {
        // Authorization already handled by StoreServiceRequest::authorize()
        $validated = $request->validated();

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;

        while (Service::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $service = Service::create([
            'service_category_id' => $validated['service_category_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
            'metadata' => $validated['metadata'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Service created successfully',
            'data' => new ServiceResource($service)
        ], 201);
    }

    public function show(Service $service)
    {
        $this->authorize('view', $service);

        $service->load(['category', 'fields']);

        return response()->json([
            'status' => 'success',
            'data' => new ServiceResource($service)
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        // Authorization already handled by UpdateServiceRequest::authorize()
        $validated = $request->validated();

        if (!empty($validated['name']) && $validated['name'] !== $service->name) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;

            while (Service::where('slug', $slug)->where('id', '!=', $service->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $validated['slug'] = $slug;
        }

        $service->update(array_filter($validated, fn ($value) => !is_null($value)));

        return response()->json([
            'status' => 'success',
            'message' => 'Service updated successfully',
            'data' => new ServiceResource($service)
        ]);
    }

    public function destroy(Service $service)
    {
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

    public function toggleActive(Service $service)
    {
        $this->authorize('update', $service);

        $service->is_active = !$service->is_active;
        $service->save();

        $status = $service->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'status' => 'success',
            'message' => "Service {$status} successfully",
            'data' => new ServiceResource($service)
        ]);
    }
}
