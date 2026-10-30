<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/**
 * Kazi na ajira.
 */
class JobsServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'jobs')->firstOrFail()->id;

        $this->seedService($id, 'Msaada wa Maombi ya Kazi', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Namba ya NIDA', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Anwani ya Makazi', 'field_key' => 'residential_address', 'field_type' => 'text'],
            ['label' => 'Namba ya Usajiri (Index Number)', 'field_key' => 'index_number', 'field_type' => 'text'],
            ['label' => 'Cheo cha Kazi Sasa', 'field_key' => 'current_job_title', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Idadi ya Miaka ya Uzoefu', 'field_key' => 'years_experience', 'field_type' => 'number'],
            ['label' => 'Kiwango cha Juu cha Elimu', 'field_key' => 'education_level', 'field_type' => 'select', 'options' => ['Cheti', 'Diploma', 'Bachelor', 'Master', 'PhD']],
            ['label' => 'Nafasi Unaomba', 'field_key' => 'job_position', 'field_type' => 'text'],
            ['label' => 'Kampuni / Taasisi', 'field_key' => 'company', 'field_type' => 'text'],
            ['label' => 'Barua ya Maombi', 'field_key' => 'cover_letter', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'CV', 'field_key' => 'upload_cv', 'field_type' => 'file'],
            ['label' => 'Cheti cha Kuzaliwa', 'field_key' => 'upload_birth_certificate', 'field_type' => 'file'],
            ['label' => 'Vyeti', 'field_key' => 'upload_certificates', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 1, slug: 'job-application-assistance');

        $this->seedService($id, 'Msaada wa Kutafuta Kazi', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Kazi Unayotafuta', 'field_key' => 'job_type', 'field_type' => 'select', 'options' => ['Siku nzima', 'Nusu muda', 'Mkataba', 'Uanafunzi', 'Mtandaoni']],
            ['label' => 'Kundi Unalopendelea', 'field_key' => 'industry', 'field_type' => 'text'],
            ['label' => 'Kiwango cha Chini cha Mshahara Unachotarajia', 'field_key' => 'salary_expectation', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Ujuzi Ulionao', 'field_key' => 'skills', 'field_type' => 'textarea'],
            ['label' => 'CV', 'field_key' => 'upload_cv', 'field_type' => 'file'],
        ], sortOrder: 2, slug: 'job-search-assistance');
    }
}
