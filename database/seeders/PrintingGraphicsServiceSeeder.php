<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

class PrintingGraphicsServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'printing-graphics-design')->firstOrFail()->id;

        $this->seedService($id, 'Branding & Logo Design', [
            ['label' => 'Company/Organization Name', 'field_key' => 'company_name', 'field_type' => 'text'],
            ['label' => 'Industry/Niche', 'field_key' => 'industry', 'field_type' => 'text'],
            ['label' => 'Color Preferences', 'field_key' => 'color_preferences', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Style Preferences', 'field_key' => 'style_preferences', 'field_type' => 'select', 'options' => ['Modern', 'Classic', 'Minimalist', 'Bold', 'Other']],
            ['label' => 'Design Inspiration/Examples', 'field_key' => 'inspiration', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'Logo Application Use', 'field_key' => 'logo_use', 'field_type' => 'textarea'],
        ], sortOrder: 1);

        $this->seedService($id, 'Printing Services', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Print Type', 'field_key' => 'print_type', 'field_type' => 'select', 'options' => ['Business Cards', 'Flyers', 'Banners', 'Brochures', 'Posters', 'Other']],
            ['label' => 'Quantity', 'field_key' => 'quantity', 'field_type' => 'number'],
            ['label' => 'Size/Dimensions', 'field_key' => 'size', 'field_type' => 'text'],
            ['label' => 'Color Options', 'field_key' => 'color_options', 'field_type' => 'select', 'options' => ['Black & White', 'Full Color', 'Mixed']],
            ['label' => 'Design File Upload', 'field_key' => 'upload_design', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 2);

        $this->seedService($id, 'Graphic Design Services', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Design Type', 'field_key' => 'design_type', 'field_type' => 'select', 'options' => ['Social Media Graphics', 'Web Banners', 'Flyers', 'Posters', 'Business Cards', 'Other']],
            ['label' => 'Design Description', 'field_key' => 'design_description', 'field_type' => 'textarea'],
            ['label' => 'Brand Guidelines', 'field_key' => 'upload_brand_guidelines', 'field_type' => 'file', 'is_required' => false],
            ['label' => 'Reference Images', 'field_key' => 'upload_references', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 3);
    }
}