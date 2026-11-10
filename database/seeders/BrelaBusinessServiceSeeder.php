<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/**
 * BRELA na usajili wa biashara / kampuni.
 *
 * Sehemu zinazohitajika zinatoka kwenye maelekezo rasmi ya Digital Star ya
 * usajili wa jina la biashara na usajili wa kampuni.
 */
class BrelaBusinessServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'brela-business')->firstOrFail()->id;

        // -----------------------------------------------------------------
        // Usajili wa jina la biashara (BRELA)
        // -----------------------------------------------------------------
        $this->seedService($id, 'Usajili wa Jina la Biashara (BRELA)', [
            ['label' => 'Jina Kamili la Biashara Unalotaka', 'field_key' => 'business_name', 'field_type' => 'text'],
            ['label' => 'Jina Nyingine Ungependa Kuhakiki (backup)', 'field_key' => 'backup_business_name', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Namba ya NIDA ya Mfanya Biashara', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],

            ['label' => 'Eneo Mfanya Biashara Anapoishi — Mkoa', 'field_key' => 'owner_region', 'field_type' => 'text'],
            ['label' => 'Eneo Mfanya Biashara Anapoishi — Wilaya', 'field_key' => 'owner_district', 'field_type' => 'text'],
            ['label' => 'Eneo Mfanya Biashara Anapoishi — Kata', 'field_key' => 'owner_ward', 'field_type' => 'text'],
            ['label' => 'Eneo Mfanya Biashara Anapoishi — Mtaa', 'field_key' => 'owner_street', 'field_type' => 'text'],
            ['label' => 'Anwani ya Makazi ya Mfanya Biashara', 'field_key' => 'owner_residential_address', 'field_type' => 'text'],

            ['label' => 'Eneo Biashara Ilikopo — Mkoa', 'field_key' => 'business_region', 'field_type' => 'text'],
            ['label' => 'Eneo Biashara Ilikopo — Wilaya', 'field_key' => 'business_district', 'field_type' => 'text'],
            ['label' => 'Eneo Biashara Ilikopo — Kata', 'field_key' => 'business_ward', 'field_type' => 'text'],
            ['label' => 'Eneo Biashara Ilikopo — Mtaa', 'field_key' => 'business_street', 'field_type' => 'text'],
            ['label' => 'Anwani Kamili ya Eneo la Biashara', 'field_key' => 'business_address', 'field_type' => 'text'],
            ['label' => 'Alama Maarufu ya Eneo la Biashara', 'field_key' => 'business_landmark', 'field_type' => 'text'],

            ['label' => 'Umiliki wa Biashara — Idadi ya Watu', 'field_key' => 'ownership_partner_count', 'field_type' => 'select', 'options' => ['Mtu mmoja', 'Watu wawili', 'Watu watatu', 'Zaidi ya watatu']],
            ['label' => 'Mfumo wa Makubaliano ya Umiliki', 'field_key' => 'ownership_agreement', 'field_type' => 'select', 'options' => ['Mtu binafsi', 'Ushirikiano (Partnership)', 'Kampuni']],

            ...$this->contactFields(),

            ['label' => 'Nakala ya Kitambulisho cha NIDA', 'field_key' => 'upload_nida_copy', 'field_type' => 'file'],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], description: 'Gharama za kukusaidia kusajili jina la biashara yako ni TSh 100,000/= tu. Tunafanya ukaguzi wa jina na kuwasilisha maombi hadi kupata cheti.', sortOrder: 1, slug: 'business-registration-brela', price: 100000);

        // -----------------------------------------------------------------
        // Usajili wa kampuni (BRELA)
        // -----------------------------------------------------------------
        $this->seedService($id, 'Usajili wa Kampuni (BRELA)', [
            ['label' => 'Jina la Kampuni', 'field_key' => 'company_name', 'field_type' => 'text'],
            ['label' => 'Aina ya Kampuni', 'field_key' => 'company_type', 'field_type' => 'select', 'options' => ['Limited Company', 'Unlimited Company']],

            // ---- Mmiliki ----
            ['label' => 'Mmiliki — Namba ya NIDA', 'field_key' => 'owner_nida_number', 'field_type' => 'text'],
            ['label' => 'Mmiliki — Namba ya Simu', 'field_key' => 'owner_phone', 'field_type' => 'tel'],
            ['label' => 'Mmiliki — Barua Pepe', 'field_key' => 'owner_email', 'field_type' => 'email'],
            ['label' => 'Mmiliki — Anwani ya Makazi', 'field_key' => 'owner_residential_address', 'field_type' => 'text'],
            ['label' => 'Mmiliki — Eneo / Barabara / Jengo Maarufu karibu na makazi', 'field_key' => 'owner_landmark', 'field_type' => 'text'],

            // ---- Director ----
            ['label' => 'Director — Majina Kamili', 'field_key' => 'director_name', 'field_type' => 'text'],
            ['label' => 'Director — Namba ya NIDA', 'field_key' => 'director_nida_number', 'field_type' => 'text'],
            ['label' => 'Director — Namba ya Simu', 'field_key' => 'director_phone', 'field_type' => 'tel'],
            ['label' => 'Director — Barua Pepe', 'field_key' => 'director_email', 'field_type' => 'email'],
            ['label' => 'Director — Anwani ya Makazi', 'field_key' => 'director_residential_address', 'field_type' => 'text'],
            ['label' => 'Director — Eneo / Barabara / Jengo Maarufu karibu na makazi', 'field_key' => 'director_landmark', 'field_type' => 'text'],

            // ---- Biashara ----
            ['label' => 'Jina la Biashara', 'field_key' => 'trading_name', 'field_type' => 'text'],
            ['label' => 'Anwani ya Eneo la Biashara', 'field_key' => 'business_address', 'field_type' => 'text'],
            ['label' => 'Eneo / Barabara / Jengo Maarufu karibu na ofisi ya biashara', 'field_key' => 'business_landmark', 'field_type' => 'text'],
            ['label' => 'Shughuli / Huduma Zinazofanywa na Biashara', 'field_key' => 'business_activities', 'field_type' => 'textarea'],

            // ---- Viambatanisho ----
            ['label' => 'Memorandum (inaonyesha mtaji, huduma, wanahisa na idadi ya hisa)', 'field_key' => 'upload_memorandum', 'field_type' => 'file'],
            ['label' => 'Articles of Association (inaonyesha mtaji, mgawanyo wa hisa na mpango wa uendeshaji)', 'field_key' => 'upload_articles', 'field_type' => 'file'],
            ['label' => 'Tunahitaji kuandaa Memorandum & Articles kwa ajili yako?', 'field_key' => 'needs_draft_documents', 'field_type' => 'select', 'options' => ['Ndiyo, tafadhali andaa', 'Tayari nina']],

            ...$this->contactFields(),
        ], description: 'Gharama ya Memorandum & Article of Association ni TSh 1,000,000/= tu — ikiwemo maombi, malipo ya serikali na usimamizi wa usajili kukamilika ndani ya muda. Gharama za usajili pamoja na TIN, Leseni na EFD Mashine ni TSh 2,000,000/= tu.', sortOrder: 2, slug: 'company-registration-brela', price: 1000000);

        // -----------------------------------------------------------------
        // Huduma zilizopo — kwa Kiswahili
        // -----------------------------------------------------------------
        $this->seedService($id, 'Utafutaji na Kuhifadhi Jina la Biashara', [
            ['label' => 'Jina la Biashara Unalotaka', 'field_key' => 'proposed_names', 'field_type' => 'textarea'],
            ['label' => 'Aina ya Biashara', 'field_key' => 'business_type', 'field_type' => 'select', 'options' => ['Mtu binafsi', 'Ushirikiano', 'Kampuni']],
            ['label' => 'Jina la Mawasiliano', 'field_key' => 'contact_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
        ], sortOrder: 3, slug: 'business-name-search-reservation');

        $this->seedService($id, 'Usajili wa NGO (BRELA)', [
            ['label' => 'Jina la NGO', 'field_key' => 'ngo_name', 'field_type' => 'text'],
            ['label' => 'Namba ya Usajili (kama ipo)', 'field_key' => 'registration_number', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Majina ya Waanzilishi', 'field_key' => 'founders_names', 'field_type' => 'textarea'],
            ['label' => 'Malengo ya NGO', 'field_key' => 'ngo_purpose', 'field_type' => 'textarea'],
            ['label' => 'Anwani ya Mawasiliano', 'field_key' => 'contact_address', 'field_type' => 'text'],
            ['label' => 'Katiba / Sheria Ndogo', 'field_key' => 'upload_constitution', 'field_type' => 'file'],
            ['label' => 'Passport Photos', 'field_key' => 'upload_passport_photos', 'field_type' => 'file'],
        ], sortOrder: 4, slug: 'ngo-registration-brela');
    }
}
