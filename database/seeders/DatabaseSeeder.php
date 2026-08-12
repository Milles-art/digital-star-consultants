<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ServiceCategorySeeder::class,
            SerikaliIdentificationServiceSeeder::class,

            // Written next, uncomment as each one lands:
            // JobsServiceSeeder::class,
            // EducationServiceSeeder::class,
            // TraServiceSeeder::class,
            // BrelaBusinessServiceSeeder::class,
            // TravelServiceSeeder::class,
            // OtherOnlineFormsServiceSeeder::class,
            // PrintingGraphicsServiceSeeder::class,
            // StationeryServiceSeeder::class,
            // ItConsultancyServiceSeeder::class,
        ]);
    }
}
