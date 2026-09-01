<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

class SerikaliIdentificationServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'serikali-identification')->firstOrFail()->id;

        $this->seedService($id, 'NIDA Registration / Correction', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Date of Birth', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Gender', 'field_key' => 'gender', 'field_type' => 'select', 'options' => ['Male', 'Female']],
            ['label' => 'Place of Birth (Region/District/Ward)', 'field_key' => 'place_of_birth', 'field_type' => 'text'],
            ['label' => 'Marital Status', 'field_key' => 'marital_status', 'field_type' => 'select', 'options' => ['Single', 'Married', 'Divorced', 'Widowed']],
            ...$this->contactFields(),
            ['label' => "Father's Full Name", 'field_key' => 'father_full_name', 'field_type' => 'text'],
            ['label' => "Mother's Full Name", 'field_key' => 'mother_full_name', 'field_type' => 'text'],
            ['label' => "Parents' NIDA Number", 'field_key' => 'parents_nida_number', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Residential Address', 'field_key' => 'residential_address', 'field_type' => 'text'],
            ['label' => 'Permanent Address', 'field_key' => 'permanent_address', 'field_type' => 'text'],
            ['label' => 'Birth Certificate', 'field_key' => 'upload_birth_certificate', 'field_type' => 'file'],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], sortOrder: 1);

        $this->seedService($id, 'Birth Certificate Application (RITA)', [
            ["label" => "Child's Full Name", 'field_key' => 'child_full_name', 'field_type' => 'text'],
            ['label' => 'Date of Birth', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Place of Birth', 'field_key' => 'place_of_birth', 'field_type' => 'text'],
            ["label" => "Father's Full Name", 'field_key' => 'father_full_name', 'field_type' => 'text'],
            ["label" => "Mother's Full Name", 'field_key' => 'mother_full_name', 'field_type' => 'text'],
            ["label" => "Parents' Nationality", 'field_key' => 'parents_nationality', 'field_type' => 'text'],
            ['label' => 'Clinic Card / Hospital Notification', 'field_key' => 'upload_clinic_card', 'field_type' => 'file'],
            ['label' => 'Ward Executive Officer (WEO) Letter', 'field_key' => 'upload_weo_letter', 'field_type' => 'file'],
        ], sortOrder: 2);

        $this->seedService($id, 'Death Certificate Application (RITA)', [
            ["label" => "Deceased's Full Name", 'field_key' => 'deceased_full_name', 'field_type' => 'text'],
            ['label' => 'Date of Death', 'field_key' => 'date_of_death', 'field_type' => 'date'],
            ['label' => 'Place of Death', 'field_key' => 'place_of_death', 'field_type' => 'text'],
            ['label' => 'Cause of Death (if known)', 'field_key' => 'cause_of_death', 'field_type' => 'text', 'is_required' => false],
            ["label" => "Applicant's Relationship to Deceased", 'field_key' => 'relationship_to_deceased', 'field_type' => 'text'],
            ['label' => 'Medical / Hospital Death Notification', 'field_key' => 'upload_death_notification', 'field_type' => 'file'],
            ["label" => "Applicant's ID", 'field_key' => 'upload_applicant_id', 'field_type' => 'file'],
        ], sortOrder: 3);

        $this->seedService($id, 'Police Clearance / Good Conduct', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Date of Birth', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Place of Birth', 'field_key' => 'place_of_birth', 'field_type' => 'text'],
            ['label' => 'Current Address', 'field_key' => 'current_address', 'field_type' => 'text'],
            ['label' => 'Places Resided (with dates)', 'field_key' => 'places_resided', 'field_type' => 'textarea'],
            ['label' => 'Passport Copy', 'field_key' => 'upload_passport_copy', 'field_type' => 'file'],
            ['label' => 'Application Letter', 'field_key' => 'upload_application_letter', 'field_type' => 'file'],
        ], description: 'Fingerprints are captured in person at submission — not part of the online form.', sortOrder: 6);

        $this->seedService($id, 'Loss Report', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Document Type Lost', 'field_key' => 'document_type_lost', 'field_type' => 'select', 'options' => ['NIDA', 'Passport', 'Driving Licence', 'Other']],
            ['label' => 'Document Number (if known)', 'field_key' => 'document_number', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Date of Loss', 'field_key' => 'date_of_loss', 'field_type' => 'date'],
            ['label' => 'Place of Loss', 'field_key' => 'place_of_loss', 'field_type' => 'text'],
            ['label' => 'Circumstances of Loss', 'field_key' => 'circumstances_of_loss', 'field_type' => 'textarea'],
            ['label' => 'Police Report / Sworn Declaration', 'field_key' => 'upload_police_report', 'field_type' => 'file'],
        ], sortOrder: 7);

        $this->seedService($id, 'Driving Licence Application / Renewal', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'NIDA / Passport Number', 'field_key' => 'nida_or_passport_number', 'field_type' => 'text'],
            ['label' => 'Date of Birth', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Licence Category Applied For', 'field_key' => 'licence_category', 'field_type' => 'text'],
            ['label' => 'Existing Licence Number (if renewal)', 'field_key' => 'existing_licence_number', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
            ['label' => 'Medical / Eyesight Form', 'field_key' => 'upload_medical_form', 'field_type' => 'file'],
        ], sortOrder: 8);

        $this->seedService($id, 'Vehicle-Related Applications', [
            ['label' => 'Owner Full Name', 'field_key' => 'owner_full_name', 'field_type' => 'text'],
            ['label' => 'NIDA Number', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Vehicle Registration Number', 'field_key' => 'vehicle_registration_number', 'field_type' => 'text'],
            ['label' => 'Chassis / Engine Number', 'field_key' => 'chassis_engine_number', 'field_type' => 'text'],
            ['label' => 'Vehicle Make & Model', 'field_key' => 'vehicle_make_model', 'field_type' => 'text'],
            ['label' => 'Proof of Ownership', 'field_key' => 'upload_proof_of_ownership', 'field_type' => 'file'],
            ['label' => 'TIN Certificate', 'field_key' => 'upload_tin_certificate', 'field_type' => 'file'],
        ], sortOrder: 9);


        $passportCategoryId = ServiceCategory::where('slug', 'passport-immigration')->firstOrFail()->id;

        $this->seedService($passportCategoryId, 'New Passport Application', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Date of Birth', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Place of Birth', 'field_key' => 'place_of_birth', 'field_type' => 'text'],
            ['label' => 'Gender', 'field_key' => 'gender', 'field_type' => 'select', 'options' => ['Male', 'Female']],
            ['label' => 'NIDA Number', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Nationality', 'field_key' => 'nationality', 'field_type' => 'text'],
            ['label' => 'Home Address', 'field_key' => 'home_address', 'field_type' => 'text'],
            ['label' => 'Occupation', 'field_key' => 'occupation', 'field_type' => 'text'],
            ['label' => 'Birth Certificate', 'field_key' => 'upload_birth_certificate', 'field_type' => 'file'],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], description: 'Assistance with a first-time passport application.', sortOrder: 1);

        $this->seedService($passportCategoryId, 'Passport Renewal', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Date of Birth', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'NIDA Number', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Current Passport Number', 'field_key' => 'passport_number', 'field_type' => 'text'],
            ['label' => 'Passport Expiry Date', 'field_key' => 'passport_expiry_date', 'field_type' => 'date'],
            ['label' => 'Home Address', 'field_key' => 'home_address', 'field_type' => 'text'],
            ['label' => 'Old Passport', 'field_key' => 'upload_old_passport', 'field_type' => 'file'],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], description: 'Assistance with renewing an existing passport.', sortOrder: 2);

        $this->seedService($passportCategoryId, 'Passport Update / Correction', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'NIDA Number', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Current Passport Number', 'field_key' => 'passport_number', 'field_type' => 'text'],
            ['label' => 'Information to Update', 'field_key' => 'information_to_update', 'field_type' => 'textarea'],
            ['label' => 'Supporting Document', 'field_key' => 'upload_supporting_document', 'field_type' => 'file'],
            ['label' => 'Current Passport', 'field_key' => 'upload_current_passport', 'field_type' => 'file'],
        ], description: 'Assistance with correcting or updating passport information.', sortOrder: 3);

        $this->seedService($passportCategoryId, 'Visa Application', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Passport Number', 'field_key' => 'passport_number', 'field_type' => 'text'],
            ['label' => 'Passport Expiry Date', 'field_key' => 'passport_expiry_date', 'field_type' => 'date'],
            ['label' => 'Nationality', 'field_key' => 'nationality', 'field_type' => 'text'],
            ['label' => 'Purpose of Visit', 'field_key' => 'purpose_of_visit', 'field_type' => 'text'],
            ['label' => 'Intended Arrival Date', 'field_key' => 'arrival_date', 'field_type' => 'date'],
            ['label' => 'Intended Departure Date', 'field_key' => 'departure_date', 'field_type' => 'date'],
            ['label' => 'Accommodation Details', 'field_key' => 'accommodation_details', 'field_type' => 'textarea'],
            ['label' => 'Passport Bio-data Page', 'field_key' => 'upload_passport_biodata', 'field_type' => 'file'],
            ['label' => 'Passport Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], description: 'Assistance with visa applications.', sortOrder: 4);

        $this->seedService($passportCategoryId, 'Residence / Immigration Application', [
            ['label' => 'Full Name', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Nationality', 'field_key' => 'nationality', 'field_type' => 'text'],
            ['label' => 'Passport Number', 'field_key' => 'passport_number', 'field_type' => 'text'],
            ['label' => 'Purpose of Residence', 'field_key' => 'purpose_of_residence', 'field_type' => 'select', 'options' => ['Work', 'Investment', 'Study', 'Other']],
            ['label' => 'Sponsoring Employer / Institution', 'field_key' => 'sponsoring_institution', 'field_type' => 'text'],
            ['label' => 'Passport Copy', 'field_key' => 'upload_passport_copy', 'field_type' => 'file'],
            ['label' => 'Work Permit (if applicable)', 'field_key' => 'upload_work_permit', 'field_type' => 'file', 'is_required' => false],
        ], description: 'Assistance with residence and immigration applications.', sortOrder: 5);


        // Retire legacy passport records created by the previous flat catalogue.
        Service::whereIn('slug', [
            'passport-application',
            'visa-application',
            'residence-immigration-applications',
        ])->update(['is_active' => false]);
    }
}
