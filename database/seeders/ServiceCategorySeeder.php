<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Kiswahili is the primary customer-facing language. Slugs stay stable
        // because they are the category's identity (bookmarks, footer links,
        // existing submissions all point at them).
        $pillars = [
            [
                'slug' => 'online-government-services',
                'name' => 'Huduma za Serikali na Mtandaoni',
                'description' => 'Maombi ya serikali, utambulisho, uhamiaji, elimu, ajira na msaada wa mtandaoni.',
                'sort_order' => 1,
                'icon' => '🏛',
                'color' => '#1657D9',
            ],
            [
                'slug' => 'business-services',
                'name' => 'Huduma za Biashara',
                'description' => 'Usajili wa biashara, uanzishaji wa kampuni, miongozo ya kisheria na msaada wa biashara.',
                'sort_order' => 2,
                'icon' => '▣',
                'color' => '#1657D9',
            ],
            [
                'slug' => 'printing-graphics-design',
                'name' => 'Uchapishaji, Brandi na Vifaa',
                'description' => 'Uchapishaji, ubunifu wa graphics, brandi na vifaa vya ofisi na shule.',
                'sort_order' => 3,
                'icon' => '✦',
                'color' => '#F6C945',
            ],
            [
                'slug' => 'it-tech-consultancy',
                'name' => 'IT na Teknolojia',
                'description' => 'Tovuti, mifumo ya programu, hosting, simu na ushauri wa teknolojia.',
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
            ['slug' => 'serikali-identification', 'name' => 'Serikali na Kitambulisho', 'sort_order' => 1, 'icon' => 'ID', 'description' => 'NIDA, usajili wa matukio ya kiraia, leseni za udereva na huduma nyingine za kitambulisho.'],
            ['slug' => 'passport-immigration', 'name' => 'Pasipoti na Uhamiaji', 'sort_order' => 2, 'icon' => 'PAS', 'description' => 'Pasipoti, visa na huduma za uhamiaji mahali pamoja.'],
            ['slug' => 'jobs', 'name' => 'Kazi na Ajira', 'sort_order' => 3, 'icon' => 'JOB', 'description' => 'Maombi ya kazi na msaada wa kutafuta kazi.'],
            ['slug' => 'education', 'name' => 'Elimu, Vyuo na Mikopo', 'sort_order' => 4, 'icon' => 'EDU', 'description' => 'Maombi ya shule, vyuo, mikopo (HESLB) na ufadhili.'],
            ['slug' => 'tra', 'name' => 'TRA na Kodi', 'sort_order' => 5, 'icon' => 'TAX', 'description' => 'TIN, kibali cha kodi, marejesho ya kodi na msaada wa TRA.'],
            ['slug' => 'travel', 'name' => 'Usafiri na Booking', 'sort_order' => 6, 'icon' => 'TRV', 'description' => 'Ndege, malazi na kupanga safari.'],
            ['slug' => 'other-online-forms', 'name' => 'Fomu Nyingine Mtandaoni', 'sort_order' => 7, 'icon' => 'WEB', 'description' => 'Maombi mengine ya mtandaoni, NSSF na fomu za taasisi.'],
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
                'name' => 'Vifaa vya Ofisi na Shule',
                'description' => 'Usambazaji wa vifaa vya ofisi na shule, pamoja na vyenye nembo ya biashara.',
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
                'name' => 'BRELA na Usajili wa Biashara',
                'description' => 'Usajili wa majina ya biashara, usajili wa kampuni na huduma nyingine za kuanzisha biashara.',
                'sort_order' => 1,
                'icon' => 'BRL',
                'parent_id' => $business->id,
                'is_active' => true,
            ],
        );

        // Telecom lines, Lipa Number and digital payments sit under the IT pillar.
        ServiceCategory::updateOrCreate(
            ['slug' => 'simu-mtandao-na-malipo'],
            [
                'name' => 'Simu, Mtandao na Malipo',
                'description' => 'Laini za wakala, Lipa Number, usajili wa laini na huduma za malipo ya simu.',
                'sort_order' => 5,
                'icon' => 'TEL',
                'parent_id' => ServiceCategory::where('slug', 'it-tech-consultancy')->firstOrFail()->id,
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
