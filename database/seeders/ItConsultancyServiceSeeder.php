<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/**
 * IT na Teknolojia.
 */
class ItConsultancyServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'it-tech-consultancy')->firstOrFail()->id;

        $this->seedService($id, 'Utengenezaji wa Tovuti', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Jina la Kampuni', 'field_key' => 'company', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Tovuti', 'field_key' => 'website_type', 'field_type' => 'select', 'options' => ['Biashara', 'Portfolio', 'E-commerce', 'Blog', 'Kampuni']],
            ['label' => 'Vipengele Vinavyohitajika', 'field_key' => 'features', 'field_type' => 'textarea'],
            ['label' => 'Upendeleo wa Muundo', 'field_key' => 'design_preferences', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'Kiwango cha Bajeti', 'field_key' => 'budget', 'field_type' => 'select', 'options' => ['Ndogo', 'Wastani', 'Juu']],
            ['label' => 'Muda Unaotarajia', 'field_key' => 'timeline', 'field_type' => 'text'],
            ['label' => 'Mifano ya Tovuti Unazopenda', 'field_key' => 'references', 'field_type' => 'textarea', 'is_required' => false],
        ], sortOrder: 1, slug: 'website-development');

        $this->seedService($id, 'Utengenezaji wa Programu za Simu', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Jina la Kampuni', 'field_key' => 'company', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Programu', 'field_key' => 'app_type', 'field_type' => 'select', 'options' => ['Android', 'iOS', 'Zote mbili']],
            ['label' => 'Vipengele / Mahitaji ya Programu', 'field_key' => 'features', 'field_type' => 'textarea'],
            ['label' => 'Watumiaji Walengwa', 'field_key' => 'target_audience', 'field_type' => 'textarea'],
            ['label' => 'Upendeleo wa Jukwaa', 'field_key' => 'platform', 'field_type' => 'select', 'options' => ['Native', 'Cross-platform', 'PWA']],
            ['label' => 'Kiwango cha Bajeti', 'field_key' => 'budget', 'field_type' => 'select', 'options' => ['Ndogo', 'Wastani', 'Juu']],
        ], sortOrder: 2, slug: 'mobile-app-development');

        $this->seedService($id, 'Ushauri wa IT na Mkakati', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Jina la Kampuni', 'field_key' => 'company', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Ushauri', 'field_key' => 'consultancy_type', 'field_type' => 'select', 'options' => ['Mabadiliko ya Kidijitali', 'Miundombinu ya IT', 'Usalama wa Mtandao', 'Suluhisho za Cloud', 'Nyingine']],
            ['label' => 'Changamoto Zilizopo', 'field_key' => 'challenges', 'field_type' => 'textarea'],
            ['label' => 'Matokeo Unayotarajia', 'field_key' => 'goals', 'field_type' => 'textarea'],
            ['label' => 'Kiwango cha Bajeti', 'field_key' => 'budget', 'field_type' => 'select', 'options' => ['Ndogo', 'Wastani', 'Juu']],
            ['label' => 'Njia Unayopendelea ya Ushauri', 'field_key' => 'consultancy_method', 'field_type' => 'select', 'options' => ['Kwa mkono', 'Mtandaoni']],
        ], sortOrder: 3, slug: 'it-consultancy-strategy');

        // -----------------------------------------------------------------
        // TOSSA — mpango bado unaendelea (audio inafanyiwa kazi na mfumo
        // unafanyiwa maboresho). Weka inactive hadi utakapotangazwa rasmi.
        // -----------------------------------------------------------------
        $tossa = $this->seedService($id, 'TOSSA', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email', 'is_required' => false],
            ['label' => 'Aina ya Huduma Unayohitaji', 'field_key' => 'service_interest', 'field_type' => 'textarea'],
        ], description: 'Mpango wa TOSSA bado unaendelea. Tutatangaza rasmi wakiwa tayari. Karibu sana, Digital Star Consultants — "Live & Enjoy your Digital Life".', sortOrder: 4, slug: 'tossa');

        $tossa->forceFill(['is_active' => false])->save();

        // Same treatment for the other programmes announced but not yet live.
        Service::where('slug', 'tossa')->update(['is_active' => false]);
    }
}
