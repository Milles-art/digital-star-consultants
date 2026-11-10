<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Support\GovernmentServiceCatalogue;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/** Updates existing active services only; no demo accounts, prices or submissions. */
class GovernmentRequirementsSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(?array $slugs = null): void
    {
        foreach (Service::where('is_active', true)->whereIn('slug', $slugs ?? array_keys(GovernmentServiceCatalogue::definitions()))->get() as $service) {
            $profile = GovernmentServiceCatalogue::definition($service->slug);
            $this->seedService(
                $service->service_category_id, $profile['name'], $profile['fields'],
                description: $profile['description'], sortOrder: $service->sort_order, slug: $service->slug,
            );
        }
    }
}
