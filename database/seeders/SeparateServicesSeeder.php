<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Support\GovernmentServiceCatalogue;
use App\Support\SeparateServiceCatalogue;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

class SeparateServicesSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $origins = Service::active()->whereIn('slug', array_keys(SeparateServiceCatalogue::groups()))->get()->keyBy('slug');
        foreach (GovernmentServiceCatalogue::definitions() as $slug => $profile) {
            $origin = $origins->get($profile['origin_slug'] ?? '');
            if (! $origin) continue;
            $existing = Service::withTrashed()->where('slug', $slug)->first();
            if ($existing && (! $existing->is_active || $existing->trashed())) continue;
            // New actions have no assumed price. Existing prices remain intact.
            $this->seedService($existing?->service_category_id ?? $origin->service_category_id,
                $profile['name'], $profile['fields'], description: $profile['description'],
                sortOrder: $existing?->sort_order ?? $origin->sort_order, slug: $slug);
        }
    }
}
