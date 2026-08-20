<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * List all active services
     * GET /services
     */
    public function index(Request $request): JsonResponse|View
    {
        $selectedCategory = $request->string('category')->toString();
        $query = Service::with(['category'])
            ->where('is_active', true);

        // Filter by category
        if ($selectedCategory !== '') {
            $query->whereHas('category', function ($q) use ($selectedCategory) {
                $q->where('slug', $selectedCategory)
                    ->orWhereHas('parent', function ($parentQuery) use ($selectedCategory) {
                        $parentQuery->where('slug', $selectedCategory);
                    });
            });
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $services = $query->orderBy('sort_order')->get();

        $serviceGroups = $services
            ->groupBy(fn (Service $service): string => $this->issueGroupFor($service))
            ->map(fn ($group, string $name): array => [
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $this->issueGroupDescription($name),
                'services' => $group,
            ])
            ->values();

        // Get all active categories for filter
        $categories = ServiceCategory::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->where('is_active', true);
            }])
            ->get();

        if (! $request->expectsJson()) {
            return view('services.index', [
                'categories' => $categories,
                'selectedCategory' => $selectedCategory,
                'services' => $services,
                'serviceGroups' => $serviceGroups,
                'search' => $request->string('search')->toString(),
            ]);
        }

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
            ],
        ]);
    }

    private function issueGroupFor(Service $service): string
    {
        $searchText = strtolower($service->name.' '.($service->category->name ?? ''));

        return match (true) {
            str_contains($searchText, 'visa') || str_contains($searchText, 'passport') || str_contains($searchText, 'residence') || str_contains($searchText, 'travel') || str_contains($searchText, 'flight') => 'Visa & travel issues',
            str_contains($searchText, 'police') || str_contains($searchText, 'clearance') || str_contains($searchText, 'conduct') || str_contains($searchText, 'loss report') => 'Police & legal issues',
            str_contains($searchText, 'business') || str_contains($searchText, 'brela') || str_contains($searchText, 'company') || str_contains($searchText, 'ngo') || str_contains($searchText, 'tax') || str_contains($searchText, 'tin registration') => 'Business & tax issues',
            str_contains($searchText, 'job') || str_contains($searchText, 'school') || str_contains($searchText, 'scholarship') || str_contains($searchText, 'exam') => 'Education & work issues',
            str_contains($searchText, 'print') || str_contains($searchText, 'stationery') || str_contains($searchText, 'design') => 'Printing & design issues',
            str_contains($searchText, 'website') || str_contains($searchText, 'mobile') || str_contains($searchText, 'it ') || str_contains($searchText, 'technology') => 'Technology & digital issues',
            default => 'Government & identity issues',
        };
    }

    private function issueGroupDescription(string $name): string
    {
        return match ($name) {
            'Visa & travel issues' => 'Support with travel documents, visas, passports, and immigration requests.',
            'Police & legal issues' => 'Help with police clearance, good conduct, and loss reports.',
            'Business & tax issues' => 'Move company registration, tax, and business paperwork forward.',
            'Education & work issues' => 'Get support with jobs, school, scholarships, and examinations.',
            'Printing & design issues' => 'Print, prepare, and present the documents your work needs.',
            'Technology & digital issues' => 'Build, improve, or plan the digital tools behind your work.',
            default => 'Navigate identity documents and essential government services.',
        };
    }

    /**
     * Show a single service with its fields
     * GET /services/{slug}
     */
    public function show(Request $request, string $slug): JsonResponse|View
    {
        $service = Service::with(['category', 'fields'])
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
            ],
        ]);
    }
}
