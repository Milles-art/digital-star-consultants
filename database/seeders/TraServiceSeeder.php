<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/**
 * TRA na Kodi — TIN, kibali cha kodi, marejesho na kubalishwa jina.
 */
class TraServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'tra')->firstOrFail()->id;

        $this->seedService($id, 'Usajili wa TIN', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Tarehe ya Kuzaliwa', 'field_key' => 'date_of_birth', 'field_type' => 'date'],
            ['label' => 'Namba ya NIDA', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Biashara / Ajira', 'field_key' => 'business_type', 'field_type' => 'select', 'options' => ['Mtu Binafsi', 'Kampuni', 'Kujiajiri', 'Serikali']],
            ['label' => 'Jina la Biashara (kama ipo)', 'field_key' => 'business_name', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Nakala ya NIDA', 'field_key' => 'upload_nida_copy', 'field_type' => 'file'],
            ['label' => 'Passport-size Photo', 'field_key' => 'upload_passport_photo', 'field_type' => 'file'],
        ], sortOrder: 1, slug: 'tin-registration');

        // -----------------------------------------------------------------
        // Kubalishwa jina kwenye TIN (marekebisho ya jina TRA)
        // -----------------------------------------------------------------
        $this->seedService($id, 'Kubalishwa Jina kwenye TIN', [
            ['label' => 'Jina Sahili Linalotakiwa', 'field_key' => 'correct_full_name', 'field_type' => 'text'],
            ['label' => 'Jina lililopo sasa kwenye TIN', 'field_key' => 'current_tin_name', 'field_type' => 'text'],
            ['label' => 'Namba ya TIN', 'field_key' => 'tin_number', 'field_type' => 'text'],
            ['label' => 'Namba ya NIDA', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Sababu ya Marekebisho', 'field_key' => 'reason_for_change', 'field_type' => 'select', 'options' => ['Makosa ya tahajia', 'Jina limebadilishwa rasmi', 'Makosa ya cheti', 'Nyingine']],
            ['label' => 'Namba ya Control ya Malipo ya Serikali', 'field_key' => 'government_control_number', 'field_type' => 'text'],
            ['label' => 'Nakala ya Kitambulisho (NIDA)', 'field_key' => 'upload_nida_copy', 'field_type' => 'file'],
            ['label' => 'Nakala ya Cheti cha Kuzaliwa', 'field_key' => 'upload_birth_certificate', 'field_type' => 'file'],
        ], description: 'Gharama ni TSh 81,000/= jumla: control number ya mamlaka ya ardhi TSh 31,000/=, deed poll / kiapo cha marekebisho TSh 30,000/=, na ushauri wa kutuma maombi TSh 20,000/=. Baada ya hapo unapeleka nyaraka TRA tayari kwa kubadilishwa jina.', sortOrder: 2, slug: 'kubalishwa-jina-tin', price: 81000);

        $this->seedService($id, 'Kibali cha Kodi (Tax Clearance)', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya TIN', 'field_key' => 'tin_number', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Madhumuni ya Kibali', 'field_key' => 'purpose', 'field_type' => 'select', 'options' => ['Tender', 'Ajira', 'Usajili wa Biashara', 'Nyingine']],
            ['label' => 'Cheti cha TIN', 'field_key' => 'upload_tin_certificate', 'field_type' => 'file'],
        ], sortOrder: 3, slug: 'tax-clearance-certificate');

        $this->seedService($id, 'Msaada wa Kurejesha Kodi (Returns)', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya TIN', 'field_key' => 'tin_number', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Mwaka wa Kodi', 'field_key' => 'tax_year', 'field_type' => 'text'],
            ['label' => 'Maelezo ya Mapato', 'field_key' => 'income_details', 'field_type' => 'textarea'],
            ['label' => 'Matumizi / Kutozwa Kodi', 'field_key' => 'expenses', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'Taarifa za Fedha', 'field_key' => 'upload_financial_statements', 'field_type' => 'file'],
        ], sortOrder: 4, slug: 'tax-return-filing-assistance');

        // -----------------------------------------------------------------
        // Leseni ya Biashara (Business License)
        // -----------------------------------------------------------------
        $this->seedService($id, 'Leseni ya Biashara', [
            ['label' => 'Namba ya nida', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'TIN namba', 'field_key' => 'tin_number', 'field_type' => 'text'],
            ['label' => 'Email yako', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya simu', 'field_key' => 'phone', 'field_type' => 'tel'],
        ], description: 'Usaidizi wa kupata leseni ya biashara (Business License) kwa ajili ya kuendesha biashara yako kwa sheria.', sortOrder: 5, slug: 'leseni-ya-biashara');
    }
}
