<?php

namespace App\Support;

/** Field additions transcribed from the customer's 13 supplied service guides.
 * Prices, approval promises and instructions to sign for another person are
 * deliberately excluded. These remain optional assistance-intake fields.
 */
final class DocumentServiceRequirements
{
    public const DOCUMENTS = [
        'DIGITAL STAR SERVICES GUIDE_ (1) (1).pdf',
        'MAELEKEZO YA KUBADILI UMILIKI WA USAFIRI.pdf',
        'MAELEKEZO  YA KUFUNGUA MADAI eARDHI (1).pdf',
        'MAELEKEZO YA HUDUMA ZA PRINTING YA NYUZI (2).pdf',
        'MAELEKEZO YA KUPATA PASIPOTI YA KUSAFIRIA (2).pdf',
        'MAELEKEZO BAADA YA KUPATA FORM YA MAOMBI YA PASSPORT (2).pdf',
        'MAELEKEZO YA KUPATA CHETI CHA KUZALIWA (2).pdf',
        'MAELEKEZO YA KUPATA NAMBA YA UTAMBULISHO WA TAIFA (2).pdf',
        'MAELEKEZO YA KUPATA CHETI CHA KIFO (2).pdf',
        'MAELEKEZO YA KUSAJIRI KAMPUNI (1).pdf',
        'USHAURI JUU YA MKOPO WA ELIMU YA JUU KWA WANAFUNZI (1).pdf',
        'MAELEKEZO YA KUCHAPA MIFUKO (1).pdf',
        'MAELEKEZO YA KUFUNGUA MADAI NSSF (1).pdf',
    ];

