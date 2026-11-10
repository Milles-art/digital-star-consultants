<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Support\SeparateServiceCatalogue;
use Database\Seeders\SeparateServicesSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SeparateServices extends Command
{
    protected $signature = 'catalogue:separate-services {--dry-run}';
    protected $description = 'Separate existing combined service actions while preserving historical records';

    public function handle(): int
    {
        return DB::transaction(function () {
            $services = Service::withTrashed()->whereIn('slug', SeparateServiceCatalogue::slugs())
                ->with('allFields')->lockForUpdate()->get();
            if ($this->option('dry-run')) {
                $this->info($services->count().' existing records found; no changes made.');
                return self::SUCCESS;
            }
            $backup = 'catalogue-backups/separate-'.now()->format('Ymd-His-u').'.json';
            if (! Storage::disk('private')->put($backup, json_encode($services->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR))) {
                throw new \RuntimeException('Backup failed; separation cancelled.');
            }
            (new SeparateServicesSeeder)->run();
            $this->info('Services separated. Backup: '.Storage::disk('private')->path($backup));
            return self::SUCCESS;
        });
    }
}
