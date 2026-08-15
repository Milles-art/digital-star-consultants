<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceCategoryController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', ServiceCategory::class);

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

    public function store(Request $request)
    {
        $this->authorize('create', ServiceCategory::class);

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
            'data' => $category
        ], 201);
    }

    public function show($id)
    {
        $category = ServiceCategory::with(['parent', 'children', 'services'])->find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        $this->authorize('view', $category);

        return response()->json([
            'status' => 'success',
            'data' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = ServiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        $this->authorize('update', $category);

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

        if ($request->has('name') && $request->name !== $category->name) {
            $data['slug'] = Str::slug($request->name);
        }

        $category->update(array_filter($data, function ($value) {
            return !is_null($value);
        }));

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy($id)
    {
        $category = ServiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

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

    public function toggleActive($id)
    {
        $category = ServiceCategory::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        }

        $this->authorize('update', $category);

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
