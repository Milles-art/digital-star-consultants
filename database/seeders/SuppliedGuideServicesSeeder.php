<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Support\GovernmentServiceCatalogue;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/** Only the two missing services explicitly named in the supplied PDFs. */
class SuppliedGuideServicesSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        foreach (['madai-eardhi', 'latra-registration'] as $slug) {
            $existing = Service::withTrashed()->where('slug', $slug)->first();
            if ($existing && (! $existing->is_active || $existing->trashed())) {
                continue;
            }
            $profile = GovernmentServiceCatalogue::definition($slug);
            $category = ServiceCategory::where('slug', $profile['category'])->firstOrFail();
            $this->seedService($category->id, $profile['name'], $profile['fields'],
                description: $profile['description'], sortOrder: $existing?->sort_order ?? 0, slug: $slug);
        }
    }
}
