<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

class TraServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'tra')->firstOrFail()->id;

        $this->seedService($id, 'TIN Registration', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Date of Birth', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'NIDA Number', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Business/Employment Type', 'field_key' => 'business_type', 'field_type' => 'select', 'options' => ['Individual', 'Company', 'Self-employed', 'Government']],
            ['label' => 'Business Name (if applicable)', 'field_key' => 'business_name', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'NIDA Copy', 'field_key' => 'upload_nida_copy', 'field_type' => 'file'],
            ['label' => 'Passport Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], sortOrder: 1);

        $this->seedService($id, 'Tax Clearance Certificate', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'TIN Number', 'field_key' => 'tin_number', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Purpose of Clearance', 'field_key' => 'purpose', 'field_type' => 'select', 'options' => ['Tender', 'Employment', 'Business Registration', 'Other']],
            ['label' => 'TIN Certificate', 'field_key' => 'upload_tin_certificate', 'field_type' => 'file'],
        ], sortOrder: 2);

        $this->seedService($id, 'Tax Return Filing Assistance', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'TIN Number', 'field_key' => 'tin_number', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Tax Year', 'field_key' => 'tax_year', 'field_type' => 'text'],
            ['label' => 'Income Details', 'field_key' => 'income_details', 'field_type' => 'textarea'],
            ['label' => 'Expenses/Deductions', 'field_key' => 'expenses', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'Financial Statements', 'field_key' => 'upload_financial_statements', 'field_type' => 'file'],
        ], sortOrder: 3);
    }
}