<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Four clean customer-facing service pillars.
        $pillars = [
            [
                'slug' => 'online-government-services',
                'name' => 'Government & Online Services',
                'description' => 'Government applications, identification, immigration, education, jobs and online assistance.',
                'sort_order' => 1,
                'icon' => '🏛',
                'color' => '#1657D9',
            ],
            [
                'slug' => 'business-services',
                'name' => 'Business Services',
                'description' => 'Business registration, company setup, compliance and related business support.',
                'sort_order' => 2,
                'icon' => '▣',
                'color' => '#1657D9',
            ],
            [
                'slug' => 'printing-graphics-design',
                'name' => 'Printing, Branding & Stationery',
                'description' => 'Printing, graphic design, branding and everyday stationery solutions.',
                'sort_order' => 3,
                'icon' => '✦',
                'color' => '#F6C945',
            ],
            [
                'slug' => 'it-tech-consultancy',
                'name' => 'IT & Technology',
                'description' => 'Websites, software systems, hosting and practical technology consulting.',
                'sort_order' => 4,
                'icon' => '⌘',
                'color' => '#1657D9',
            ],
        ];

        foreach ($pillars as $pillar) {
            ServiceCategory::updateOrCreate(
                ['slug' => $pillar['slug']],
                $pillar + ['is_active' => true, 'parent_id' => null],
            );
        }

        $onlineGov = ServiceCategory::where('slug', 'online-government-services')->firstOrFail();
        $business = ServiceCategory::where('slug', 'business-services')->firstOrFail();

        $onlineGroups = [
            ['slug' => 'serikali-identification', 'name' => 'Government & Identification', 'sort_order' => 1, 'icon' => 'ID', 'description' => 'NIDA, civil registration, police clearance, driving and other identification-related services.'],
            ['slug' => 'passport-immigration', 'name' => 'Passport & Immigration', 'sort_order' => 2, 'icon' => 'PAS', 'description' => 'Passport, visa and immigration services in one place.'],
            ['slug' => 'jobs', 'name' => 'Jobs & Careers', 'sort_order' => 3, 'icon' => 'JOB', 'description' => 'Job applications and job search support.'],
            ['slug' => 'education', 'name' => 'Education', 'sort_order' => 4, 'icon' => 'EDU', 'description' => 'School, scholarship and examination application assistance.'],
            ['slug' => 'tra', 'name' => 'TRA & Tax', 'sort_order' => 5, 'icon' => 'TAX', 'description' => 'TIN, tax clearance and tax return assistance.'],
            ['slug' => 'travel', 'name' => 'Travel & Bookings', 'sort_order' => 6, 'icon' => 'TRV', 'description' => 'Flights, accommodation and travel planning.'],
            ['slug' => 'other-online-forms', 'name' => 'Other Online Forms', 'sort_order' => 7, 'icon' => 'WEB', 'description' => 'Other online applications and institutional forms.'],
        ];

        foreach ($onlineGroups as $group) {
            ServiceCategory::updateOrCreate(
                ['slug' => $group['slug']],
                $group + ['parent_id' => $onlineGov->id, 'is_active' => true],
            );
        }

        // Stationery belongs inside the printing/branding pillar so the public catalogue
        // keeps one clean creative-services area instead of exposing another top-level card.
        $printing = ServiceCategory::where('slug', 'printing-graphics-design')->firstOrFail();
        ServiceCategory::updateOrCreate(
            ['slug' => 'stationery'],
            [
                'name' => 'Stationery',
                'description' => 'Office and school stationery supplies, including customized items.',
                'sort_order' => 2,
                'icon' => 'STA',
                'parent_id' => $printing->id,
                'is_active' => true,
            ],
        );

        // Business is its own pillar, while BRELA remains a useful sub-group.
        ServiceCategory::updateOrCreate(
            ['slug' => 'brela-business'],
            [
                'name' => 'BRELA & Business Registration',
                'description' => 'Business names, company registration and related business setup services.',
                'sort_order' => 1,
                'icon' => 'BRL',
                'parent_id' => $business->id,
                'is_active' => true,
            ],
        );

        // Older builds created duplicate top-level categories. Move their services
        // into the canonical categories and hide the legacy category itself.
        $legacyMap = [
            'business-registration' => 'brela-business',
            'tax-tra' => 'tra',
            'immigration-travel' => 'travel',
            'it-consultancy' => 'it-tech-consultancy',
            'printing-graphics' => 'printing-graphics-design',
        ];

        foreach ($legacyMap as $legacySlug => $canonicalSlug) {
            $legacy = ServiceCategory::withTrashed()->where('slug', $legacySlug)->first();
            $canonical = ServiceCategory::withTrashed()->where('slug', $canonicalSlug)->first();

            if (! $legacy || ! $canonical || $legacy->id === $canonical->id) {
                continue;
            }

            Service::where('service_category_id', $legacy->id)
                ->update(['service_category_id' => $canonical->id]);

            $legacy->update(['is_active' => false]);
        }

        // A previous version placed BRELA under Government. Re-parent it safely.
        ServiceCategory::where('slug', 'brela-business')
            ->update(['parent_id' => $business->id, 'is_active' => true]);
    }
}
