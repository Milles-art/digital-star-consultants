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
            ->orderBy('sort_order')
            ->get();

        $steps = [
            ['n' => '01', 'title' => 'Choose a service', 'desc' => 'Browse our services and select the one that matches your needs.'],
            ['n' => '02', 'title' => 'Send your request', 'desc' => 'Complete the application with your details and supporting documents.'],
            ['n' => '03', 'title' => 'We review it', 'desc' => 'Our team checks your request and contacts you if anything else is needed.'],
            ['n' => '04', 'title' => 'Get your result', 'desc' => 'Follow your application status and receive the outcome when it is ready.'],
        ];

        $popularServices = Service::query()
            ->with('category')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        return view('home', compact('categories', 'steps', 'popularServices'));
    }
}
