<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

class EducationServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'education')->firstOrFail()->id;

        $this->seedService($id, 'School Application Assistance', [
            ['label' => 'Student Full Name', 'field_key' => 'student_name', 'field_type' => 'text'],
            ['label' => 'Date of Birth', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Parent/Guardian Name', 'field_key' => 'guardian_name', 'field_type' => 'text'],
            ['label' => 'Parent/Guardian Email', 'field_key' => 'guardian_email', 'field_type' => 'email'],
            ['label' => 'Parent/Guardian Phone', 'field_key' => 'guardian_phone', 'field_type' => 'tel'],
            ['label' => 'School Applying To', 'field_key' => 'school_name', 'field_type' => 'text'],
            ['label' => 'Grade/Class Applied For', 'field_key' => 'grade_applied', 'field_type' => 'text'],
            ['label' => 'Current School', 'field_key' => 'current_school', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Academic Reports', 'field_key' => 'upload_academic_reports', 'field_type' => 'file'],
            ['label' => 'Birth Certificate', 'field_key' => 'upload_birth_certificate', 'field_type' => 'file'],
            ['label' => 'Passport Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], sortOrder: 1);

        $this->seedService($id, 'Scholarship Application Assistance', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Scholarship Name/Program', 'field_key' => 'scholarship_name', 'field_type' => 'text'],
            ['label' => 'Field of Study', 'field_key' => 'field_of_study', 'field_type' => 'text'],
            ['label' => 'Current Education Level', 'field_key' => 'education_level', 'field_type' => 'select', 'options' => ['Secondary', 'Bachelor\'s', 'Master\'s', 'PhD']],
            ['label' => 'Motivation Letter', 'field_key' => 'motivation_letter', 'field_type' => 'textarea'],
            ['label' => 'Academic Transcripts', 'field_key' => 'upload_transcripts', 'field_type' => 'file'],
            ['label' => 'Recommendation Letters', 'field_key' => 'upload_recommendations', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 2);

        $this->seedService($id, 'Exam Registration Assistance', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Date of Birth', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Email Address', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Phone Number', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Exam Type', 'field_key' => 'exam_type', 'field_type' => 'select', 'options' => ['NECTA', 'Cambridge', 'IELTS', 'TOEFL', 'Other']],
            ['label' => 'Exam Subjects', 'field_key' => 'exam_subjects', 'field_type' => 'textarea'],
            ['label' => 'Preferred Exam Center', 'field_key' => 'exam_center', 'field_type' => 'text'],
            ['label' => 'Passport Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], sortOrder: 3);
    }
}