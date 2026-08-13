<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

class JobsServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'jobs')->firstOrFail()->id;

        $this->seedService($id, 'Job Application Assistance', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Current Job Title', 'field_key' => 'current_job_title', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Years of Experience', 'field_key' => 'years_experience', 'field_type' => 'number'],
            ['label' => 'Highest Education Level', 'field_key' => 'education_level', 'field_type' => 'select', 'options' => ['Certificate', 'Diploma', 'Bachelor\'s', 'Master\'s', 'PhD']],
            ['label' => 'Job Position Applied For', 'field_key' => 'job_position', 'field_type' => 'text'],
            ['label' => 'Company/Organization', 'field_key' => 'company', 'field_type' => 'text'],
            ['label' => 'Cover Letter', 'field_key' => 'cover_letter', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'CV/Resume', 'field_key' => 'upload_cv', 'field_type' => 'file'],
            ['label' => 'Certificates', 'field_key' => 'upload_certificates', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 1);

        $this->seedService($id, 'Job Search Assistance', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Desired Job Type', 'field_key' => 'job_type', 'field_type' => 'select', 'options' => ['Full-time', 'Part-time', 'Contract', 'Internship', 'Remote']],
            ['label' => 'Preferred Industry', 'field_key' => 'industry', 'field_type' => 'text'],
            ['label' => 'Minimum Salary Expectation', 'field_key' => 'salary_expectation', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Skills', 'field_key' => 'skills', 'field_type' => 'textarea'],
            ['label' => 'CV/Resume', 'field_key' => 'upload_cv', 'field_type' => 'file'],
        ], sortOrder: 2);
    }
}