<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

class OtherOnlineFormsServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'other-online-forms')->firstOrFail()->id;

        $this->seedService($id, 'General Form Assistance', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Form/Organization Name', 'field_key' => 'form_name', 'field_type' => 'text'],
            ['label' => 'Form Purpose', 'field_key' => 'form_purpose', 'field_type' => 'textarea'],
            ['label' => 'Additional Details', 'field_key' => 'additional_details', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'Supporting Documents', 'field_key' => 'upload_supporting_docs', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 1);

        $this->seedService($id, 'NGO Membership / Application', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Organization Name', 'field_key' => 'organization_name', 'field_type' => 'text'],
            ['label' => 'Membership Type', 'field_key' => 'membership_type', 'field_type' => 'select', 'options' => ['Individual', 'Corporate', 'Student']],
            ['label' => 'Why do you want to join?', 'field_key' => 'motivation', 'field_type' => 'textarea'],
            ['label' => 'Supporting Documents', 'field_key' => 'upload_documents', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 2);

        $this->seedService($id, 'Welfare/Community Assistance Request', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email', 'is_required' => false],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Type of Assistance', 'field_key' => 'assistance_type', 'field_type' => 'select', 'options' => ['Medical', 'Educational', 'Funeral', 'Food', 'Other']],
            ['label' => 'Reason for Request', 'field_key' => 'reason', 'field_type' => 'textarea'],
            ['label' => 'Supporting Documents', 'field_key' => 'upload_documents', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 3);
    }
}