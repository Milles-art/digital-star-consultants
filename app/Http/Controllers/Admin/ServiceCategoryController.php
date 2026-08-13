<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
{
    /**
     * List all categories
     * GET /admin/categories
     */
    public function index()
    {
        $categories = ServiceCategory::with(['parent', 'children'])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'icon' => $category->icon,
                    'color' => $category->color,
                    'sort_order' => $category->sort_order,
                    'is_active' => $category->is_active,
                    'parent_id' => $category->parent_id,
                    'parent_name' => $category->parent->name ?? null,
                    'children_count' => $category->children->count(),
                    'created_at' => $category->created_at->format('Y-m-d H:i'),
                ];
            })
        ]);
    }

    /**
     * Create a new category
     * POST /admin/categories
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:service_categories,id',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $category = ServiceCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'parent_id' => $request->parent_id,
            'icon' => $request->icon,
            'color' => $request->color,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'parent_id' => $category->parent_id,
                'icon' => $category->icon,
                'color' => $category->color,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
                'created_at' => $category->created_at->format('Y-m-d H:i'),
            ]
        ], 201);
    }

    /**
     * Show a single category
     * GET /admin/categories/{id}
     */
    public function show($id)
    {
        $category = ServiceCategory::with(['parent', 'children', 'services'])
            ->find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'icon' => $category->icon,
                'color' => $category->color,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
                'parent_id' => $category->parent_id,
                'parent_name' => $category->parent->name ?? null,
                'children' => $category->children->map(function ($child) {
                    return [
                        'id' => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                        'is_active' => $child->is_active,
                    ];
                }),
                'services_count' => $category->services->count(),
                'created_at' => $category->created_at->format('Y-m-d H:i'),
                'updated_at' => $category->updated_at->format('Y-m-d H:i'),
            ]
        ]);
    }

    /**
     * Update a category
     * PUT /admin/categories/{id}
     */
    public function update(Request $request, $id)
    {
        $category = ServiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:service_categories,id',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:7',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'description', 'parent_id', 'icon', 'color', 'sort_order', 'is_active']);

        // If name is updated, update slug too
        if ($request->has('name') && $request->name !== $category->name) {
            $data['slug'] = Str::slug($request->name);
        }

        $category->update(array_filter($data, function ($value) {
            return !is_null($value);
        }));

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
                'parent_id' => $category->parent_id,
                'icon' => $category->icon,
                'color' => $category->color,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
                'updated_at' => $category->updated_at->format('Y-m-d H:i'),
            ]
        ]);
    }

    /**
     * Delete a category
     * DELETE /admin/categories/{id}
     */
    public function destroy($id)
    {
        $category = ServiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot delete category with children. Delete children first.'
            ], 422);
        }

        // Check if category has services
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

    /**
     * Toggle category active status
     * POST /admin/categories/{id}/toggle-active
     */
    public function toggleActive($id)
    {
        $category = ServiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        $category->is_active = !$category->is_active;
        $category->save();

        $status = $category->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'status' => 'success',
            'message' => "Category {$status} successfully",
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'is_active' => $category->is_active,
            ]
        ]);
    }
}