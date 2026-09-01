<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Guard: this seeder creates demo accounts. NEVER let it run in production.
        if (app()->environment('production')) {
            $this->command?->error('DatabaseSeeder skipped: refusing to seed demo accounts in production.');
            return;
        }

        // --------------------------------------------------
        // Core users (explicit assignment – role not fillable)
        // --------------------------------------------------
        // SECURITY: these are strong random passwords for local/staging only.
        // Change them immediately after seeding, or use php artisan tinker to reset.
        $admin  = $this->createUser('Admin User', 'admin@digitalstar.local',  User::ROLE_ADMIN,            'Adm1n!Dsc2026#Xq9');
        $ceo    = $this->createUser('CEO User',   'ceo@digitalstar.local',    User::ROLE_CEO,              'Ceo1!Dsc2026#Yp8');
        $gm     = $this->createUser('GM User',    'gm@digitalstar.local',     User::ROLE_GENERAL_MANAGER,  'Gm1!Dsc2026#Zo7');
        $staff1 = $this->createUser('Staff One',  'staff1@digitalstar.local', User::ROLE_STAFF,            'Stf1!Dsc2026#Wn6');
        $staff2 = $this->createUser('Staff Two',  'staff2@digitalstar.local', User::ROLE_STAFF,            'Stf2!Dsc2026#Vm5');

        // --------------------------------------------------
        // Canonical service catalogue
        // --------------------------------------------------
        $this->call([
            ServiceCategorySeeder::class,
            SerikaliIdentificationServiceSeeder::class,
            JobsServiceSeeder::class,
            EducationServiceSeeder::class,
            TraServiceSeeder::class,
            BrelaBusinessServiceSeeder::class,
            TravelServiceSeeder::class,
            OtherOnlineFormsServiceSeeder::class,
            PrintingGraphicsServiceSeeder::class,
            StationeryServiceSeeder::class,
            ItConsultancyServiceSeeder::class,
        ]);

        // --------------------------------------------------
        // Sample submissions
        // --------------------------------------------------
        $activeServices = Service::where('is_active', true)->get();

        if ($activeServices->isNotEmpty()) {
            // Pending (unassigned)
            Submission::factory()->count(3)->create([
                'service_id' => $activeServices->random()->id,
                'status' => Submission::STATUS_PENDING,
            ]);

            // Assigned to staff1
            Submission::factory()->count(2)->assignedTo($staff1)->create([
                'service_id' => $activeServices->random()->id,
            ]);

            // Assigned to staff2 + completed
            Submission::factory()->count(2)->assignedTo($staff2)->completed()->create([
                'service_id' => $activeServices->random()->id,
            ]);
        }

        $this->command?->info('Seeded 5 demo users with strong passwords.');
        $this->command?->warn('For local credentials management, use the Admin > Team & Access password reset action.');
    }

    protected function createUser(string $name, string $email, string $role, string $password): User
    {
        $user = User::where('email', $email)->first();

        if ($user) {
            return $user;
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->role = $role;
        $user->is_active = true;
        $user->email_verified_at = now();
        $user->save();

        return $user;
    }
}
