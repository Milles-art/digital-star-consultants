<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/**
 * Elimu, vyuo na mikopo (HESLB).
 */
class EducationServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'education')->firstOrFail()->id;

        // -----------------------------------------------------------------
        // Mkopo wa elimu ya juu (HESLB)
        // -----------------------------------------------------------------
        $this->seedService($id, 'Maombi ya Mkopo wa Elimu ya Juu (HESLB)', [
            ['label' => 'Majina Kamili ya Mwanafunzi', 'field_key' => 'student_name', 'field_type' => 'text'],
            ['label' => 'Namba ya NIDA ya Mwanafunzi', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Namba ya NaPA ya Mwanafunzi', 'field_key' => 'napa_student', 'field_type' => 'text'],
            ['label' => 'Namba ya NaPA ya Wazazi', 'field_key' => 'napa_parents', 'field_type' => 'text'],
            ['label' => 'Namba ya NIDA ya Wazazi', 'field_key' => 'parents_nida_number', 'field_type' => 'text'],
            ['label' => 'Ngazi ya Masomo', 'field_key' => 'study_level', 'field_type' => 'select', 'options' => ['Degree', 'Diploma', 'Cheti (Certificate)']],
            ['label' => 'Chuo / Taasisi', 'field_key' => 'institution', 'field_type' => 'text'],
            ['label' => 'Namba ya Akaunti ya Benki', 'field_key' => 'bank_account_number', 'field_type' => 'text'],
            ['label' => 'Jina la Benki', 'field_key' => 'bank_name', 'field_type' => 'text'],
            ['label' => 'Namba ya Uhakiki wa Cheti cha Kuzaliwa', 'field_key' => 'birth_certificate_verification_number', 'field_type' => 'text'],
            ['label' => 'Namba za Mitihani', 'field_key' => 'exam_numbers', 'field_type' => 'textarea'],
            ['label' => 'Jina la Mdhamini', 'field_key' => 'guarantor_name', 'field_type' => 'text'],
            ['label' => 'Jina la Mtu wa Dharura', 'field_key' => 'emergency_contact_name', 'field_type' => 'text'],
            ['label' => 'Uhusiano na Mtu wa Dharura', 'field_key' => 'emergency_relationship', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu ya Mtu wa Dharura', 'field_key' => 'emergency_phone', 'field_type' => 'tel'],
            ['label' => 'Picha ya Mdhamini', 'field_key' => 'upload_guarantor_photo', 'field_type' => 'file'],
            ['label' => 'Barua ya Ufadhili uliopita (CAMFED, benki, taasisi — si lazima)', 'field_key' => 'upload_previous_sponsorship_letter', 'field_type' => 'file', 'is_required' => false],
            ['label' => 'Passport-size Photo ya Mwanafunzi', 'field_key' => 'upload_student_photo', 'field_type' => 'file'],
            ['label' => 'Vyeti vya Elimu', 'field_key' => 'upload_education_certificates', 'field_type' => 'file'],
            ['label' => 'Fomu ya Maombi Imesainiwa na Mwanasheria', 'field_key' => 'upload_signed_application_form', 'field_type' => 'file'],
        ], description: 'Gharama ni TSh 70,000/= tu pamoja na huduma za kisheria na uhakiki wa cheti cha kuzaliwa. Tafadhali hakikisha majina ya cheti chako yanaendana na vyeti vya elimu. Dirisha la maombi ya mkopo linafungwa 31/08/2026.', sortOrder: 1, slug: 'maombi-ya-mkopo-heslb', price: 70000);

        // -----------------------------------------------------------------
        // Maombi ya chuo
        // -----------------------------------------------------------------
        $this->seedService($id, 'Maombi ya Chuo', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Namba zako za Mitihani', 'field_key' => 'exam_numbers', 'field_type' => 'textarea'],
            ['label' => 'Matokeo ya Mitihani (Division / Points)', 'field_key' => 'exam_results', 'field_type' => 'text'],
            ['label' => 'Chuo / Programu Unaotaka Kuomba', 'field_key' => 'preferred_programme', 'field_type' => 'text'],
            ['label' => 'Chaguo la Pili la Programu', 'field_key' => 'second_programme', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Jina la Mtu wa Dharura', 'field_key' => 'emergency_contact_name', 'field_type' => 'text'],
            ['label' => 'Uhusiano na Mtu wa Dharura', 'field_key' => 'emergency_relationship', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu ya Mtu wa Dharura', 'field_key' => 'emergency_phone', 'field_type' => 'tel'],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
            ['label' => 'Vyeti vya Mitihani', 'field_key' => 'upload_exam_certificates', 'field_type' => 'file'],
        ], description: 'Gharama ya kuomba chuo ni TSh 5,000/= na kila chuo unalipia control ya TSh 10,000/=. Jumla ni TSh 15,000/= kwa chuo kimoja. Dirisha la maombi ya vyuo linafungwa 10/08/2026. Tunaweza kukushauri programu bora kulingana na masomo yako, ufaulu na hali ya ajira.', sortOrder: 2, slug: 'maombi-ya-chuo', price: 15000);

        // -----------------------------------------------------------------
        // Huduma zilizopo — kwa Kiswahili
        // -----------------------------------------------------------------
        $this->seedService($id, 'Maombi ya Shule', [
            ['label' => 'Majina Kamili ya Mwanafunzi', 'field_key' => 'student_name', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuzaliwa', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Jina la Mlezi / Mzazi', 'field_key' => 'guardian_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe ya Mlezi', 'field_key' => 'guardian_email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu ya Mlezi', 'field_key' => 'guardian_phone', 'field_type' => 'tel'],
            ['label' => 'Shule Unaomba', 'field_key' => 'school_name', 'field_type' => 'text'],
            ['label' => 'Darasa / Kiwango Unaomba', 'field_key' => 'grade_applied', 'field_type' => 'text'],
            ['label' => 'Shule ya Sasa', 'field_key' => 'current_school', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Taarifa za Masomo', 'field_key' => 'upload_academic_reports', 'field_type' => 'file'],
            ['label' => 'Cheti cha Kuzaliwa', 'field_key' => 'upload_birth_certificate', 'field_type' => 'file'],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], sortOrder: 3, slug: 'school-application-assistance');

        $this->seedService($id, 'Maombi ya Ufadhili (Scholarship)', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Jina la Ufadhili / Programu', 'field_key' => 'scholarship_name', 'field_type' => 'text'],
            ['label' => 'Utaalamu', 'field_key' => 'field_of_study', 'field_type' => 'text'],
            ['label' => 'Kiwango cha Sasa cha Elimu', 'field_key' => 'education_level', 'field_type' => 'select', 'options' => ['Sekondari', 'Bachelor', 'Master', 'PhD']],
            ['label' => 'Barua ya Motivation', 'field_key' => 'motivation_letter', 'field_type' => 'textarea'],
            ['label' => 'Transcripts', 'field_key' => 'upload_transcripts', 'field_type' => 'file'],
            ['label' => 'Barua za Mapendekezo', 'field_key' => 'upload_recommendations', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 4, slug: 'scholarship-application-assistance');

        $this->seedService($id, 'Usajili wa Mitihani', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuzaliwa', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Mtihani', 'field_key' => 'exam_type', 'field_type' => 'select', 'options' => ['NECTA', 'Cambridge', 'IELTS', 'TOEFL', 'Nyingine']],
            ['label' => 'Masomo ya Mtihani', 'field_key' => 'exam_subjects', 'field_type' => 'textarea'],
            ['label' => 'Kituo Unachopendelea', 'field_key' => 'exam_center', 'field_type' => 'text'],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], sortOrder: 5, slug: 'exam-registration-assistance');
    }
}
