<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(): View
    {
        $slides = [
            [
                'id' => 'digital-platform',
                'category' => 'SOFTWARE & WEB',
                'title' => 'Service Request & Tracking Platform',
                'description' => 'A guided digital service platform that collects applications, manages documents, assigns staff and gives customers a clear status timeline.',
                'stack' => ['Laravel', 'PHP', 'MySQL', 'JavaScript'],
                'image' => 'images/work-digital-platform.svg',
            ],
            [
                'id' => 'business-ops',
                'category' => 'IT & TECHNOLOGY',
                'title' => 'Business Operations Dashboard',
                'description' => 'An operations workspace for assignments, reporting, workflow visibility and role-based team management.',
                'stack' => ['Laravel', 'Charts', 'RBAC'],
                'image' => 'images/work-operations.svg',
            ],
            [
                'id' => 'brand-identity',
                'category' => 'GRAPHICS & BRANDING',
                'title' => 'Brand Identity & Campaign Design',
                'description' => 'A complete visual identity system with social graphics, stationery and campaign-ready marketing assets.',
                'stack' => ['Brand Identity', 'Print', 'Social'],
                'image' => 'images/work-branding.svg',
            ],
            [
                'id' => 'digital-ecosystem',
                'category' => 'IT & TECHNOLOGY',
                'title' => 'Digital Business Ecosystem',
                'description' => 'A connected digital presence combining a business website, service catalogue, customer workflows and internal operations.',
                'stack' => ['Web', 'Cloud', 'Integrations'],
                'image' => 'images/work-ecosystem.svg',
            ],
        ];

        $projects = [
            [
                'category' => 'IT & TECHNOLOGY',
                'filter' => 'it',
                'title' => 'Business Operations Dashboard',
                'description' => 'Internal workspace for staff, assignments and reporting.',
                'image' => 'images/work-operations.svg',
                'meta' => ['Laravel', 'Role-based access'],
            ],
            [
                'category' => 'SOFTWARE & WEB',
                'filter' => 'software',
                'title' => 'Service Request Platform',
                'description' => 'Digital applications, documents and customer tracking.',
                'image' => 'images/work-digital-platform.svg',
                'meta' => ['Laravel', 'MySQL'],
            ],
            [
                'category' => 'SOFTWARE & WEB',
                'filter' => 'software',
                'title' => 'Business Website & Catalogue',
                'description' => 'Responsive company website with a structured service catalogue.',
                'image' => 'images/work-website.svg',
                'meta' => ['Blade', 'Vite', 'Responsive'],
            ],
            [
                'category' => 'GRAPHICS & BRANDING',
                'filter' => 'graphics',
                'title' => 'Brand Identity System',
                'description' => 'Identity, print assets and social media design toolkit.',
                'image' => 'images/work-branding.svg',
                'meta' => ['Branding', 'Print Design'],
            ],
            [
                'category' => 'GRAPHICS & BRANDING',
                'filter' => 'graphics',
                'title' => 'Campaign & Promotional Design',
                'description' => 'Campaign graphics built for digital and print publishing.',
                'image' => 'images/work-campaign.svg',
                'meta' => ['Social', 'Campaign'],
            ],
            [
                'category' => 'IT & TECHNOLOGY',
                'filter' => 'it',
                'title' => 'Digital Infrastructure Concept',
                'description' => 'Technology architecture and cloud-ready digital workflows.',
                'image' => 'images/work-ecosystem.svg',
                'meta' => ['Cloud', 'Systems'],
            ],
        ];

        $stats = [
            ['value' => '40+', 'label' => 'Projects & deliveries'],
            ['value' => '20+', 'label' => 'Business & creative engagements'],
            ['value' => '3', 'label' => 'Core disciplines'],
            ['value' => '24/7', 'label' => 'Digital support'],
        ];

        return view('work', compact('slides', 'projects', 'stats'));
    }
}
