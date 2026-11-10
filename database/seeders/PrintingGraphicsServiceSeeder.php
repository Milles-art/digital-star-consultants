<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Database\Seeders\Concerns\SeedsServiceFields;
use Illuminate\Database\Seeder;

/**
 * Uchapishaji, ubunifu na vifungashio.
 */
class PrintingGraphicsServiceSeeder extends Seeder
{
    use SeedsServiceFields;

    public function run(): void
    {
        $id = ServiceCategory::where('slug', 'printing-graphics-design')->firstOrFail()->id;

        // -----------------------------------------------------------------
        // Uchapishaji wa nyuzi (Embroidery)
        // -----------------------------------------------------------------
        $this->seedService($id, 'Uchapishaji wa Nyuzi (Embroidery)', [
            ['label' => 'Huduma Unayotaka', 'field_key' => 'service_option', 'field_type' => 'select', 'options' => [
                'Logo tu',
                'Digitalization ya logo (kuweka logo mtindo wa nyuzi)',
                'Printing tu',
                'Logo + Printing',
                'T-shirt + Printing',
                'Logo + T-shirt + Printing',
            ]],
            ['label' => 'Unapata T-shirt Mpya Kutoka Kwetu?', 'field_key' => 'needs_tshirt', 'field_type' => 'select', 'options' => ['Ndiyo', 'Hapana, nina zangu']],
            ['label' => 'Idadi ya T-shirt', 'field_key' => 'tshirt_quantity', 'field_type' => 'number', 'is_required' => false],
            ['label' => 'Ukubwa wa Kila T-shirt', 'field_key' => 'tshirt_sizes', 'field_type' => 'text', 'is_required' => false, 'help_text' => 'mf. 3×XL, 5×L, 10×M'],
            ['label' => 'Ukubwa wa Logo kwenye T-shirt', 'field_key' => 'logo_size', 'field_type' => 'select', 'options' => ['Kifua tu (chest)', 'Kifua na mgongo (front & back)', 'Kiganja (sleeve)', 'Nyingine']],
            ['label' => 'Rangi za Nyuzi Zinazotakiwa', 'field_key' => 'thread_colours', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Je, una Logo tayari (AI / PNG / PDF)?', 'field_key' => 'has_logo_file', 'field_type' => 'select', 'options' => ['Ndiyo, nina faili', 'Hapana, mnitengenezee']],
            ['label' => 'Maelezo ya Kazi', 'field_key' => 'notes', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'Faili la Logo', 'field_key' => 'upload_logo', 'field_type' => 'file', 'is_required' => false],
            ['label' => 'Picha ya Mfano Unaotaka (reference)', 'field_key' => 'upload_reference_image', 'field_type' => 'file', 'is_required' => false],
        ], description: 'Gharama: Logo TSh 30,000/= · Digitalization ya logo TSh 20,000/= · Printing TSh 3,000/=. Kuanzia T-shirt 5, digitalization hushuka hadi TSh 15,000/= na printing TSh 2,500/=. Vifurushi: T-shirt + Printing TSh 10,000/= · Digitalization TSh 10,000/= · Logo + T-shirt + Printing TSh 30,000/= · Logo + Printing TSh 20,000/=. Gharama ya chini ni TSh 20,000/=.', sortOrder: 1, slug: 'uchapishaji-wa-nyuzi', price: 3000);

        // -----------------------------------------------------------------
        // Vifungashio vya kisasa (paper bags)
        // -----------------------------------------------------------------
        $this->seedService($id, 'Vifungashio vya Kisasa (Premium Paper Bags)', [
            ['label' => 'Ukubwa wa Vifungashio', 'field_key' => 'bag_size', 'field_type' => 'select', 'options' => [
                'A6 — TSh 60,000/ (Pc 100)',
                'A5 — TSh 70,000/ (Pc 100)',
                'A4 — TSh 80,000/ (Pc 100)',
                'A3 — TSh 100,000/ (Pc 100)',
                'A2 — TSh 150,000/ (Pc 100)',
            ]],
            ['label' => 'Idadi ya Vifurushi (kila kifurushi = Pc 100)', 'field_key' => 'quantity', 'field_type' => 'number'],
            ['label' => 'Jina / Nembo Itakayochapishwa', 'field_key' => 'brand_name', 'field_type' => 'text'],
            ['label' => 'Maelezo ya Kuchapishwa (simu, anwani, kauli mbiu)', 'field_key' => 'printed_details', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'Rangi Zinazotakiwa', 'field_key' => 'colour_preferences', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Matumizi ya Vifungashio', 'field_key' => 'usage', 'field_type' => 'select', 'options' => ['Maduka', 'Boutique', 'Gift packaging', 'Biashara nyingine']],
            ['label' => 'Tarehe Unayotaka Kuipokea', 'field_key' => 'delivery_date', 'field_type' => 'date', 'is_required' => false],
            ['label' => 'Faili la Nembo / Muundo', 'field_key' => 'upload_design', 'field_type' => 'file', 'is_required' => false],
        ], description: 'Bei kwa Pc 100: A6 TSh 60,000/= · A5 TSh 70,000/= · A4 TSh 80,000/= · A3 TSh 100,000/= · A2 TSh 150,000/=. Ubora wa hali ya juu kwa maduka, boutique na gift packaging.', sortOrder: 2, slug: 'vifungashio-vya-kisasa', price: 60000);

        // -----------------------------------------------------------------
        // Huduma zilizopo — kwa Kiswahili
        // -----------------------------------------------------------------
        $this->seedService($id, 'Branding na Utengenezaji wa Logo', [
            ['label' => 'Jina la Kampuni / Taasisi', 'field_key' => 'company_name', 'field_type' => 'text'],
            ['label' => 'Kundi la Biashara', 'field_key' => 'industry', 'field_type' => 'text'],
            ['label' => 'Upendeleo wa Rangi', 'field_key' => 'color_preferences', 'field_type' => 'text', 'is_required' => false],
            ['label' => 'Upendeleo wa Mtindo', 'field_key' => 'style_preferences', 'field_type' => 'select', 'options' => ['Kisasa', 'Classic', 'Minimalist', 'Bold', 'Nyingine']],
            ['label' => 'Mifano / Mwongozo wa Ubunifu', 'field_key' => 'inspiration', 'field_type' => 'textarea', 'is_required' => false],
            ['label' => 'Logo Itatumikaje', 'field_key' => 'logo_use', 'field_type' => 'textarea'],
        ], sortOrder: 3, slug: 'branding-logo-design');

        $this->seedService($id, 'Huduma za Uchapishaji', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Uchapishaji', 'field_key' => 'print_type', 'field_type' => 'select', 'options' => ['Kadi za Biashara', 'Vituo (Flyers)', 'Mabango', 'Brochure', 'Posters', 'Nyingine']],
            ['label' => 'Idadi', 'field_key' => 'quantity', 'field_type' => 'number'],
            ['label' => 'Ukubwa / Vipimo', 'field_key' => 'size', 'field_type' => 'text'],
            ['label' => 'Chaguo la Rangi', 'field_key' => 'color_options', 'field_type' => 'select', 'options' => ['Nyeusi na Nyeupe', 'Rangi Kamili', 'Mseto']],
            ['label' => 'Faili la Muundo', 'field_key' => 'upload_design', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 4, slug: 'printing-services');

        $this->seedService($id, 'Huduma za Ubunifu wa Graphics', [
            ['label' => 'Majina Kamili', 'field_key' => 'full_name', 'field_type' => 'text'],
            ['label' => 'Barua Pepe', 'field_key' => 'email', 'field_type' => 'email'],
            ['label' => 'Namba ya Simu', 'field_key' => 'phone', 'field_type' => 'tel'],
            ['label' => 'Aina ya Muundo', 'field_key' => 'design_type', 'field_type' => 'select', 'options' => ['Mitandao ya Kijamii', 'Web Banners', 'Flyers', 'Posters', 'Kadi za Biashara', 'Nyingine']],
            ['label' => 'Maelezo ya Muundo', 'field_key' => 'design_description', 'field_type' => 'textarea'],
            ['label' => 'Mwongozo wa Brandi', 'field_key' => 'upload_brand_guidelines', 'field_type' => 'file', 'is_required' => false],
            ['label' => 'Picha za Rejea', 'field_key' => 'upload_references', 'field_type' => 'file', 'is_required' => false],
        ], sortOrder: 5, slug: 'graphic-design-services');
    }
}
