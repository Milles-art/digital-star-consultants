<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

class BrelaBusinessServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'brela-business')->firstOrFail()->id;

        $this->seedService($id, 'Business Registration (BRELA)', [
            ['label' => 'Business Name', 'field_key' => 'business_name', 'field_type' => 'text'],
            ['label' => 'Owner Full Name', 'field_key' => 'owner_name', 'field_type' => 'text'],
            ['label' => 'NIDA Number', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Business Type', 'field_key' => 'business_type', 'field_type' => 'select', 'options' => ['Sole Proprietorship', 'Partnership', 'Company Limited']],
            ['label' => 'Business Address', 'field_key' => 'business_address', 'field_type' => 'text'],
            ['label' => 'TIN Number', 'field_key' => 'tin_number', 'field_type' => 'text'],
            ['label' => 'NIDA Copy', 'field_key' => 'upload_nida_copy', 'field_type' => 'file'],
            ['label' => 'Passport Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], sortOrder: 1);

        $this->seedService($id, 'Company Registration (BRELA)', [
            ['label' => 'Company Name', 'field_key' => 'company_name', 'field_type' => 'text'],
            ['label' => 'Company Type', 'field_key' => 'company_type', 'field_type' => 'select', 'options' => ['Private Limited', 'Public Limited', 'Limited by Guarantee']],
            ['label' => 'Directors Names', 'field_key' => 'directors_names', 'field_type' => 'textarea'],
            ['label' => 'Shareholders Names', 'field_key' => 'shareholders', 'field_type' => 'textarea'],
            ['label' => 'Company Secretary Name', 'field_key' => 'secretary_name', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Registered Address', 'field_key' => 'registered_address', 'field_type' => 'text'],
            ['label' => 'Memorandum & Articles', 'field_key' => 'upload_memorandum', 'field_type' => 'file'],
            ['label' => 'TIN Certificate', 'field_key' => 'upload_tin_certificate', 'field_type' => 'file'],
        ], sortOrder: 2);

        $this->seedService($id, 'Business Name Search & Reservation', [
            ['label' => 'Proposed Business Names', 'field_key' => 'proposed_names', 'field_type' => 'textarea'],
            ['label' => 'Business Type', 'field_key' => 'business_type', 'field_type' => 'select', 'options' => ['Sole Proprietorship', 'Partnership', 'Company Limited']],
            ['label' => 'Contact Name', 'field_key' => 'contact_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
        ], sortOrder: 3);

        $this->seedService($id, 'NGO Registration (BRELA)', [
            ['label' => 'NGO Name', 'field_key' => 'ngo_name', 'field_type' => 'text'],
            ['label' => 'Registration Number (if existing)', 'field_key' => 'registration_number', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Founders Names', 'field_key' => 'founders_names', 'field_type' => 'textarea'],
            ['label' => 'NGO Purpose', 'field_key' => 'ngo_purpose', 'field_type' => 'textarea'],
            ['label' => 'Contact Address', 'field_key' => 'contact_address', 'field_type' => 'text'],
            ['label' => 'Constitution/By-laws', 'field_key' => 'upload_constitution', 'field_type' => 'file'],
            ['label' => 'Passport Photos', 'field_key' => 'upload_passport_photos', 'field_type' => 'file'],
        ], sortOrder: 4);
    }
}