    public static function apply(array $profiles): array
    {
        $f = fn (string $key, string $label, string $type = 'text', bool $review = false, array $when = [], ?string $help = null): array => [
            'field_key' => $key, 'label' => $label, 'field_type' => $type,
            'is_required' => false, 'review_required' => $review,
            'applies_when' => $when, 'help_text' => $help,
        ];
        $u = fn (string $key, string $label, bool $review = false, array $when = [], ?string $help = null): array => $f($key, $label, 'file', $review, $when, $help);
        $s = fn (string $key, string $label, array $options, bool $review = false): array => $f($key, $label, 'select', $review) + ['options' => $options];
        $parents = [];
        foreach (['father' => 'Baba', 'mother' => 'Mama'] as $key => $label) {
            $parents[] = $f($key.'_birth_date', $label.' — tarehe ya kuzaliwa', 'date');
            $parents[] = $f($key.'_birth_place', $label.' — mahali pa kuzaliwa (mkoa, wilaya, kata, mtaa/kijiji)');
            $parents[] = $f($key.'_residence', $label.' — makazi (mkoa, wilaya, kata, mtaa/kijiji)');
            $parents[] = $f($key.'_address', $label.' — anwani ya makazi');
            $parents[] = $f($key.'_house_number', $label.' — namba ya nyumba');
            $parents[] = $f($key.'_phone', $label.' — namba ya simu', 'tel');
            $parents[] = $f($key.'_occupation', $label.' — kazi / cheo');
        }
        $parentLife = $s('parents_alive', 'Uhai wa wazazi', ['Wote hai', 'Baba amefariki', 'Mama amefariki', 'Wote wamefariki', 'Sijui']);
        $parentId = $f('parents_identity_details', 'NIDA / kitambulisho cha baba na mama, kama vipo', 'textarea');
        $patches = [
            'nida-registration-correction' => [
                'documents' => [0, 7],
                'fields' => [...$parents, $f('birth_location', 'Mahali ulipozaliwa (mkoa, wilaya, kata, mtaa/kijiji)'),
                    $f('house_number', 'Namba ya nyumba'), $f('occupation', 'Kazi / cheo'),
                    $f('primary_school', 'Shule ya msingi uliposoma'), $f('primary_school_year', 'Mwaka uliomaliza shule ya msingi'),
                    $u('upload_father_birth_certificate', 'Cheti cha kuzaliwa / kiapo cha baba, kama unacho'),
                ],
            ],
            'birth-certificate-application-rita' => [
                'documents' => [0, 6], 'remove' => ['parents_information'],
                'fields' => [...$parents, $parentLife, $parentId,
                    $s('child_alive', 'Uhai wa mwenye cheti', ['Hai', 'Amefariki', 'Sijui']),
                    $f('current_residence', 'Makazi ya sasa (mkoa, wilaya, kata, mtaa/kijiji)'),
                    $f('residential_address', 'Anwani ya makazi'), $f('occupation', 'Kazi / cheo, kama inahusika'),
                    $u('upload_applicant_id', 'Kitambulisho cha mwombaji / mwenye cheti, kama kipo'),
                    $u('upload_birth_supporting_docs', 'Vielelezo vya kuzaliwa kwa usajili uliochelewa', true, ['request_type' => ['Usajili baada ya siku 90']], 'Kadi ya kliniki, cheti cha ubatizo au barua ya mamlaka ya eneo kulingana na hali yako.'),
                    $u('upload_additional_birth_evidence', 'Vielelezo vingine vya kuzaliwa ulivyonavyo', false, [], 'NIDA/kura, cheti cha ubatizo, kadi ya kliniki au cheti cha kuhitimu shule. Kwa mtoto mdogo: kitambulisho cha mzazi, kadi ya ujauzito/kliniki kama zipo.'),
                ],
            ],
            'death-certificate-application-rita' => [
                'documents' => [8], 'fields' => [...$parents, $parentLife,
                    $f('deceased_birth_place', 'Mahali marehemu alipozaliwa'), $f('death_hospital', 'Hospitali kifo kilipotokea, kama inahusika'),
                    $f('residential_address', 'Anwani ya makazi ya marehemu'), $f('occupation', 'Kazi / cheo cha marehemu'),
                    $u('upload_family_identity', 'Vitambulisho vya ndugu walioshiriki kikao, kama vinahusika'),
                    $u('upload_deceased_birth_certificate', 'Cheti cha kuzaliwa / kiapo cha marehemu, kama kipo'),
                    $u('upload_parent_birth_certificate', 'Cheti cha kuzaliwa / kiapo cha mzazi, kama kinaombwa kwa hali yako'),
                    $u('upload_residence_letter', 'Barua ya utambulisho wa makazi, kama inahusika'),
                ],
            ],
            'new-passport-application' => [
                'documents' => [0, 4, 5], 'fields' => [...$parents,
                    $f('house_number', 'Namba ya nyumba'), $f('occupation', 'Kazi / cheo'),
                    $s('purpose_of_travel', 'Madhumuni ya safari', ['Kusoma', 'Kazi', 'Matibabu', 'Ndoa', 'Dini', 'Kutembea']),
                    $u('upload_education_certificates', 'Vyeti vya elimu, kama vinahusika'),
                    $u('upload_residence_letter', 'Barua ya uthibitisho wa makazi'),
                    $u('upload_passport_request_letter', 'Barua ya ombi la pasipoti, kama tayari unayo'),
                    $u('upload_travel_evidence', 'Kielelezo cha safari, kama kinahusika'),
                    $u('upload_signed_passport_form', 'Fomu ya maombi ya pasipoti iliyokamilishwa, kama tayari unayo', false, [], 'Chapisha fomu na vielelezo. Mwombaji asaini tamko lake; kila mdhamini asaini mwenyewe; sehemu ya shahidi/mwanasheria ijazwe na mhusika. Kisha fuata maelekezo ya ofisi ya Uhamiaji.'),
                    $f('emergency_contact_details', 'Mtu wa dharura: majina na mawasiliano', 'textarea'),
                    $f('guarantor1_house_number', 'Mdhamini 1 — namba ya nyumba'),
                    $f('guarantor1_occupation', 'Mdhamini 1 — kazi / cheo'),
                    $f('guarantor2_house_number', 'Mdhamini 2 — namba ya nyumba'),
                    $f('guarantor2_occupation', 'Mdhamini 2 — kazi / cheo'),
                ],
            ],
            'vehicle-related-applications' => [
                'documents' => [1], 'fields' => [
                    $f('seller_tin', 'TIN ya mmiliki / muuzaji', 'text', true, ['request_type' => ['Uhamisho wa umiliki']]),
                    $f('buyer_tin', 'TIN ya mnunuzi', 'text', true, ['request_type' => ['Uhamisho wa umiliki']]),
                    $u('upload_vehicle_registration', 'Kadi ya usajili wa usafiri', true, ['request_type' => ['Uhamisho wa umiliki']]),
                    $u('upload_seller_identity', 'Kitambulisho cha mmiliki / muuzaji', true, ['request_type' => ['Uhamisho wa umiliki']]),
                    $u('upload_buyer_identity', 'Kitambulisho cha mnunuzi', true, ['request_type' => ['Uhamisho wa umiliki']]),
                    $u('upload_sale_agreement', 'Mkataba wa mauziano wenye muhuri wa mwanasheria wa TLS', true, ['request_type' => ['Uhamisho wa umiliki']]),
                    $u('upload_lawyer_receipt', 'Risiti ya mwanasheria wa TLS', true, ['request_type' => ['Uhamisho wa umiliki']]),
                ],
            ],
            'company-registration-brela' => [
                'documents' => [0, 9], 'fields' => [
                    $s('liability_type', 'Dhima ya kampuni', ['Limited', 'Unlimited', 'Nahitajia ushauri']),
                    $f('owners_details', 'Kila mmiliki: NIN, simu, email, anwani na alama maarufu karibu na makazi', 'textarea'),
                    $f('trading_name', 'Jina la biashara'),
                    $f('business_landmark', 'Eneo / barabara / jengo maarufu karibu na ofisi'),
                    $f('directors_details', 'Taarifa za kila mkurugenzi', 'textarea', true, [], 'Majina, NIN/pasipoti, TIN, simu, email, anwani na eneo/barabara/jengo maarufu karibu na makazi. Mkurugenzi anaweza kuwa mmiliki au mtu mwingine; andika kila mmoja.'),
                ],
            ],
            'business-registration-brela' => ['documents' => [0], 'fields' => [
                $f('business_landmark', 'Alama maarufu karibu na eneo la biashara'),
                $f('ownership_agreement', 'Mfumo wa makubaliano ya umiliki / ushirikiano', 'textarea'),
            ]],
            'job-application-assistance' => ['documents' => [0], 'fields' => [
                $f('residential_address', 'Anwani ya makazi'), $f('index_number', 'Namba ya usajili / index number'),
                $f('education_level', 'Kiwango cha elimu'),
                $u('upload_certificates', 'Vyeti vya elimu vinavyohusika', false, [], 'Mwongozo wa ofisi unaelekeza vyeti vya chuo viwe na uthibitisho/muhuri wa mwanasheria inapohitajika kwa ombi husika.'),
            ]],
            'tin-registration' => ['documents' => [0], 'fields' => []],
            'nhif-registration' => ['documents' => [0], 'fields' => [$f('email', 'Barua pepe yako', 'email')]],
            'leseni-ya-biashara' => ['documents' => [0], 'fields' => [$f('nida_number', 'Namba ya NIDA'), $f('email', 'Barua pepe yako', 'email')]],
            'maombi-ya-mkopo-heslb' => ['documents' => [10], 'fields' => [
                $f('napa_parents', 'NaPA ya wazazi, kama tayari ipo'),
                $f('father_nida_number', 'NIN ya baba, kama ipo'), $f('mother_nida_number', 'NIN ya mama, kama ipo'),
                $u('upload_birth_certificate', 'Cheti cha kuzaliwa cha mwanafunzi'),
                $u('upload_father_photo', 'Picha ya baba, kama inahitajika kwa maandalizi ya NaPA'),
                $u('upload_mother_photo', 'Picha ya mama, kama inahitajika kwa maandalizi ya NaPA'),
                $u('upload_previous_sponsorship_letter', 'Barua ya ufadhili uliopita (CAMFED, benki au taasisi), kama ipo'),
                $f('bank_account_number', 'Namba ya akaunti ya benki'), $f('bank_name', 'Jina la benki'),
            ]],
            'maombi-ya-chuo' => ['documents' => [10], 'fields' => [
                $f('emergency_contact_name', 'Jina la mtu wa dharura'),
                $f('emergency_relationship', 'Uhusiano na mtu wa dharura'), $f('emergency_phone', 'Simu ya mtu wa dharura', 'tel'),
            ]],
            'madai-ya-nssf' => ['documents' => [12], 'fields' => [
                $f('registered_email', 'Barua pepe iliyotumika kusajili kadi ya NSSF', 'email'),
                $f('registered_phone', 'Simu iliyotumika kusajili kadi ya NSSF', 'tel'),
                $u('upload_bank_statement', 'Bank statement / taarifa ya benki'),
                $u('upload_nida_copy', 'Kitambulisho cha taifa'),
            ]],
        ];

        $new = [
            'madai-eardhi' => [
                'name' => 'Msaada wa Kufungua Madai eArdhi', 'category' => 'other-online-forms',
                'description' => 'Andaa nyaraka ulizonazo kulingana na aina ya madai. Ukikosa nyaraka unaweza kutuma ombi hili; timu yetu itakuelekeza. Hili ni ombi la msaada wa maandalizi, si uamuzi wa shauri.',
                'documents' => [2], 'fields' => [
                    $s('claim_type', 'Aina ya madai', ['Madai ya kiwanja', 'Marekebisho ya majina', 'Nahitajia ushauri'], true),
                    $u('upload_identity', 'NIDA / kitambulisho kingine kinachokubalika'),
                    $u('upload_land_title', 'Hati ya kiwanja, kama ipo', false, ['claim_type' => ['Madai ya kiwanja']]),
                    $u('upload_sale_documents', 'Mkataba wa mauziano na risiti za malipo, kama ulinunua', false, ['claim_type' => ['Madai ya kiwanja']]),
                    $u('upload_ownership_documents', 'Barua ya ugawaji / nyaraka za urithi / uthibitisho mwingine wa umiliki', false, ['claim_type' => ['Madai ya kiwanja']]),
                    $f('plot_details', 'Namba ya kiwanja, kitalu, eneo na mipaka, kama vinajulikana', 'textarea', false, ['claim_type' => ['Madai ya kiwanja']]),
                    $u('upload_plot_plan', 'Ramani ya kiwanja, kama ipo', false, ['claim_type' => ['Madai ya kiwanja']]),
                    $f('opposing_party', 'Jina na anwani ya unayemdai, kama vinajulikana', 'textarea', false, ['claim_type' => ['Madai ya kiwanja']]),
                    $u('upload_dispute_evidence', 'Ushahidi: picha, ujumbe, barua au taarifa za ujenzi', false, ['claim_type' => ['Madai ya kiwanja']]),
                    $f('witness_details', 'Mashahidi / ushahidi mwingine ulionao', 'textarea', false, ['claim_type' => ['Madai ya kiwanja']]),
                    $u('upload_deed_poll', 'Deed poll / kiapo cha majina', true, ['claim_type' => ['Marekebisho ya majina']]),
                    $u('upload_birth_certificate', 'Cheti cha kuzaliwa', true, ['claim_type' => ['Marekebisho ya majina']]),
                    $u('upload_other_documents', 'Nyaraka nyingine zinazohusika'),
                ],
            ],
            'latra-registration' => [
                'name' => 'Msaada wa Usajili wa LATRA', 'category' => 'other-online-forms',
                'description' => 'Weka taarifa hizi za mwanzo kama zilivyo kwenye mwongozo wa huduma. Ukikosa taarifa, unaweza kuendelea ili timu yetu ikuelekeze.',
                'documents' => [0], 'fields' => [
                    $f('owner_name', 'Majina kamili / jina la kampuni', 'text', true),
                    $f('nida_number', 'Namba ya NIDA'), $f('vehicle_registration_number', 'Namba ya usajili wa gari', 'text', true),
                ],
            ],
            'uchapishaji-wa-nyuzi' => [
                'name' => 'Uchapishaji wa Nyuzi (Embroidery)', 'category' => 'printing-graphics-design',
                'description' => 'Chagua logo, digitalization, printing au kifurushi unachohitaji kulingana na mwongozo. Tuma ulicho nacho; tutawasiliana nawe kukamilisha maandalizi.',
                'documents' => [3], 'fields' => [
                    $s('service_option', 'Huduma unayotaka', ['Logo', 'Digitalization ya logo', 'Printing', 'T-shirt + Printing', 'Logo + T-shirt + Printing', 'Logo + Printing'], true),
                    $f('tshirt_quantity', 'Idadi ya T-shirt', 'number'), $u('upload_logo', 'Logo uliyonayo, kama ipo'),
                ],
            ],
            'vifungashio-vya-kisasa' => [
                'name' => 'Vifungashio vya Kisasa (Premium Paper Bags)', 'category' => 'printing-graphics-design',
                'description' => 'Chagua ukubwa wa vifungashio na idadi ya vifurushi vya vipande 100 kulingana na mwongozo. Matumizi ni pamoja na maduka, boutique na gift packaging.',
                'documents' => [11], 'fields' => [
                    $s('bag_size', 'Ukubwa wa vifungashio', ['A6', 'A5', 'A4', 'A3', 'A2'], true),
                    $f('quantity', 'Idadi ya vifurushi (kila kifurushi = vipande 100)', 'number', true),
                    $s('usage', 'Matumizi ya vifungashio', ['Maduka', 'Boutique', 'Gift packaging', 'Biashara mbalimbali']),
                ],
            ],
        ];
        foreach ($new as $slug => $profile) {
            $profiles[$slug] = $profile + ['sources' => []];
            $patches[$slug] = ['documents' => $profile['documents'], 'fields' => []];
        }
        foreach ($patches as $slug => $patch) {
            $fields = array_column($profiles[$slug]['fields'], null, 'field_key');
            foreach ($patch['remove'] ?? [] as $key) unset($fields[$key]);
            foreach ($patch['fields'] as $field) $fields[$field['field_key']] = $field;
            $profiles[$slug]['fields'] = array_values($fields);
            $profiles[$slug]['document_sources'] = array_map(fn ($index) => self::DOCUMENTS[$index], $patch['documents']);
        }

        return $profiles;
    }

    public static function affectedSlugs(): array
    {
        return array_keys(array_filter(GovernmentServiceCatalogue::definitions(), fn ($p) => ! empty($p['document_sources'])));
    }
}
