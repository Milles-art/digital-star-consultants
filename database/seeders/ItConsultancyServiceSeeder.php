<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

class ItConsultancyServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'it-tech-consultancy')->firstOrFail()->id;

        $this->seedService($id, 'Website Development', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Company Name', 'field_key' => 'company', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Website Type', 'field_key' => 'website_type', 'field_type' => 'select', 'options' => ['Business', 'Portfolio', 'E-commerce', 'Blog', 'Corporate']],
            ['label' => 'Features Required', 'field_key' => 'features', 'field_type' => 'textarea'],
            ['label' => 'Design Preferences', 'field_key' => 'design_preferences', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'Budget Range', 'field_key' => 'budget', 'field_type' => 'select', 'options' => ['Low', 'Medium', 'High']],
            ['label' => 'Timeline', 'field_key' => 'timeline', 'field_type' => 'text'],
            ['label' => 'Reference Websites', 'field_key' => 'references', 'field_type' => 'textarea', 'is_required' => false],
        ], sortOrder: 1);

        $this->seedService($id, 'Mobile App Development', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Company Name', 'field_key' => 'company', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'App Type', 'field_key' => 'app_type', 'field_type' => 'select', 'options' => ['Android', 'iOS', 'Both']],
            ['label' => 'App Features/Specifications', 'field_key' => 'features', 'field_type' => 'textarea'],
            ['label' => 'Target Audience', 'field_key' => 'target_audience', 'field_type' => 'textarea'],
            ['label' => 'Platform Preferences', 'field_key' => 'platform', 'field_type' => 'select', 'options' => ['Native', 'Cross-platform', 'PWA']],
            ['label' => 'Budget Range', 'field_key' => 'budget', 'field_type' => 'select', 'options' => ['Low', 'Medium', 'High']],
        ], sortOrder: 2);

        $this->seedService($id, 'IT Consultancy & Strategy', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Company Name', 'field_key' => 'company', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Consultancy Type', 'field_key' => 'consultancy_type', 'field_type' => 'select', 'options' => ['Digital Transformation', 'IT Infrastructure', 'Cybersecurity', 'Cloud Solutions', 'Other']],
            ['label' => 'Current Challenges/Issues', 'field_key' => 'challenges', 'field_type' => 'textarea'],
            ['label' => 'Desired Outcomes/Goals', 'field_key' => 'goals', 'field_type' => 'textarea'],
            ['label' => 'Budget Range', 'field_key' => 'budget', 'field_type' => 'select', 'options' => ['Low', 'Medium', 'High']],
            ['label' => 'Preferred Consultation Method', 'field_key' => 'consultation_method', 'field_type' => 'select', 'options' => ['In-person', 'Virtual/Online']],
        ], sortOrder: 3);
    }
}