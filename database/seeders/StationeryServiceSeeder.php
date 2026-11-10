<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/**
 * Vifaa vya ofisi na shule.
 */
class StationeryServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'stationery')->firstOrFail()->id;

        $this->seedService($id, 'Oda ya Vifaa vya Ofisi', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Kampuni / Taasisi', 'field_key' => 'company', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Anwani ya Kupelekea', 'field_key' => 'delivery_address', 'field_type' => 'text'],
            ['label' => 'Vifaa Vinavyohitajika', 'field_key' => 'items_required', 'field_type' => 'textarea'],
            ['label' => 'Maelezo ya Idadi', 'field_key' => 'quantity_details', 'field_type' => 'textarea'],
            ['label' => 'Kiwango cha Bajeti', 'field_key' => 'budget', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Tarehe ya Kupeleka', 'field_key' => 'delivery_date', 'field_type' => 'date'],
        ], sortOrder: 1, slug: 'office-stationery-order');

        $this->seedService($id, 'Ombi la Vifaa vya Shule', [
            ['label' => 'Jina la Shule', 'field_key' => 'school_name', 'field_type' => 'text'],
            ['label' => 'Mtu wa Kuwasiliana Naye', 'field_key' => 'contact_person', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Anwani ya Kupelekea', 'field_key' => 'delivery_address', 'field_type' => 'text'],
            ['label' => 'Orodha ya Vifaa', 'field_key' => 'stationery_list', 'field_type' => 'textarea'],
            ['label' => 'Tarehe Inayotarajika ya Matumizi', 'field_key' => 'use_date', 'field_type' => 'date'],
        ], sortOrder: 2, slug: 'school-stationery-request');

        $this->seedService($id, 'Vifaa Vilivyoboreshwa kwa Nembo (Branded)', [
            ['label' => 'Jina la Kampuni / Taasisi', 'field_key' => 'company_name', 'field_type' => 'text'],
            ['label' => 'Mtu wa Kuwasiliana Naye', 'field_key' => 'contact_person', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Vifaa', 'field_key' => 'stationery_type', 'field_type' => 'select', 'options' => ['Barua (Letterheads)', 'Mafuli (Envelopes)', 'Kadi za Biashara', 'Daftari']],
            ['label' => 'Idadi Inayohitajika', 'field_key' => 'quantity', 'field_type' => 'number'],
            ['label' => 'Faili la Nembo / Logo', 'field_key' => 'upload_logo', 'field_type' => 'file'],
            ['label' => 'Maelezo ya Muundo', 'field_key' => 'design_preferences', 'field_type' => 'textarea', 'is_required' => false],
        ], sortOrder: 3, slug: 'customized-stationery-branded');
    }
}
