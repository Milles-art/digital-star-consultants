<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $pillars = [
            [
                'slug' => 'online-government-services',
                'name' => 'Online & Government Services',
                'description' => 'Help applying for and following up on government and institutional services.',
                'sort_order' => 1,
            ],
            [
                'slug' => 'printing-graphics-design',
                'name' => 'Printing & Graphics Design',
                'description' => 'Printing, branding, and graphic design.',
                'sort_order' => 2,
            ],
            [
                'slug' => 'stationery',
                'name' => 'Stationery',
                'description' => 'Office and school stationery supplies.',
                'sort_order' => 3,
            ],
            [
                'slug' => 'it-tech-consultancy',
                'name' => 'IT & Tech Consultancy',
                'description' => 'Custom systems, websites, and technology consulting.',
                'sort_order' => 4,
            ],
        ];

        foreach ($pillars as $pillar) {
            ServiceCategory::updateOrCreate(
                ['slug' => $pillar['slug']],
                $pillar + ['is_active' => true],
            );
        }

        $onlineGov = ServiceCategory::where('slug', 'online-government-services')->firstOrFail();

        $subGroups = [
            ['slug' => 'serikali-identification', 'name' => 'Serikali & Identification', 'sort_order' => 1],
            ['slug' => 'jobs', 'name' => 'Jobs', 'sort_order' => 2],
            ['slug' => 'education', 'name' => 'Education', 'sort_order' => 3],
            ['slug' => 'tra', 'name' => 'TRA', 'sort_order' => 4],
            ['slug' => 'brela-business', 'name' => 'BRELA & Business', 'sort_order' => 5],
            ['slug' => 'travel', 'name' => 'Travel', 'sort_order' => 6],
            ['slug' => 'other-online-forms', 'name' => 'Other Online Forms', 'sort_order' => 7],
        ];

        foreach ($subGroups as $group) {
            ServiceCategory::updateOrCreate(
                ['slug' => $group['slug']],
                $group + ['parent_id' => $onlineGov->id, 'is_active' => true],
            );
        }
    }
}
