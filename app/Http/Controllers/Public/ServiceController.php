<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * List all active services
     * GET /services
     */
    public function index(Request $request)
    {
        $query = Service::with(['category'])
            ->where('is_active', true);

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $services = $query->orderBy('sort_order')->get();

        // Get all active categories for filter
        $categories = ServiceCategory::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => $categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'icon' => $category->icon,
                        'children' => $category->children->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'name' => $child->name,
                                'slug' => $child->slug,
                                'icon' => $child->icon,
                            ];
                        }),
                    ];
                }),
                'services' => $services->map(function ($service) {
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'slug' => $service->slug,
                        'description' => $service->description,
                        'price' => $service->price,
                        'formatted_price' => $service->formatted_price,
                        'duration' => $service->duration,
                        'category_name' => $service->category->name ?? null,
                        'category_slug' => $service->category->slug ?? null,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Show a single service with its fields
     * GET /services/{slug}
     */
    public function show($slug)
    {
        $service = Service::with(['category', 'fields'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

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
                'duration' => $service->duration,
                'category' => [
                    'id' => $service->category->id ?? null,
                    'name' => $service->category->name ?? null,
                    'slug' => $service->category->slug ?? null,
                ],
                'fields' => $service->fields->map(function ($field) {
                    return [
                        'id' => $field->id,
                        'label' => $field->label,
                        'field_key' => $field->field_key,
                        'field_type' => $field->field_type,
                        'type_label' => $field->type_label,
                        'options' => $field->options,
                        'placeholder' => $field->placeholder,
                        'help_text' => $field->help_text,
                        'default_value' => $field->default_value,
                        'is_required' => $field->is_required,
                        'sort_order' => $field->sort_order,
                    ];
                }),
            ]
        ]);
    }
}
