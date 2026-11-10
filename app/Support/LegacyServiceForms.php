<?php

namespace App\Support;

/** Populate the six existing URLs separately; never merge their records or requests. */
final class LegacyServiceForms
{
    public static function slugs(): array
    {
        return ['brela-company-name-search', 'business-license-application', 'tin-application', 'vat-registration', 'passport-assistance', 'visa-support'];
    }

    public static function apply(array $profiles): array
    {
        foreach ([
            'brela-company-name-search' => ['business-name-search-reservation', 'Uhakiki wa Jina la Kampuni — BRELA'],
            'business-license-application' => ['leseni-ya-biashara', 'Msaada wa Maombi ya Leseni Mpya ya Biashara'],
            'tin-application' => ['tin-registration', 'Msaada wa Maombi ya TIN'],
            'passport-assistance' => ['new-passport-application', 'Msaada wa Maombi ya Pasipoti Mpya'],
            'visa-support' => ['visa-application', 'Msaada wa Maombi ya Visa'],
        ] as $slug => [$source, $name]) {
            $profiles[$slug] = $profiles[$source];
            unset($profiles[$slug]['origin_slug']);
            $profiles[$slug]['name'] = $name;
        }
        $profiles['brela-company-name-search']['fields'] = array_values(array_filter(
            $profiles['brela-company-name-search']['fields'], fn ($f) => $f['field_key'] !== 'entity_type',
        ));
        $profiles['brela-company-name-search']['description'] = 'Tuma majina ya kampuni unayopendekeza ili kuyahakiki kwenye huduma za BRELA. Uhakiki wa jina hauimaanishi jina limehifadhiwa au kampuni imesajiliwa.';

        $f = fn ($key, $label, $type = 'text', $review = false, $when = [], $help = null) => [
            'field_key' => $key, 'label' => $label, 'field_type' => $type, 'is_required' => false,
            'review_required' => $review, 'applies_when' => $when, 'help_text' => $help,
        ];
        $profiles['vat-registration'] = [
            'name' => 'Maombi ya Usajili wa VAT — Tanzania Bara',
            'description' => 'Usajili wa VAT Tanzania Bara huombwa kupitia TRA IDRAS. Sifa hutegemea mauzo yanayotozwa VAT, aina ya shughuli au kuanza biashara. Tuma taarifa ulizonazo tukuhakikie sifa na nyaraka za ombi lako.',
            'category' => null, 'document_sources' => [],
            'sources' => ['https://www.tra.go.tz/page/value-added-tax-vat'],
            'fields' => [
                $f('taxpayer_name', 'Jina la mlipakodi / biashara', review: true),
                $f('tin_number', 'TIN ya mlipakodi', review: true),
                $f('business_address', 'Anwani na eneo la biashara', review: true),
                $f('business_activities', 'Shughuli za biashara', 'textarea', true),
                $f('registration_basis', 'Hali ya biashara kwa usajili wa VAT', 'select', true) + ['options' => ['Biashara inayoendelea', 'Huduma za kitaalamu', 'Taasisi ya Serikali yenye shughuli za kiuchumi', 'Biashara inayotarajiwa kuanza', 'Sijui bado']],
                $f('taxable_turnover', 'Mauzo yanayotozwa VAT na kipindi chake (miezi 6 / 12)', 'textarea', false),
                $f('upload_turnover_records', 'Kumbukumbu za mauzo zinazosaidia kuhakiki sifa za VAT', 'file', false, [], 'Tuma ulizonazo ili timu ihakiki mauzo yanayohesabiwa kwa usajili.'),
                $f('upload_intending_trade_evidence', 'Ushahidi wa biashara inayotarajiwa kuanza', 'file', false, ['registration_basis' => ['Biashara inayotarajiwa kuanza']], 'Mfano mkataba, tuzo ya zabuni, ramani ya ujenzi, mpango wa biashara au ushahidi wa ufadhili wa benki; si vyote vinavyohitajika pamoja.'),
                $f('official_reference', 'Namba ya ombi, kama tayari umeanza'),
            ],
        ];
        return $profiles;
    }
}
