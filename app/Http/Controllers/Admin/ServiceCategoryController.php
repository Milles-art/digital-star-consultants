<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceCategoryRequest;
use App\Http\Requests\Admin\UpdateServiceCategoryRequest;
use App\Http\Resources\ServiceCategoryResource;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceCategoryController extends Controller
{
    public function index(): View|JsonResponse
    {
        $this->authorize('viewAny', ServiceCategory::class);

        $categories = ServiceCategory::with('parent')
            ->withCount('children')
            ->orderBy('sort_order')
            ->get();

        if (! request()->expectsJson()) {
            return view('admin.categories.index', [
                'categories' => $categories,
                'parentCategories' => ServiceCategory::topLevel()->orderBy('name')->get(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => ServiceCategoryResource::collection($categories),
        ]);
    }

    public function store(StoreServiceCategoryRequest $request)
    {
        // Authorization already handled by StoreServiceCategoryRequest::authorize()
        $validated = $request->validated();

        $category = ServiceCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'color' => $validated['color'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'data' => new ServiceCategoryResource($category)
        ], 201);
    }

    public function show(ServiceCategory $category)
    {
        $this->authorize('view', $category);

        $category->load(['parent', 'children', 'services']);

        return response()->json([
            'status' => 'success',
            'data' => new ServiceCategoryResource($category)
        ]);
    }

    public function update(UpdateServiceCategoryRequest $request, ServiceCategory $category)
    {
        // Authorization already handled by UpdateServiceCategoryRequest::authorize()
        $validated = $request->validated();

        if (!empty($validated['name']) && $validated['name'] !== $category->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category->update(array_filter($validated, fn ($value) => !is_null($value)));

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'data' => new ServiceCategoryResource($category)
        ]);
    }

    public function destroy(ServiceCategory $category)
    {
        $this->authorize('delete', $category);

        if ($category->children()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete category with children. Delete children first.'
            ], 422);
        }

        if ($category->services()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete category with services. Delete services first.'
            ], 422);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully'
        ]);
    }

    public function toggleActive(ServiceCategory $category)
    {
        $this->authorize('update', $category);

        $category->is_active = !$category->is_active;
        $category->save();

        $status = $category->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'status' => 'success',
            'message' => "Category {$status} successfully",
            'data' => new ServiceCategoryResource($category)
        ]);
    }
}
