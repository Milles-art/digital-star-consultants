<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Service catalogue: Pillar -> Group -> Specific service.
     * A customer only sees the next useful level instead of a flat list.
     */
    public function index(Request $request): JsonResponse|View
    {
        $selectedSlug = $request->string('category')->toString();
        $search = $request->string('search')->trim()->toString();

        $categories = ServiceCategory::query()
            ->active()
            ->topLevel()
            ->with([
                'children' => fn ($query) => $query
                    ->active()
                    ->withCount(['services as active_services_count' => fn ($q) => $q->where('is_active', true)])
                    ->with(['services' => fn ($serviceQuery) => $serviceQuery
                        ->where('is_active', true)
                        ->orderBy('sort_order'),
                    ]),
            ])
            ->withCount(['services as active_services_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')
            ->get();

        $selectedCategory = null;
        $childCategories = collect();
        $services = collect();
        $serviceGroups = collect();

        if ($selectedSlug !== '') {
            $selectedCategory = ServiceCategory::query()
                ->active()
                ->with('parent')
                ->where('slug', $selectedSlug)
                ->first();

            if ($selectedCategory) {
                if ($selectedCategory->isTopLevel()) {
                    $childCategories = $selectedCategory->children()
                        ->active()
                        ->withCount(['services as active_services_count' => fn ($q) => $q->where('is_active', true)])
                        ->get();

                    $services = $selectedCategory->services()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get();
                } else {
                    $services = $selectedCategory->services()
                        ->where('is_active', true)
                        ->orderBy('sort_order')
                        ->get();
                }
            }
        }

        // Search is intentionally global: the user can type "TIN" or "passport"
        // without first knowing which group contains the service.
        if ($search !== '') {
            $services = Service::query()
                ->with(['category.parent'])
                ->where('is_active', true)
                ->where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                })
                ->orderBy('sort_order')
                ->get();

            $serviceGroups = $services
                ->groupBy(fn (Service $service): string => $service->category?->full_path ?? 'Other services')
                ->map(fn ($group, string $name): array => [
                    'name' => $name,
                    'description' => $group->first()?->category?->description ?? 'Professional assistance from application to completion.',
                    'services' => $group->values(),
                ])
                ->values();
        }

        if (! $request->expectsJson()) {
            return view('services.index', compact(
                'categories',
                'selectedCategory',
                'childCategories',
                'services',
                'serviceGroups',
                'search',
            ));
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'categories' => $categories->map(fn ($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'icon' => $category->icon,
                    'children' => $category->children->map(fn ($child) => [
                        'id' => $child->id,
                        'name' => $child->name,
                        'slug' => $child->slug,
                        'icon' => $child->icon,
                        'services_count' => $child->active_services_count,
                    ]),
                    'services_count' => $category->active_services_count,
                ]),
                'selected_category' => $selectedCategory ? [
                    'id' => $selectedCategory->id,
                    'name' => $selectedCategory->name,
                    'slug' => $selectedCategory->slug,
                    'parent' => $selectedCategory->parent?->name,
                ] : null,
                'services' => $services->map(fn ($service) => [
                    'id' => $service->id,
                    'name' => $service->name,
                    'slug' => $service->slug,
                    'description' => $service->description,
                    'price' => $service->price,
                    'formatted_price' => $service->formatted_price,
                    'duration' => $service->duration,
                    'category_name' => $service->category?->name,
                    'category_slug' => $service->category?->slug,
                ]),
            ],
        ]);
    }

    /**
     * Show a single service and its dynamic application fields.
     */
    public function show(Request $request, string $slug): JsonResponse|View
    {
        $service = Service::with(['category.parent', 'fields'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $service) {
            if (! $request->expectsJson()) {
                abort(404);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Service not found',
            ], 404);
        }

        if (! $request->expectsJson()) {
            return view('services.show', compact('service'));
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
                    'id' => $service->category?->id,
                    'name' => $service->category?->name,
                    'slug' => $service->category?->slug,
                    'parent_name' => $service->category?->parent?->name,
                ],
                'fields' => $service->fields->map(fn ($field) => [
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
                ]),
            ],
        ]);
    }
}
