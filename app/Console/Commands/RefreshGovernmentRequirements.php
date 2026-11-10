<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Support\GovernmentServiceCatalogue;
use Database\Seeders\GovernmentRequirementsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RefreshGovernmentRequirements extends Command
{
    protected $signature = 'catalogue:refresh-government {--dry-run : List services without changing records} {--documents : Update only services covered by the supplied PDF guides}';

    protected $description = 'Back up and refresh existing government assistance checklists, preserving prices and submission history';

    public function handle(): int
    {
        return DB::transaction(function () {
            $slugs = $this->option('documents')
                ? \App\Support\DocumentServiceRequirements::affectedSlugs()
                : array_keys(GovernmentServiceCatalogue::definitions());
            $services = Service::where('is_active', true)
                ->whereIn('slug', $slugs)
                ->with('allFields')->lockForUpdate()->get();
            foreach ($services as $service) {
                $this->line($service->slug.' -> '.GovernmentServiceCatalogue::definition($service->slug)['name']);
            }
            if ($this->option('dry-run')) {
                $this->info($services->count().' existing active services found; no changes made.');

                return self::SUCCESS;
            }

            $backup = 'catalogue-backups/government-'.now()->format('Ymd-His-u').'.json';
            $written = Storage::disk('private')->put($backup, json_encode(
                ['version' => GovernmentServiceCatalogue::VERSION, 'saved_at' => now()->toIso8601String(), 'services' => $services->toArray()],
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            ));
            if (! $written) {
                throw new \RuntimeException('Catalogue backup could not be written; refresh cancelled.');
            }
            // These two services are explicitly described in the supplied
            // guides but were missing; never create other speculative services.
            if ($this->option('documents')) {
                (new \Database\Seeders\SuppliedGuideServicesSeeder)->run();
            }
            (new GovernmentRequirementsSeeder)->run($slugs);
            $this->info('Updated '.$services->count().' services. Catalogue backup: '.Storage::disk('private')->path($backup));

            return self::SUCCESS;
        });
    }
}
