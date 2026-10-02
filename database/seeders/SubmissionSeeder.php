<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Service;
use App\Models\ServiceField;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SubmissionSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $this->command?->warn('SubmissionSeeder skipped in production.');
            return;
        }

        $admin = User::where('email', 'admin@digitalstar.local')->first();
        $staff1 = User::where('email', 'staff1@digitalstar.local')->first() ?? $admin;
        $staff2 = User::where('email', 'staff2@digitalstar.local')->first() ?? $admin;

        $definitions = [
            [
                'reference' => 'DSC-DEMO-0001',
                'service' => 'NGO Registration (BRELA)',
                'customer' => ['name' => 'Asha Mwakalinga', 'phone' => '+255 712 345 678', 'email' => 'asha.mwakalinga@example.test'],
                'status' => Submission::STATUS_IN_PROGRESS,
                'staff' => $staff1,
                'price' => 185000,
                'payment' => ['status' => Submission::PAYMENT_PAID, 'method' => 'Mobile Money'],
                'preferred_date' => '2026-09-08 10:00:00',
                'notes' => 'Please process the registration before the planned launch date.',
                'answers' => [
                    'ngo_name' => 'Digital Star Community Foundation',
                    'registration_number' => 'NGO-DSC-2026-0142',
                    'founders_names' => 'Asha Mwakalinga; Rehema Said; John Michael',
                    'ngo_purpose' => 'Community development, digital literacy and youth empowerment.',
                    'contact_address' => 'Samora Avenue, Dar es Salaam',
                ],
                'files' => ['upload_constitution', 'upload_passport_photos'],
            ],
            [
                'reference' => 'DSC-DEMO-0002',
                'service' => 'New Passport Application',
                'customer' => ['name' => 'Daniel Joseph', 'phone' => '+255 754 221 903', 'email' => 'daniel.joseph@example.test'],
                'status' => Submission::STATUS_PENDING,
                'staff' => null,
                'price' => 145000,
                'payment' => ['status' => Submission::PAYMENT_PENDING, 'method' => null],
                'preferred_date' => '2026-09-11 10:00:00',
                'notes' => 'First-time passport application. Please advise if anything else is required.',
                'answers' => [
                    'full_name' => 'Daniel Joseph',
                    'date_of_birth' => '1998-02-14',
                    'place_of_birth' => 'Mwanza, Tanzania',
                    'gender' => 'Male',
                    'nida_number' => '19980214-12345-67890-11',
                    'nationality' => 'Tanzanian',
                    'home_address' => 'Mbezi Beach, Dar es Salaam',
                    'occupation' => 'Software Developer',
                ],
                'files' => ['upload_birth_certificate', 'upload_passport_photo'],
            ],
            [
                'reference' => 'DSC-DEMO-0003',
                'service' => 'TIN Registration',
                'customer' => ['name' => 'Neema Josephat', 'phone' => '+255 783 111 227', 'email' => 'neema.josephat@example.test'],
                'status' => Submission::STATUS_AWAITING_CUSTOMER,
                'staff' => $staff2,
                'price' => 75000,
                'payment' => ['status' => Submission::PAYMENT_PENDING, 'method' => null],
                'preferred_date' => '2026-09-09 10:00:00',
                'notes' => 'I have submitted the available information. Please tell me what is still missing.',
                'answers' => [
                    'full_name' => 'Neema Josephat',
                    'date_of_birth' => '1995-11-03',
                    'nida_number' => '19951103-22222-33333-44',
                    'email' => 'neema.josephat@example.test',
                    'phone' => '+255 783 111 227',
                    'business_type' => 'Individual',
                    'business_name' => null,
                ],
                'files' => [],
            ],
            [
                'reference' => 'DSC-DEMO-0004',
                'service' => 'Birth Certificate Application (RITA)',
                'customer' => ['name' => 'Salma Hamisi', 'phone' => '+255 626 444 318', 'email' => 'salma.hamisi@example.test'],
                'status' => Submission::STATUS_COMPLETED,
                'staff' => $staff1,
                'price' => 55000,
                'payment' => ['status' => Submission::PAYMENT_PAID, 'method' => 'Card'],
                'preferred_date' => '2026-09-05 10:00:00',
                'notes' => 'Certificate application completed successfully.',
                'answers' => [
                    'child_full_name' => 'Imani Hamisi',
                    'date_of_birth' => '2024-03-22',
                    'place_of_birth' => 'Ilala, Dar es Salaam',
                    'father_full_name' => 'Hamisi Juma',
                    'mother_full_name' => 'Salma Omari',
                    'parents_nationality' => 'Tanzanian',
                ],
                'files' => ['upload_clinic_card', 'upload_weo_letter'],
            ],
            [
                'reference' => 'DSC-DEMO-0005',
                'service' => 'Driving Licence Application / Renewal',
                'customer' => ['name' => 'Hassan Omari', 'phone' => '+255 717 909 441', 'email' => 'hassan.omari@example.test'],
                'status' => Submission::STATUS_IN_PROGRESS,
                'staff' => $staff2,
                'price' => 110000,
                'payment' => ['status' => Submission::PAYMENT_PAID, 'method' => 'Mobile Money'],
                'preferred_date' => '2026-09-10 10:00:00',
                'notes' => 'Renewal request. Existing licence number is included below.',
                'answers' => [
                    'full_name' => 'Hassan Omari',
                    'nida_or_passport_number' => '19891010-99887-77665-54',
                    'date_of_birth' => '1989-10-10',
                    'licence_category' => 'Class B',
                    'existing_licence_number' => 'DL-TZ-458219',
                ],
                'files' => ['upload_passport_photo', 'upload_medical_form'],
            ],
        ];

        foreach ($definitions as $definition) {
            $service = Service::with('fields')->where('name', $definition['service'])->first();
            if (! $service) {
                $this->command?->warn("Skipping {$definition['service']}: service not found.");
                continue;
            }

            $submission = Submission::withTrashed()->updateOrCreate(
                ['reference_number' => $definition['reference']],
                [
                    'service_id' => $service->id,
                    'customer_name' => $definition['customer']['name'],
                    'customer_phone' => $definition['customer']['phone'],
                    'customer_email' => $definition['customer']['email'],
                    'customer_notes' => $definition['notes'],
                    'preferred_date' => $definition['preferred_date'],
                    'status' => $definition['status'],
                    'payment_status' => $definition['payment']['status'],
                    'payment_method' => $definition['payment']['method'],
                    'total_price' => $definition['price'],
                    'processed_by' => $definition['staff']?->id,
                    'completed_at' => $definition['status'] === Submission::STATUS_COMPLETED ? now()->subDay() : null,
                    'staff_notes' => $definition['status'] === Submission::STATUS_AWAITING_CUSTOMER
                        ? 'Awaiting one or more customer documents/details.'
                        : 'Demo request for testing the Digital Star operations workspace.',
                    'deleted_at' => null,
                ]
            );

            SubmissionFieldValue::where('submission_id', $submission->id)->delete();
            $this->seedFieldValues($submission, $service->fields, $definition['answers'], $definition['files'], $definition['reference']);

            if (Schema::hasTable('activity_logs')) {
                ActivityLog::where('subject_type', Submission::class)
                    ->where('subject_id', $submission->id)
                    ->delete();

                $events = [
                    ['event' => 'created', 'title' => 'Request received', 'description' => 'Customer submitted a new service request.', 'user_id' => null],
                ];

                if ($definition['staff']) {
                    $events[] = ['event' => 'assigned', 'title' => 'Request assigned', 'description' => 'Request assigned to '.$definition['staff']->name.'.', 'user_id' => $admin?->id];
                }

                if (in_array($definition['status'], [Submission::STATUS_IN_PROGRESS, Submission::STATUS_AWAITING_CUSTOMER, Submission::STATUS_COMPLETED], true)) {
                    $events[] = ['event' => 'status_changed', 'title' => 'Request moved to in progress', 'description' => 'Processing has started on this request.', 'user_id' => $definition['staff']?->id ?? $admin?->id];
                }

                if ($definition['status'] === Submission::STATUS_AWAITING_CUSTOMER) {
                    $events[] = ['event' => 'status_changed', 'title' => 'Awaiting customer information', 'description' => 'Additional information is required to continue processing this request.', 'user_id' => $definition['staff']?->id ?? $admin?->id];
                }

                if ($definition['status'] === Submission::STATUS_COMPLETED) {
                    $events[] = ['event' => 'status_changed', 'title' => 'Request completed', 'description' => 'Service request was completed.', 'user_id' => $definition['staff']?->id ?? $admin?->id];
                }

                foreach ($events as $event) {
                    ActivityLog::create([
                        'user_id' => $event['user_id'],
                        'subject_type' => Submission::class,
                        'subject_id' => $submission->id,
                        'event' => $event['event'],
                        'title' => $event['title'],
                        'description' => $event['description'],
                        'metadata' => [],
                    ]);
                }
            }
        }

        $this->command?->info('Seeded 5 complete demo service requests with service-specific answers and document samples.');
    }

    private function seedFieldValues(Submission $submission, $fields, array $answers, array $fileKeys, string $reference): void
    {
        foreach ($fields->sortBy('sort_order') as $field) {
            $value = $field->field_type === 'file' ? null : ($answers[$field->field_key] ?? null);
            $filePath = null;

            if ($field->field_type === 'file' && in_array($field->field_key, $fileKeys, true)) {
                $filename = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $field->label)).'.pdf';
                $filePath = "submissions/{$submission->id}/{$filename}";
                Storage::disk('private')->put($filePath, "%PDF-1.4\n% Digital Star demo document {$reference}\n");
            }

            SubmissionFieldValue::create([
                'submission_id' => $submission->id,
                'service_field_id' => $field->id,
                'field_label_snapshot' => $field->label,
                'field_key_snapshot' => $field->field_key,
                'field_type_snapshot' => $field->field_type,
                'field_options_snapshot' => $field->options,
                'value' => is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : $value,
                'file_path' => $filePath,
            ]);
        }
    }
}
