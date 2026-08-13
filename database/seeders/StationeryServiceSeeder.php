<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

class StationeryServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'stationery')->firstOrFail()->id;

        $this->seedService($id, 'Office Stationery Order', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Company/Organization', 'field_key' => 'company', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Delivery Address', 'field_key' => 'delivery_address', 'field_type' => 'text'],
            ['label' => 'Items Required', 'field_key' => 'items_required', 'field_type' => 'textarea'],
            ['label' => 'Quantity Details', 'field_key' => 'quantity_details', 'field_type' => 'textarea'],
            ['label' => 'Budget Range', 'field_key' => 'budget', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Delivery Date', 'field_key' => 'delivery_date', 'field_type' => 'date'],
        ], sortOrder: 1);

        $this->seedService($id, 'School Stationery Request', [
            ['label' => 'School Name', 'field_key' => 'school_name', 'field_type' => 'text'],
            ['label' => 'Contact Person', 'field_key' => 'contact_person', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Delivery Address', 'field_key' => 'delivery_address', 'field_type' => 'text'],
            ['label' => 'Stationery List', 'field_key' => 'stationery_list', 'field_type' => 'textarea'],
            ['label' => 'Expected Use Date', 'field_key' => 'use_date', 'field_type' => 'date'],
        ], sortOrder: 2);

        $this->seedService($id, 'Customized Stationery (Branded)', [
            ['label' => 'Company/Organization Name', 'field_key' => 'company_name', 'field_type' => 'text'],
            ['label' => 'Contact Person', 'field_key' => 'contact_person', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Stationery Type', 'field_key' => 'stationery_type', 'field_type' => 'select', 'options' => ['Letterheads', 'Envelopes', 'Business Cards', 'Notepads']],
            ['label' => 'Quantity Required', 'field_key' => 'quantity', 'field_type' => 'number'],
            ['label' => 'Brand/Logo File', 'field_key' => 'upload_logo', 'field_type' => 'file'],
            ['label' => 'Design Preferences', 'field_key' => 'design_preferences', 'field_type' => 'textarea', 'is_required' => false],
        ], sortOrder: 3);
    }
}