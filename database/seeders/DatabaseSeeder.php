<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --------------------------------------------------
        // Core users (explicit assignment – role not fillable)
        // --------------------------------------------------
        $admin = $this->createUser('Admin User', 'admin@digitalstar.local', User::ROLE_ADMIN, 'password');
        $ceo = $this->createUser('CEO User', 'ceo@digitalstar.local', User::ROLE_CEO, 'password');
        $gm = $this->createUser('GM User', 'gm@digitalstar.local', User::ROLE_GENERAL_MANAGER, 'password');
        $staff1 = $this->createUser('Staff One', 'staff1@digitalstar.local', User::ROLE_STAFF, 'password');
        $staff2 = $this->createUser('Staff Two', 'staff2@digitalstar.local', User::ROLE_STAFF, 'password');

        // --------------------------------------------------
        // Categories + Services (demo data)
        // --------------------------------------------------
        $categories = [
            'Business Registration' => ['BRELA Company Name Search', 'Business License Application'],
            'Tax & TRA' => ['TIN Application', 'VAT Registration'],
            'Immigration & Travel' => ['Passport Assistance', 'Visa Support'],
            'IT Consultancy' => ['Website Setup', 'Email Hosting'],
            'Printing & Graphics' => ['Business Cards', 'Banner Design'],
        ];

        foreach ($categories as $catName => $services) {
            $category = ServiceCategory::firstOrCreate(
                ['slug' => Str::slug($catName)],
                [
                    'name' => $catName,
                    'description' => $catName.' services',
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );

            foreach ($services as $index => $serviceName) {
                Service::firstOrCreate(
                    ['slug' => Str::slug($serviceName)],
                    [
                        'service_category_id' => $category->id,
                        'name' => $serviceName,
                        'description' => 'Professional '.$serviceName.' service',
                        'price' => rand(10000, 150000),
                        'is_active' => true,
                        'sort_order' => $index,
                        'duration_minutes' => 60,
                    ]
                );
            }
        }

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

        $this->command?->info('Seeded users:');
        $this->command?->info('  admin@digitalstar.local / password (admin)');
        $this->command?->info('  ceo@digitalstar.local / password (ceo)');
        $this->command?->info('  gm@digitalstar.local / password (gm)');
        $this->command?->info('  staff1@digitalstar.local / password (staff)');
        $this->command?->info('  staff2@digitalstar.local / password (staff)');
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
