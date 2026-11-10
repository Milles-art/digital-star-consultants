<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/**
 * Laini za wakala, Lipa Number na huduma za malipo ya simu.
 */
class TelecomPaymentServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'simu-mtandao-na-malipo')->firstOrFail()->id;

        // -----------------------------------------------------------------
        // Laini za wakala
        // -----------------------------------------------------------------
        $this->seedService($id, 'Laini ya Wakala (Agent Line)', [
            ['label' => 'Kampuni ya Simu', 'field_key' => 'network', 'field_type' => 'select', 'options' => ['Tigo', 'Vodacom', 'Airtel', 'Halotel']],
            ['label' => 'Jina Halisi la Mmiliki', 'field_key' => 'owner_full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya NIDA', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Namba ya TIN', 'field_key' => 'tin_number', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu ya Mawasiliano', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Eneo la Biashara', 'field_key' => 'business_location', 'field_type' => 'text'],
            ['label' => 'Unahitaji Laini Zote Nne kwa Pamoja?', 'field_key' => 'needs_all_lines', 'field_type' => 'select', 'options' => ['Ndiyo, laini zote', 'Hapana, moja tu']],
            ['label' => 'Tayari una NIDA, TIN na float ya TSh 100,000/= (inayohitajika kwa Halotel)?', 'field_key' => 'documents_ready', 'field_type' => 'select', 'options' => ['Ndiyo', 'Hapana']],
            ['label' => 'Nakala ya Kitambulisho cha NIDA', 'field_key' => 'upload_nida_copy', 'field_type' => 'file'],
            ['label' => 'Cheti cha TIN', 'field_key' => 'upload_tin_certificate', 'field_type' => 'file'],
        ], description: 'Bei ya rejareja: Tigo TSh 150,000/=, Vodacom TSh 90,000/=, Airtel TSh 30,000/=, Halotel TSh 30,000/= — jumla ya laini zote TSh 300,000/=. Laini utazipata papo hapo; laini ya Tigo/Yass utaisubiri ndani ya saa 24. Unatakiwa kuwa na NIDA, TIN na float ya TSh 100,000/= (kwa Halotel pekee).', sortOrder: 1, slug: 'laini-ya-wakala', price: 30000);

        // -----------------------------------------------------------------
        // Usajili wa Lipa Number
        // -----------------------------------------------------------------
        $this->seedService($id, 'Usajili wa Lipa Number', [
            ['label' => 'Jina la Biashara', 'field_key' => 'business_name', 'field_type' => 'text'],
            ['label' => 'Aina ya Jina Litakalotumika', 'field_key' => 'name_type', 'field_type' => 'select', 'options' => ['Jina Halisi', 'Jina Lingine']],
            ['label' => 'Jina Halisi la Mmiliki', 'field_key' => 'owner_full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya NIDA', 'field_key' => 'nida_number', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Biashara', 'field_key' => 'business_type', 'field_type' => 'select', 'options' => ['Mtu binafsi', 'Ushirikiano', 'Kampuni']],
            ['label' => 'Laini Mpya Bila Kuunganisha M-PESA?', 'field_key' => 'new_line_without_mpesa', 'field_type' => 'select', 'options' => ['Ndiyo', 'Hapana']],
            ['label' => 'Nakala ya Kitambulisho cha NIDA', 'field_key' => 'upload_nida_copy', 'field_type' => 'file'],
        ], description: 'Usajili wa Lipa Number kwa jina halisi ni TSh 20,000/= na kwa jina lingine ni TSh 30,000/=. Usajili hukamilika ndani ya wiki moja. Unatakiwa kuwa na laini mpya bila kuunganisha M-PESA.', sortOrder: 2, slug: 'usajili-wa-lipa-number', price: 20000);
    }
}
