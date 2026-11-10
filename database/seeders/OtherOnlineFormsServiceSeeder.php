<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/**
 * Fomu nyingine mtandaoni — NSSF, ufuatiliaji wa simu na huduma zingine.
 */
class OtherOnlineFormsServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'other-online-forms')->firstOrFail()->id;

        // -----------------------------------------------------------------
        // Madai ya NSSF
        // -----------------------------------------------------------------
        $this->seedService($id, 'Madai ya NSSF', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya NIDA', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu Iliyotumika Wakati wa Usajili wa Kadi ya NSSF', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Barua Pepe Iliyotumika Wakati wa Usajili', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Jina la Mwajiri', 'field_key' => 'employer_name', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuacha Kazi', 'field_key' => 'termination_date', 'field_type' => 'date'],
            ['label' => 'Cheti cha Kazi (Certificate of Service)', 'field_key' => 'upload_certificate_of_service', 'field_type' => 'file'],
            ['label' => 'Maelezo ya Benki (Bank Statement)', 'field_key' => 'upload_bank_statement', 'field_type' => 'file'],
            ['label' => 'Barua ya Kuacha Kazi (Termination Letter)', 'field_key' => 'upload_termination_letter', 'field_type' => 'file'],
            ['label' => 'Kadi ya NSSF', 'field_key' => 'upload_nssf_card', 'field_type' => 'file'],
            ['label' => 'Kitambulisho cha NIDA', 'field_key' => 'upload_nida_copy', 'field_type' => 'file'],
        ], description: 'Gharama ni TSh 20,000/= pamoja na kukusaidia kufuatilia mpaka mwisho. Unatakiwa kuwa na barua pepe na namba ya simu zilizotumika wakati wa usajiri wa kadi yako ya NSSF.', sortOrder: 1, slug: 'madai-ya-nssf', price: 20000);

        // -----------------------------------------------------------------
        // Kufuatilia simu iliyopotea / kuibiwa
        // -----------------------------------------------------------------
        $this->seedService($id, 'Kufuatilia Simu Iliyopotea au Kuibiwa', [
            ['label' => 'Mfurushi Unaochagua', 'field_key' => 'tracking_package', 'field_type' => 'select', 'options' => ['BASIC TRACK — TSh 100,000/=', 'STANDARD TRACK — TSh 200,000/=', 'PREMIUM TRACK — TSh 300,000/=']],
            ['label' => 'Jina Kamili la Mwombaji', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya NIDA', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu ya Mawasiliano', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Simu (Make / Model)', 'field_key' => 'phone_model', 'field_type' => 'text'],
            ['label' => 'Rangi ya Simu', 'field_key' => 'phone_colour', 'field_type' => 'text'],
            ['label' => 'IMEI Na. 1', 'field_key' => 'imei_1', 'field_type' => 'text'],
            ['label' => 'IMEI Na. 2', 'field_key' => 'imei_2', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Namba ya Simu Iliyokuwa Inatumika', 'field_key' => 'sim_number_in_use', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kupotea / Kuibiwa', 'field_key' => 'date_lost', 'field_type' => 'date'],
            ['label' => 'Mahali Ilikuwa Ipopotea / Kuibiwa', 'field_key' => 'place_lost', 'field_type' => 'text'],
            ['label' => 'Maelezo ya Tukio', 'field_key' => 'incident_description', 'field_type' => 'textarea'],
            ['label' => 'Nakala ya Kitambulisho cha Taifa (NIDA)', 'field_key' => 'upload_nida_copy', 'field_type' => 'file'],
            ['label' => 'Box la Simu lenye IMEI', 'field_key' => 'upload_phone_box', 'field_type' => 'file', 'is_required' => false],
            ['label' => 'Risiti ya Ununuzi wa Simu', 'field_key' => 'upload_purchase_receipt', 'field_type' => 'file', 'is_required' => false],
            ['label' => 'Akaunti ya Google / iCloud', 'field_key' => 'upload_account_proof', 'field_type' => 'file', 'is_required' => false],
        ], description: 'Tafadhali jaza fomu rasmi ya kufuatilia simu kwenye ukurasa wa Fomu. Bei: BASIC TRACK TSh 100,000/=, STANDARD TRACK TSh 200,000/=, PREMIUM TRACK TSh 300,000/=. Gharama hazijumuwi ada za kisheria wala gharama za mamlaka za serikali kama Polisi.', sortOrder: 2, slug: 'kufuatilia-simu-iliyopotea', price: 100000);

        // -----------------------------------------------------------------
        // Huduma zilizopo — kwa Kiswahili
        // -----------------------------------------------------------------
        $this->seedService($id, 'Msaada wa Jumla wa Fomu', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Jina la Fomu / Taasisi', 'field_key' => 'form_name', 'field_type' => 'text'],
            ['label' => 'Madhumuni ya Fomu', 'field_key' => 'form_purpose', 'field_type' => 'textarea'],
            ['label' => 'Maelezo Yaongezeo', 'field_key' => 'additional_details', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'Nyaraka za Msaada', 'field_key' => 'upload_supporting_docs', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 3, slug: 'general-form-assistance');

        $this->seedService($id, 'Usajili / Uanachama wa NGO', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Jina la Taasisi', 'field_key' => 'organization_name', 'field_type' => 'text'],
            ['label' => 'Aina ya Uanachama', 'field_key' => 'membership_type', 'field_type' => 'select', 'options' => ['Mtu binafsi', 'Kampuni', 'Mwanafunzi']],
            ['label' => 'Kwa Nini Unataka Kujiunga?', 'field_key' => 'motivation', 'field_type' => 'textarea'],
            ['label' => 'Nyaraka za Msaada', 'field_key' => 'upload_documents', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 4, slug: 'ngo-membership-application');

        $this->seedService($id, 'Ombi la Msaada wa Jamii / Ustawi', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email', 'is_required' => false],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Msaada', 'field_key' => 'assistance_type', 'field_type' => 'select', 'options' => ['Matibabu', 'Elimu', 'Mazishi', 'Chakula', 'Nyingine']],
            ['label' => 'Sababu ya Ombi', 'field_key' => 'reason', 'field_type' => 'textarea'],
            ['label' => 'Nyaraka za Msaada', 'field_key' => 'upload_documents', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 5, slug: 'welfarecommunity-assistance-request');
    }
}
