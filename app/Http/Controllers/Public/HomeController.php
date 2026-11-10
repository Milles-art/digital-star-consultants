<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = ServiceCategory::query()
            ->active()
            ->topLevel()
            ->with([
                'children' => fn ($query) => $query
                    ->active()
                    ->with(['services' => fn ($query) => $query->active()->orderBy('sort_order')]),
                'services' => fn ($query) => $query->active()->orderBy('sort_order'),
            ])
            ->whereIn('slug', [
                'online-government-services',
                'business-services',
                'printing-graphics-design',
                'it-tech-consultancy',
            ])
            ->orderBy('sort_order')
            ->get();

        $steps = array_map(fn (array $step) => [
            'n' => $step['n'],
            'title' => $step['title'],
            'desc' => $step['copy'],
            'icon' => $step['icon'],
            'copy' => $step['copy'],
        ], __('site.home.proc_steps'));

        $popularServices = Service::query()
            ->with('category')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('home', compact('categories', 'steps', 'popularServices'));
    }
}
