<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/**
 * Serikali, kitambulisho, pasipoti na uhamiaji.
 *
 * Sehemu zinazohitajika zinatoka kwenye maelekezo rasmi ya Digital Star
 * (NIDA, cheti cha kuzaliwa/kifo, pasipoti). Slug hubaki thabiti ili maombi
 * ya wateja yaliyopo yasipotee.
 */
class SerikaliIdentificationServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'serikali-identification')->firstOrFail()->id;

        // -----------------------------------------------------------------
        // Nambari ya Utambulisho wa Taifa (NIDA)
        // -----------------------------------------------------------------
        $this->seedService($id, 'Nambari ya Utambulisho wa Taifa (NIDA)', [
            ['label' => 'Majina Kamili (kama yanavyoonekana kwenye cheti cha kuzaliwa)', 'field_key' => 'full_name', 'field_type' => 'text'],

            ['label' => 'Mahali pa Kuzaliwa — Mkoa', 'field_key' => 'birth_region', 'field_type' => 'text'],
            ['label' => 'Mahali pa Kuzaliwa — Wilaya', 'field_key' => 'birth_district', 'field_type' => 'text'],
            ['label' => 'Mahali pa Kuzaliwa — Kata', 'field_key' => 'birth_ward', 'field_type' => 'text'],
            ['label' => 'Mahali pa Kuzaliwa — Kijiji / Mtaa', 'field_key' => 'birth_village', 'field_type' => 'text'],

            ['label' => 'Uhai wa Mwombaji', 'field_key' => 'applicant_alive', 'field_type' => 'select', 'options' => ['Hai', 'Ameaga dunia']],

            ['label' => 'Unapoishi Sasa — Mkoa', 'field_key' => 'current_region', 'field_type' => 'text'],
            ['label' => 'Unapoishi Sasa — Wilaya', 'field_key' => 'current_district', 'field_type' => 'text'],
            ['label' => 'Unapoishi Sasa — Kata', 'field_key' => 'current_ward', 'field_type' => 'text'],
            ['label' => 'Unapoishi Sasa — Kijiji / Mtaa', 'field_key' => 'current_village', 'field_type' => 'text'],

            ['label' => 'Anwani ya Makazi', 'field_key' => 'residential_address', 'field_type' => 'text'],
            ['label' => 'Namba ya Nyumba', 'field_key' => 'house_number', 'field_type' => 'text'],

            ['label' => 'Kitambulisho (NIDA / Kura / Ubatizo / Kadi ya Kliniki / Leaving ya Darasa la Saba au Sekondari)', 'field_key' => 'identity_document_type', 'field_type' => 'select', 'options' => ['Kitambulisho cha NIDA', 'Kitambulisho cha Uchaguzi', 'Cheti cha Ubatizo', 'Kadi ya Kliniki', 'Cheti cha Darasa la Saba', 'Cheti cha Sekondari']],
            ['label' => 'Jina la Shule ya Msingi Uliposoma', 'field_key' => 'primary_school', 'field_type' => 'text'],
            ['label' => 'Mwaka Uliyomaliza Shule ya Msingi', 'field_key' => 'primary_school_year', 'field_type' => 'text'],

            ['label' => 'Kazi / Cheo', 'field_key' => 'occupation', 'field_type' => 'text'],
            ...$this->contactFields(),

            // ---- Wazazi ----
            ['label' => 'Jina Kamili la Baba', 'field_key' => 'father_full_name', 'field_type' => 'text'],
            ['label' => 'Jina Kamili la Mama', 'field_key' => 'mother_full_name', 'field_type' => 'text'],
            ['label' => 'Walipozaliwa — Tarehe', 'field_key' => 'parents_birth_date', 'field_type' => 'date'],
            ['label' => 'Wanapoishi — Mkoa, Wilaya, Kata, Kijiji / Mtaa', 'field_key' => 'parents_residence', 'field_type' => 'text'],
            ['label' => 'Anwani ya Makazi ya Wazazi', 'field_key' => 'parents_address', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Namba ya Nyumba ya Wazazi', 'field_key' => 'parents_house_number', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Namba ya NIDA ya Wazazi', 'field_key' => 'parents_nida_number', 'field_type' => 'text'],
            ['label' => 'Cheti cha Kuzaliwa cha Mzazi / Kiapo (Baba)', 'field_key' => 'upload_father_birth_certificate', 'field_type' => 'file'],
            ['label' => 'Kazi / Cheo cha Wazazi', 'field_key' => 'parents_occupation', 'field_type' => 'text', 'is_required' => false],

            ['label' => 'Uraia', 'field_key' => 'citizenship', 'field_type' => 'select', 'options' => ['Raia wa Kuzaliwa', 'Raia wa Kuandikishwa']],

            ['label' => 'Cheti cha Kuzaliwa', 'field_key' => 'upload_birth_certificate', 'field_type' => 'file'],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
            ['label' => 'Barua ya Utambulisho wa Makazi', 'field_key' => 'upload_residence_letter', 'field_type' => 'file'],
        ], description: 'Usaidizi wa kujaza na kuwasilisha maombi ya namba ya utambulisho wa taifa. Gharama ni TSh 20,000/= na mchakato huchukua takribani wiki mbili.', sortOrder: 1, slug: 'nida-registration-correction', price: 20000);

        // -----------------------------------------------------------------
        // Cheti cha Kuzaliwa (RITA)
        // -----------------------------------------------------------------
        $this->seedService($id, 'Cheti cha Kuzaliwa (RITA)', [
            ['label' => 'Majina Kamili ya Mtoto', 'field_key' => 'child_full_name', 'field_type' => 'text'],
            ['label' => 'Majina Kamili ya Mwombaji', 'field_key' => 'applicant_full_name', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuzaliwa', 'field_key' => 'date_of_birth', 'field_type' => 'date'],

            ['label' => 'Mahali pa Kuzaliwa — Mkoa', 'field_key' => 'birth_region', 'field_type' => 'text'],
            ['label' => 'Mahali pa Kuzaliwa — Wilaya', 'field_key' => 'birth_district', 'field_type' => 'text'],
            ['label' => 'Mahali pa Kuzaliwa — Kata', 'field_key' => 'birth_ward', 'field_type' => 'text'],
            ['label' => 'Mahali pa Kuzaliwa — Kijiji / Mtaa', 'field_key' => 'birth_village', 'field_type' => 'text'],

            ['label' => 'Jina la Hospitali Ulipozaliwa', 'field_key' => 'birth_hospital', 'field_type' => 'text'],
            ['label' => 'Mzaliwa wa Pacha au Mtoto wa Kwanza', 'field_key' => 'birth_order', 'field_type' => 'select', 'options' => ['Mzaliwa wa Pacha', 'Mtoto wa Kawaida']],

            ['label' => 'Uhai wa Mtoto', 'field_key' => 'child_alive', 'field_type' => 'select', 'options' => ['Hai', 'Ameaga dunia']],
            ['label' => 'Unapoishi Sasa — Mkoa, Wilaya, Kata, Kijiji / Mtaa', 'field_key' => 'current_residence', 'field_type' => 'text'],
            ['label' => 'Anwani ya Makazi', 'field_key' => 'residential_address', 'field_type' => 'text'],
            ['label' => 'Kazi / Cheo', 'field_key' => 'occupation', 'field_type' => 'text', 'is_required' => false],
            ...$this->contactFields(),

            // ---- Wazazi ----
            ['label' => 'Jina Kamili la Baba', 'field_key' => 'father_full_name', 'field_type' => 'text'],
            ['label' => 'Jina Kamili la Mama', 'field_key' => 'mother_full_name', 'field_type' => 'text'],
            ['label' => 'Uhai wa Wazazi', 'field_key' => 'parents_alive', 'field_type' => 'select', 'options' => ['Wote Hai', 'Baba Ameaga', 'Mama Ameaga', 'Wote Wameaga']],
            ['label' => 'Wanapoishi — Mkoa, Wilaya, Kata, Kijiji / Mtaa', 'field_key' => 'parents_residence', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuzaliwa ya Wazazi', 'field_key' => 'parents_birth_date', 'field_type' => 'date'],
            ['label' => 'Anwani ya Makazi ya Wazazi', 'field_key' => 'parents_address', 'field_type' => 'text'],
            ['label' => 'Kazi / Cheo cha Wazazi', 'field_key' => 'parents_occupation', 'field_type' => 'text'],
            ['label' => 'Kitambulisho cha Wazazi / Mzazi cha NIDA', 'field_key' => 'parents_nida_number', 'field_type' => 'text'],
            ...array_map(fn ($k) => ['label' => 'Namba ya Simu', 'field_key' => $k, 'field_type' => 'tel', 'is_required' => false], ['father_phone', 'mother_phone']),

            ['label' => 'Vielelezo vya Kuzaliwa', 'field_key' => 'upload_birth_supporting_docs', 'field_type' => 'file'],
            ['label' => 'Kitambulisho cha Mwombaji', 'field_key' => 'upload_applicant_id', 'field_type' => 'file'],
        ], description: 'Cheti cha kuzaliwa hutolewa ndani ya wiki moja au mbili. Gharama ni TSh 40,000/= kwa mtoto wa miaka 10 na kuendelea, au TSh 28,000/= chini ya miaka 10.', sortOrder: 2, slug: 'birth-certificate-application-rita', price: 40000);

        // -----------------------------------------------------------------
        // Cheti cha Kifo (RITA)
        // -----------------------------------------------------------------
        $this->seedService($id, 'Cheti cha Kifo (RITA)', [
            ['label' => 'Majina Kamili ya Marehemu', 'field_key' => 'deceased_full_name', 'field_type' => 'text'],

            ['label' => 'Mahali pa Kuzaliwa — Mkoa, Wilaya, Kata, Kijiji / Mtaa', 'field_key' => 'deceased_birth_place', 'field_type' => 'text'],

            ['label' => 'Mkoa ulipootokea msiba', 'field_key' => 'death_region', 'field_type' => 'text'],
            ['label' => 'Wilaya ulipootokea msiba', 'field_key' => 'death_district', 'field_type' => 'text'],
            ['label' => 'Kata ulipootokea msiba', 'field_key' => 'death_ward', 'field_type' => 'text'],
            ['label' => 'Kijiji / Mtaa ulipootokea msiba', 'field_key' => 'death_village', 'field_type' => 'text'],

            ['label' => 'Jina la Hospitali Msiba Ulipotokea', 'field_key' => 'death_hospital', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Tarehe ya Kifo', 'field_key' => 'date_of_death', 'field_type' => 'date'],
            ['label' => 'Anwani ya Makazi', 'field_key' => 'residential_address', 'field_type' => 'text'],
            ['label' => 'Kazi / Cheo', 'field_key' => 'occupation', 'field_type' => 'text', 'is_required' => false],

            // ---- Wazazi ----
            ['label' => 'Jina Kamili la Baba', 'field_key' => 'father_full_name', 'field_type' => 'text'],
            ['label' => 'Jina Kamili la Mama', 'field_key' => 'mother_full_name', 'field_type' => 'text'],
            ['label' => 'Uhai wa Wazazi', 'field_key' => 'parents_alive', 'field_type' => 'select', 'options' => ['Wote Hai', 'Baba Ameaga', 'Mama Ameaga', 'Wote Wameaga']],
            ['label' => 'Wanapoishi — Mkoa, Wilaya, Kata, Kijiji / Mtaa', 'field_key' => 'parents_residence', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuzaliwa ya Wazazi', 'field_key' => 'parents_birth_date', 'field_type' => 'date'],
            ['label' => 'Anwani ya Makazi ya Wazazi', 'field_key' => 'parents_address', 'field_type' => 'text'],
            ['label' => 'Kazi / Cheo cha Wazazi', 'field_key' => 'parents_occupation', 'field_type' => 'text'],

            ['label' => 'Mwombaji ni Nani kwa Marehemu', 'field_key' => 'relationship_to_deceased', 'field_type' => 'text'],
            ...$this->contactFields(),

            ['label' => 'Vielelezo Vinavyohitajika (weka alama ya ✓ kwenye vilivyokuleta)', 'field_key' => 'attachments', 'field_type' => 'checkbox', 'is_required' => false, 'help_text' => 'Vitambulisho vya wanandugu, barua ya mtendaji, kibali cha mazishi, cheti cha kuzaliwa cha marehemu, cheti cha kuzaliwa cha mzazi, kitambulisho cha mwombaji, barua ya makazi na muhtasari wa kikao.'],
            ['label' => 'Barua ya Mtendaji ya Kuthibitisha Kifo', 'field_key' => 'upload_weo_death_letter', 'field_type' => 'file'],
            ['label' => 'Kibali cha Mazishi', 'field_key' => 'upload_burial_permit', 'field_type' => 'file'],
            ['label' => 'Cheti cha Kuzaliwa cha Marehemu / Kiapo', 'field_key' => 'upload_deceased_birth_certificate', 'field_type' => 'file'],
            ['label' => 'Cheti cha Kuzaliwa cha Wazazi / Kiapo', 'field_key' => 'upload_parent_birth_certificate', 'field_type' => 'file'],
            ['label' => 'Kitambulisho cha Mwombaji (NIDA / Kura / Pasipoti)', 'field_key' => 'upload_applicant_id', 'field_type' => 'file'],
        ], description: 'Gharama ni TSh 40,000/= kuanzia miaka 10 kutoka kifo kutokea, au TSh 28,000/= chini ya miaka 10.', sortOrder: 3, slug: 'death-certificate-application-rita', price: 40000);

        // -----------------------------------------------------------------
        // Huduma zilizopo — zimehindiliwa kwa Kiswahili
        // -----------------------------------------------------------------
        $this->seedService($id, 'Uthibitisho wa Tabia Nzuri (Polisi)', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuzaliwa', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Mahali pa Kuzaliwa', 'field_key' => 'place_of_birth', 'field_type' => 'text'],
            ['label' => 'Anwani ya Sasa', 'field_key' => 'current_address', 'field_type' => 'text'],
            ['label' => 'Makazi Uliyowahi Kukaa (pamoja na tarehe)', 'field_key' => 'places_resided', 'field_type' => 'textarea'],
            ['label' => 'Nakala ya Pasipoti', 'field_key' => 'upload_passport_copy', 'field_type' => 'file'],
            ['label' => 'Barua ya Maombi', 'field_key' => 'upload_application_letter', 'field_type' => 'file'],
        ], description: 'Pigwa vidole pale kwenye ofisi baada ya kuwasilisha maombi — si sehemu ya fomu ya mtandaoni.', sortOrder: 4, slug: 'police-clearance-good-conduct');

        $this->seedService($id, 'Ripoti ya Upotevu wa Nyaraka', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Aina ya Nyaraka Iliyopotea', 'field_key' => 'document_type_lost', 'field_type' => 'select', 'options' => ['NIDA', 'Pasipoti', 'Leseni ya Udereva', 'Nyingine']],
            ['label' => 'Namba ya Nyaraka (ikijulikana)', 'field_key' => 'document_number', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Tarehe ya Upotevu', 'field_key' => 'date_of_loss', 'field_type' => 'date'],
            ['label' => 'Mahali Ilikopotea', 'field_key' => 'place_of_loss', 'field_type' => 'text'],
            ['label' => 'Maelezo ya Tukio', 'field_key' => 'circumstances_of_loss', 'field_type' => 'textarea'],
            ['label' => 'Ripoti ya Polisi / Tamko la Kiapo', 'field_key' => 'upload_police_report', 'field_type' => 'file'],
        ], sortOrder: 5, slug: 'loss-report');

        $this->seedService($id, 'Leseni ya Udereva — Maombi au Ufuatishaji', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya NIDA / Pasipoti', 'field_key' => 'nida_or_passport_number', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuzaliwa', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Aina ya Leseni Unaomba', 'field_key' => 'licence_category', 'field_type' => 'text'],
            ['label' => 'Namba ya Leseni ya Zamani (kama ni fuatishaji)', 'field_key' => 'existing_licence_number', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
            ['label' => 'Fomu ya Matibabu / Maono', 'field_key' => 'upload_medical_form', 'field_type' => 'file'],
        ], sortOrder: 6, slug: 'driving-licence-application-renewal');

        $this->seedService($id, 'Maombi Yanayohusiana na Magari', [
            ['label' => 'Majina Kamili ya Mmiliki', 'field_key' => 'owner_full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya NIDA', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Namba ya Usajili wa Gari', 'field_key' => 'vehicle_registration_number', 'field_type' => 'text'],
            ['label' => 'Namba ya Chassis / Injini', 'field_key' => 'chassis_engine_number', 'field_type' => 'text'],
            ['label' => 'Aina na Modeli ya Gari', 'field_key' => 'vehicle_make_model', 'field_type' => 'text'],
            ['label' => 'Ushahidi wa Umiliki', 'field_key' => 'upload_proof_of_ownership', 'field_type' => 'file'],
            ['label' => 'Cheti cha TIN', 'field_key' => 'upload_tin_certificate', 'field_type' => 'file'],
        ], sortOrder: 7, slug: 'vehicle-related-applications');

        // -----------------------------------------------------------------
        // Usajili wa Kadi ya NHIF (Huduma za Serikali na Mtandaoni)
        // -----------------------------------------------------------------
        $nhifCategoryId = ServiceCategory::where('slug', 'online-government-services')->firstOrFail()->id;

        $this->seedService($nhifCategoryId, 'Usajili wa Kadi ya NHIF', [
            ['label' => 'Majina yako kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya nida', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Email yako', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya simu', 'field_key' => 'phone', 'field_type' => 'tel'],
        ], description: 'Usaidizi wa kuwa na kadi ya NHIF (National Health Insurance Fund) kwa afya ya kutengenezwa na malizia.', sortOrder: 1, slug: 'nhif-registration');

        // -----------------------------------------------------------------
        // Pasipoti na uhamiaji
        // -----------------------------------------------------------------
        $passportCategoryId = ServiceCategory::where('slug', 'passport-immigration')->firstOrFail()->id;

        $this->seedService($passportCategoryId, 'Maombi ya Pasipoti Mpya', [
            ['label' => 'Majina Kamili (Jina, Jina la Kati, Jina la Ukoo)', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Jina Maarufu (kama linatumika)', 'field_key' => 'common_name', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Tarehe ya Kuzaliwa', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Mahali pa Kuzaliwa (Mkoa / Wilaya)', 'field_key' => 'place_of_birth', 'field_type' => 'text'],
            ['label' => 'Jinsia', 'field_key' => 'gender', 'field_type' => 'select', 'options' => ['Mwanaume', 'Mwanamke']],
            ['label' => 'Uraia', 'field_key' => 'nationality', 'field_type' => 'text', 'default_value' => 'Mtanzania'],
            ['label' => 'Namba ya NIDA', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Barua Pepe (si lazima)', 'field_key' => 'email', 'field_type' => 'email', 'is_required' => false],
            ['label' => 'Anwani ya Makazi (Kimwili)', 'field_key' => 'home_address', 'field_type' => 'text'],
            ['label' => 'Anwani ya Posta (kama ni tofauti)', 'field_key' => 'postal_address', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Kazi / Cheo', 'field_key' => 'occupation', 'field_type' => 'text'],
            ['label' => 'Mwajiri / Jina la Kampuni (kama ipo)', 'field_key' => 'employer', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Hali ya Ndoa', 'field_key' => 'marital_status', 'field_type' => 'select', 'options' => ['Sijioa', 'Nimeoa', 'Nimetengana', 'Mjane']],
            ['label' => 'Namba ya Nyumba', 'field_key' => 'house_number', 'field_type' => 'text'],

            ['label' => 'Jina Kamili la Baba', 'field_key' => 'father_full_name', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuzaliwa ya Baba', 'field_key' => 'father_birth_date', 'field_type' => 'date'],
            ['label' => 'Mahali pa Kuzaliwa cha Baba (Mkoa / Wilaya)', 'field_key' => 'father_birth_place', 'field_type' => 'text'],
            ['label' => 'Uraia wa Baba', 'field_key' => 'father_nationality', 'field_type' => 'text'],
            ['label' => 'Kazi / Cheo cha Baba', 'field_key' => 'father_occupation', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu ya Baba', 'field_key' => 'father_phone', 'field_type' => 'tel'],

            ['label' => 'Jina Kamili la Mama', 'field_key' => 'mother_full_name', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuzaliwa ya Mama', 'field_key' => 'mother_birth_date', 'field_type' => 'date'],
            ['label' => 'Mahali pa Kuzaliwa cha Mama (Mkoa / Wilaya)', 'field_key' => 'mother_birth_place', 'field_type' => 'text'],
            ['label' => 'Uraia wa Mama', 'field_key' => 'mother_nationality', 'field_type' => 'text'],
            ['label' => 'Kazi / Cheo cha Mama', 'field_key' => 'mother_occupation', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu ya Mama', 'field_key' => 'mother_phone', 'field_type' => 'tel'],

            ['label' => 'Nchi Utakayosafiria', 'field_key' => 'destination_country', 'field_type' => 'text'],
            ['label' => 'Madhumuni ya Safari', 'field_key' => 'purpose_of_travel', 'field_type' => 'select', 'options' => ['Kusoma', 'Kazi', 'Matibabu', 'Ndoa', 'Dini', 'Kutembea']],
            ['label' => 'Muda Utakaokaa Nje ya Nchi', 'field_key' => 'duration_abroad', 'field_type' => 'text'],
            ['label' => 'Je, una visa nyinginezo?', 'field_key' => 'has_other_visas', 'field_type' => 'select', 'options' => ['Ndiyo', 'Hapana']],
            ['label' => 'Kama Ndiyo, taja nchi', 'field_key' => 'other_visa_countries', 'field_type' => 'text', 'is_required' => false],

            ['label' => 'Jina Kamili la Mdhamini 1', 'field_key' => 'guarantor1_name', 'field_type' => 'text'],
            ['label' => 'Anwani ya Mdhamini 1', 'field_key' => 'guarantor1_address', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu ya Mdhamini 1', 'field_key' => 'guarantor1_phone', 'field_type' => 'tel'],
            ['label' => 'Uhusiano Wako na Mdhamini 1', 'field_key' => 'guarantor1_relationship', 'field_type' => 'text'],
            ['label' => 'Jina Kamili la Mdhamini 2', 'field_key' => 'guarantor2_name', 'field_type' => 'text'],
            ['label' => 'Anwani ya Mdhamini 2', 'field_key' => 'guarantor2_address', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu ya Mdhamini 2', 'field_key' => 'guarantor2_phone', 'field_type' => 'tel'],
            ['label' => 'Uhusiano Wako na Mdhamini 2', 'field_key' => 'guarantor2_relationship', 'field_type' => 'text'],

            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
            ['label' => 'Cheti cha Kuzaliwa', 'field_key' => 'upload_birth_certificate', 'field_type' => 'file'],
            ['label' => 'Cheti cha Kuzaliwa cha Mzazi / Kiapo', 'field_key' => 'upload_parent_birth_certificate', 'field_type' => 'file', 'is_required' => false],
            ['label' => 'Vyeti vya Elimu', 'field_key' => 'upload_education_certificates', 'field_type' => 'file'],
            ['label' => 'Barua ya Uthibitisho wa Makazi', 'field_key' => 'upload_residence_letter', 'field_type' => 'file'],
        ], description: 'Pasipoti ya miaka 10 huchukua wiki 2 hadi 3. Gharama ni TSh 200,000/= pamoja na malipo ya serikali.', sortOrder: 1, slug: 'new-passport-application', price: 200000);

        $this->seedService($passportCategoryId, 'Kusasisha Pasipoti', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuzaliwa', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Namba ya NIDA', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Namba ya Pasipoti ya Sasa', 'field_key' => 'passport_number', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuisha kwa Pasipoti', 'field_key' => 'passport_expiry_date', 'field_type' => 'date'],
            ['label' => 'Anwani ya Makazi', 'field_key' => 'home_address', 'field_type' => 'text'],
            ['label' => 'Pasipoti ya Zamani', 'field_key' => 'upload_old_passport', 'field_type' => 'file'],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], description: 'Usaidizi wa kusasisha pasipoti iliyopo.', sortOrder: 2, slug: 'passport-renewal');

        $this->seedService($passportCategoryId, 'Marekebisho ya Pasipoti', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya NIDA', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Namba ya Pasipoti ya Sasa', 'field_key' => 'passport_number', 'field_type' => 'text'],
            ['label' => 'Taarifa Zinazotakiwa Kurekebishwa', 'field_key' => 'information_to_update', 'field_type' => 'textarea'],
            ['label' => 'Nyaraka ya Msaada', 'field_key' => 'upload_supporting_document', 'field_type' => 'file'],
            ['label' => 'Pasipoti ya Sasa', 'field_key' => 'upload_current_passport', 'field_type' => 'file'],
        ], description: 'Usaidizi wa kurekebisha au kusasisha taarifa za pasipoti.', sortOrder: 3, slug: 'passport-update-correction');

        $this->seedService($passportCategoryId, 'Maombi ya Visa', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya Pasipoti', 'field_key' => 'passport_number', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuisha kwa Pasipoti', 'field_key' => 'passport_expiry_date', 'field_type' => 'date'],
            ['label' => 'Uraia', 'field_key' => 'nationality', 'field_type' => 'text'],
            ['label' => 'Madhumuni ya Ziara', 'field_key' => 'purpose_of_visit', 'field_type' => 'text'],
            ['label' => 'Tarehe Inayokusudiwa ya Kufika', 'field_key' => 'arrival_date', 'field_type' => 'date'],
            ['label' => 'Tarehe Inayokusudiwa ya Kurudi', 'field_key' => 'departure_date', 'field_type' => 'date'],
            ['label' => 'Maelezo ya Malazi', 'field_key' => 'accommodation_details', 'field_type' => 'textarea'],
            ['label' => 'Ukurasa wa Kitambulisho cha Pasipoti', 'field_key' => 'upload_passport_biodata', 'field_type' => 'file'],
            ['label' => 'Passport Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], description: 'Usaidizi wa maombi ya visa.', sortOrder: 4, slug: 'visa-application');

        $this->seedService($passportCategoryId, 'Maombi ya Leseni ya Kukaa (Uhamiaji)', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Uraia', 'field_key' => 'nationality', 'field_type' => 'text'],
            ['label' => 'Namba ya Pasipoti', 'field_key' => 'passport_number', 'field_type' => 'text'],
            ['label' => 'Madhumuni ya Kukaa', 'field_key' => 'purpose_of_residence', 'field_type' => 'select', 'options' => ['Kazi', 'Uwekezaji', 'Kusoma', 'Nyingine']],
            ['label' => 'Taasisi / Mwajiri Anayesimamia', 'field_key' => 'sponsoring_institution', 'field_type' => 'text'],
            ['label' => 'Nakala ya Pasipoti', 'field_key' => 'upload_passport_copy', 'field_type' => 'file'],
            ['label' => 'Leseni ya Kufanya Kazi (ikihusika)', 'field_key' => 'upload_work_permit', 'field_type' => 'file', 'is_required' => false],
        ], description: 'Usaidizi wa maombi ya leseni ya kukaa na uhamiaji.', sortOrder: 5, slug: 'residence-immigration-application');

        // Retire legacy passport records created by the previous flat catalogue.
        // NOTE: 'visa-application' used to live in this list even though it was
        // actively seeded, which silently switched the visa service off. It is
        // a live service now, so only the true legacy slugs are retired here.
        Service::whereIn('slug', [
            'passport-application',
            'residence-immigration-applications',
        ])->update(['is_active' => false]);
    }
}
