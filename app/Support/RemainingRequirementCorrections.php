<?php

namespace App\Support;

/** Follow-up evidence: TRA public manuals, Lands rectification guide and NSSF FAQ. */
final class RemainingRequirementCorrections
{
    public static function apply(array $profiles): array
    {
        $f = fn ($key, $label, $type = 'text', $review = false, $when = [], $help = null) => [
            'field_key' => $key, 'label' => $label, 'field_type' => $type, 'is_required' => false,
            'review_required' => $review, 'applies_when' => $when, 'help_text' => $help,
        ];
        $patch = function ($slug, $fields = [], $description = null, $sources = []) use (&$profiles) {
            $current = array_column($profiles[$slug]['fields'], null, 'field_key');
            foreach ($fields as $field) $current[$field['field_key']] = $field;
            $profiles[$slug]['fields'] = array_values($current);
            if ($description !== null) $profiles[$slug]['description'] = $description;
            if ($sources) $profiles[$slug]['sources'] = array_values(array_unique(array_merge($sources, $profiles[$slug]['sources'])));
        };
        $hide = function ($slug, $keys) use (&$profiles) {
            foreach ($profiles[$slug]['fields'] as &$field) {
                if (in_array($field['field_key'], $keys, true)) $field['preparation_visible'] = false;
            }
        };
        $driving = 'https://www.tra.go.tz/images/uploads/pages/Driver%E2%80%99s_License_Guide.pdf';
        $motor = 'https://www.tra.go.tz/page/motor';
        $vehicle = 'https://www.tra.go.tz/images/uploads/pages/Motor_Vehicle_Registration_Guide.pdf';
        $mainland = ['jurisdiction' => ['Tanzania Bara']];
        foreach (['driving-licence-application-renewal', 'learner-driving-licence', 'driving-licence-renewal', 'driving-licence-replacement'] as $slug) {
            $patch($slug, [
                $f('printing_region', 'Mkoa na kituo unachotaka leseni ichapishwe', review: false, when: $mainland),
            ], sources: [$driving]);
        }
        foreach (['driving-licence-application-renewal', 'learner-driving-licence'] as $slug) {
            $patch($slug, [
                $f('driving_school', 'Shule ya udereva', when: $mainland),
            ]);
        }
        $patch('driving-licence-application-renewal', [
            $f('provisional_licence_number', 'Namba ya leseni ya kujifunzia, kama tayari unayo', when: $mainland),
            $f('efd_verification_code', 'Namba ya uhakiki ya risiti ya EFD ya shule ya udereva, kama unayo', when: $mainland),
            $f('competence_test_details', 'Kituo cha Polisi na tarehe ya mtihani wa udereva, kama tayari umepangiwa', when: $mainland),
        ], 'Kwa Tanzania Bara, maombi ya leseni mpya huanza na leseni ya kujifunzia, taarifa za shule ya udereva, uhakiki wa risiti ya EFD na mtihani wa umahiri. Tuma ulivyonavyo tukuelekeze hatua uliyofikia. Zanzibar hufuata utaratibu wake.');
        $hide('driving-licence-application-renewal', ['upload_medical_form']);
        $patch('driving-licence-replacement', [
            $f('existing_licence_number', 'Namba ya leseni iliyopotea / kuharibika, kama unaikumbuka'),
            $f('replacement_reason', 'Sababu ya kuomba leseni mbadala', 'select', true) + ['options' => ['Imepotea / kuibiwa', 'Imeharibika', 'Nyingine / sijui']],
            $f('upload_police_loss_report', 'Ripoti ya upotevu wa leseni', 'file', false, $mainland + ['replacement_reason' => ['Imepotea / kuibiwa']]),
        ]);
        $patch('vehicle-related-applications', [
            $f('transfer_case', 'Hali ya uhamisho wa umiliki', 'select', true) + ['options' => ['Muuzaji anapatikana', 'Muuzaji hapatikani', 'Sijui bado']],
            $f('upload_sale_agreement', 'Mkataba wa mauziano / hati ya zawadi / hati ya rehani kulingana na uhamisho', 'file', true, [], 'Kwa muuzaji asiyepatikana, TRA hutaja mkataba wa mauziano uliothibitishwa na mwanasheria.'),
            $f('upload_lawyer_receipt', 'Risiti ya EFD ya mwanasheria', 'file', true),
            $f('upload_application_letter', 'Barua ya kuomba uhamisho wa umiliki', 'file', true),
            $f('upload_sale_efd', 'Risiti ya EFD ya mauzo kwa uhamisho wa kampuni', 'file', false, ['owner_type' => ['Kampuni / taasisi']]),
            $f('upload_ownership_affidavit', 'Hati ya kiapo cha umiliki ikiwa muuzaji hapatikani', 'file', false, ['transfer_case' => ['Muuzaji hapatikani']]),
            $f('upload_brela_confirmation', 'Uthibitisho wa BRELA kwa kampuni ikiwa muuzaji hapatikani', 'file', false, ['owner_type' => ['Kampuni / taasisi'], 'transfer_case' => ['Muuzaji hapatikani']]),
            $f('chassis_number', 'Namba ya chassis'),
            $f('transaction_date', 'Tarehe ya muamala wa umiliki', 'date'),
        ], 'Andaa TIN ya mnunuzi, kadi ya usafiri, nyaraka za uhamisho, risiti ya EFD ya mwanasheria na barua ya maombi. Uhamisho wa kampuni au muuzaji asiyepatikana una nyaraka za ziada zilizoainishwa hapa chini. Tuma ulivyonavyo tukuelekeze.', [$motor, $vehicle]);
        foreach (['vehicle-registration', 'vehicle-information-correction', 'vehicle-document-replacement'] as $slug) $patch($slug, sources: [$motor, $vehicle]);
        $patch('vehicle-registration', [
            $f('vehicle_usage', 'Matumizi ya gari: binafsi / biashara / mengine'),
            $f('owner_tin', 'TIN ya mmiliki'),
            $f('titleholder_tin', 'TIN ya mwenye haki ya hati (titleholder), kama ni tofauti na mmiliki'),
        ]);
        $patch('vehicle-information-correction', [
            $f('correction_details', 'Taarifa iliyokosewa na taarifa sahihi unayoomba', 'textarea', true),
            $f('inspection_report_number', 'Namba ya ripoti ya ukaguzi wa gari, ikiwa inahusika'),
        ]);
        $starting = 'https://www.tra.go.tz/page/starting-business-income-for-individuals-and-paying-taxes';
        $tinGuide = 'https://www.tra.go.tz/images/uploads/acts/HOW_TO_REGISTER_GET_AN_ACCOUNT_AS_WELL_AS_UPDATING_TIN_INFORMATION_IN_TAXPAYERS_PORTAL.pdf';
        $businessCase = ['taxpayer_type' => ['Mfanyabiashara binafsi', 'Kampuni / taasisi']];
        $patch('tin-registration', [
            $f('registered_phone', 'Namba ya simu iliyosajiliwa kwa NIN yako, kama unajua'),
            $f('upload_nida_copy', 'Kitambulisho: NIDA, pasipoti au kitambulisho cha mpiga kura', 'file', false),
            $f('upload_local_government_letter', 'Barua ya Serikali ya Mtaa kwa usajili wa TIN ya biashara', 'file', false, $businessCase),
            $f('upload_business_premises', 'Mkataba wa pango au hati ya eneo la biashara', 'file', false, $businessCase),
        ], 'TIN ya mtu binafsi na TIN ya biashara/taasisi zina hatua tofauti. Kwa biashara, andaa utambulisho, barua ya Serikali ya Mtaa na ushahidi wa eneo; kampuni huongeza nyaraka zake za usajili. Tuma ulivyonavyo tukuhakikie aina ya ombi na nyaraka zinazohusika.', [$starting, $tinGuide]);
        $patch('kubalishwa-jina-tin', sources: [$tinGuide, $starting]);
        $patch('tax-clearance-certificate', description: 'Tax clearance huombwa TRA baada ya hali ya kodi kuhakikiwa. Tuma TIN, madhumuni ya cheti na taarifa ulizonazo kuhusu marejesho/madeni ili tukusaidie kufuatilia. Barua si kiambatisho cha jumla kilichothibitishwa kwa kila ombi la mtandaoni.', sources: [$starting]);
        $hide('tax-clearance-certificate', ['upload_application_letter']);
        $patch('tax-return-filing-assistance', [
            $f('withholding_transactions', 'Kwa withholding: TIN za waliokatwa, tarehe, aina ya malipo, kiasi na kodi iliyokatwa', 'textarea', false, ['tax_type' => ['Withholding tax']]),
        ], sources: ['https://www.tra.go.tz/images/uploads/pages/WHT_USER_MANUAL_UPDATED.pdf', 'https://www.tra.go.tz/page/value-added-tax-vat']);
        $landGuide = 'https://www.lands.go.tz/uploads/documents/sw-1682059525-MIONGOZO%20%28FINAL%29.pdf';
        $patch('marekebisho-majina-eardhi', [
            $f('correction_case', 'Aina ya marekebisho ya jina', 'select', true) + ['options' => ['Kosa la jina kwenye hati', 'Mabadiliko ya jina kisheria', 'Sijui bado']],
            $f('existing_name', 'Jina lililoandikwa kwenye hati', review: true),
            $f('correct_name', 'Jina sahihi / jipya', review: true),
            $f('plot_details', 'Namba ya hati, kiwanja, kitalu na eneo', 'textarea'),
            $f('upload_land_title', 'Hati ya ardhi yenye jina linalorekebishwa', 'file', true),
            $f('upload_application_letter', 'Barua ya kuomba kusahihisha kosa kwenye hati', 'file', false, ['correction_case' => ['Kosa la jina kwenye hati']]),
            $f('upload_deed_poll', 'Deed poll ya mabadiliko ya jina kisheria, kama inahusika', 'file', false, ['correction_case' => ['Mabadiliko ya jina kisheria']]),
            $f('upload_birth_certificate', 'Cheti cha kuzaliwa, ikiwa kinasaidia kuthibitisha jina', 'file'),
        ], 'Kosa la jina kwenye hati huanza kwa barua kwa Kamishna wa Ardhi, uhakiki na utaratibu wa Deed of Rectification pamoja na hati halisi. Kubadili jina kisheria ni hatua tofauti inayoweza kuhusisha deed poll. Tuma ulivyonavyo ili tukuelekeze kwa ofisi husika.', [$landGuide, 'https://www.lands.go.tz/services/registration-of-tittle']);
        $hide('marekebisho-majina-eardhi', ['upload_birth_certificate']);
        $patch('madai-eardhi', [$f('complaint_details', 'Eleza mgogoro, ulipoanzia na msaada unaoomba', 'textarea', true)]);
        $nssfFaq = 'https://www.nssf.go.tz/faqs?page=3';
        foreach (['madai-ya-nssf', 'nssf-old-age-benefit', 'nssf-invalidity-benefit'] as $slug) {
            $label = match ($slug) {
                'madai-ya-nssf' => 'Barua ya kuachishwa kazi / kumalizika kwa ajira',
                'nssf-old-age-benefit' => 'Barua ya kustaafu',
                default => 'Barua kuhusu kuacha kazi kutokana na ulemavu',
            };
            $patch($slug, [$f('upload_employment_exit_letter', $label, 'file', true)], sources: [$nssfFaq]);
        }
        // Second follow-up: use the revised Acts' current section numbering.
        $taxAct = 'https://elibrary.osg.go.tz/bitstreams/92000677-95db-435e-a4ad-c5fa77752cb1/download';
        $roadAct = 'https://elibrary.osg.go.tz/bitstreams/226a0d91-d630-4fde-b01c-ef823b9ff9b0/download';
        $nssfAct = 'https://www.nssf.go.tz/uploads/publications/en-1771921274-NSSF%20ACT%20CAP%2050%20RE.%202023.pdf';
        $bot = 'https://www.bot.go.tz/Publications/Regular/Financial%20Stability/en/2026050610584713.pdf';
        $patch('kubalishwa-jina-tin', [
            $f('upload_change_notification', 'Taarifa ya maandishi kwa TRA kuhusu mabadiliko, kama tayari umeiandaa', 'file', false, [], 'Sheria inataka taarifa ya mabadiliko; timu itakusaidia kuiandaa. Nyaraka za kuthibitisha jina hutegemea aina ya marekebisho.'),
        ], 'Mabadiliko ya taarifa kwenye cheti cha TIN yanahitaji kutaarifiwa TRA kwa maandishi. Taja jina lililopo, taarifa sahihi na sababu ya marekebisho; tutahakiki ushahidi unaokubalika kwa hali yako. Tuma ulivyonavyo tukusaidie kuandaa taarifa na viambatisho.', [$taxAct]);
        $patch('driving-licence-class-addition', [
            $f('licence_category', 'Daraja unalotaka kuongeza', review: true),
            $f('current_licence_class', 'Madaraja yaliyopo kwenye leseni yako'),
            $f('upload_competence_certificate', 'Cheti cha umahiri kwa daraja unaloomba, kama tayari umepimwa', 'file', false, $mainland, 'Cheti hiki hutokana na mtihani wa udereva; cheti cha mafunzo ya shule pekee si cheti cha mtihani cha daraja husika.'),
        ], 'Taja madaraja ya leseni yako na daraja unalotaka kuongeza. Tanzania Bara, umahiri wa daraja husika unathibitishwa kwa mtihani. Timu itakuelekeza kuhusu sifa na hatua za Polisi/TRA kwa daraja lako. Tuma ulivyonavyo ili tuanze kukusaidia.', [$roadAct, $driving]);
        $hide('driving-licence-class-addition', ['upload_medical_form']);
        // A medical-board determination is explicit in section 86(2); an ordinary
        // medical letter must not be presented as an automatic invalidity approval.
        $patch('nssf-invalidity-benefit', [
            $f('upload_medical_board_assessment', 'Tathmini ya Bodi ya Madaktari kuhusu ulemavu, kama tayari imefanyika', 'file', false, [], 'Ikiwa tathmini haijafanyika, tuma ombi ili tukuelekeze hatua zinazohitajika na NSSF.'),
        ], 'Fao la ulemavu hutegemea sifa za mwanachama, michango na tathmini ya kitabibu. Uamuzi unaotegemea suala la kitabibu hupelekwa kwa Bodi ya Madaktari. Tuma ulivyonavyo; timu itathibitisha fomu na viambatisho vya madai na NSSF.', [$nssfAct]);
        $patch('madai-ya-nssf', description: 'Fao la ukosefu wa ajira hutegemea sababu ya mwisho wa ajira, umri, michango na kama umepata ajira nyingine. Masharti na aina ya malipo yahakikiwe kwa kanuni za sasa, ikiwemo marekebisho ya mwaka 2025. Tuma barua ya mwisho wa ajira na taarifa ulizonazo tukuthibitishie hatua na viambatisho na NSSF.', sources: [$bot]);
        $patch('nssf-survivors-benefit', description: 'Taja mwanachama aliyefariki na uhusiano wa mwombaji naye. Haki ya fao hutegemea hali ya familia na sifa za mwanachama; baadhi ya hali za watoto watu wazima zinahusika chini ya masharti ya sasa. Timu itathibitisha walengwa, fomu na ushahidi wa uhusiano na NSSF.', sources: [$bot]);
        // Google located these authority-hosted forms; the vehicle form was
        // rendered because its text is scanned, not extractable.
        $mv40 = 'https://www.tra.go.tz/images/uploads/forms/APPLICATION_FOR_REPLACEMENT_MOTOR_VEHICLE.pdf';
        $entityAmendment = 'https://www.tra.go.tz/images/uploads/forms/APPLICATION_FOR_TAXPAYER_ENTITY_REGISTRATION_OR_CHANGE_OF_REGISTERED_PARTICULARS.pdf';
        $patch('vehicle-document-replacement', [
            $f('chassis_number', 'Namba ya chassis', review: true, when: $mainland),
            $f('vehicle_make_model', 'Mtengenezaji na modeli ya gari', when: $mainland),
            $f('owner_identification_number', 'TIN au namba nyingine ya utambulisho wa mmiliki', when: $mainland),
            $f('owner_address', 'Anwani ya mmiliki: posta, mji na eneo', 'textarea', false, $mainland),
            $f('upload_police_loss_report', 'Ripoti ya Polisi ya kupotea kwa kadi ya gari', 'file', true, $mainland),
            $f('upload_replacement_application', 'Fomu MV40 ya maombi ya kadi mbadala, kama tayari imejazwa', 'file', false, $mainland, 'Tunaweza kukusaidia kuiandaa; kutokuwa nayo hakukuzuii kutuma ombi la msaada.'),
            $f('upload_proof_of_ownership', 'Nakala ya kadi iliyopotea, kama unayo', 'file', false),
        ], 'Kwa kadi ya usajili wa gari iliyopotea Tanzania Bara, fomu ya TRA MV40 inaelekeza kuambatanisha ripoti ya Polisi. Andaa namba ya usajili, chassis, modeli na utambulisho/anwani ya mmiliki. Tuma ulivyonavyo tukusaidie kuandaa fomu na kuthibitisha hatua za ukaguzi au nyaraka za ziada na TRA. Zanzibar hufuata utaratibu wake.', [$mv40]);
        $hide('vehicle-document-replacement', ['upload_proof_of_ownership']);
        $patch('kubalishwa-jina-tin', [
            $f('taxpayer_type', 'TIN inayorekebishwa ni ya nani?', 'select', false) + ['options' => ['Mtu binafsi', 'Kampuni / taasisi', 'Sijui bado']],
            $f('upload_entity_amendment_form', 'Fomu ya marekebisho ya TIN ya kampuni (ITX100.01.B), kama tayari imejazwa', 'file', false, ['taxpayer_type' => ['Kampuni / taasisi']], 'Kwa marekebisho, fomu inaelekeza kujaza TIN na taarifa zilizobadilika pekee. Tunaweza kusaidia kuiandaa.'),
        ], 'Taja TIN, jina lililopo, jina sahihi na sababu ya marekebisho. Kwa kampuni/taasisi, fomu ya TRA ITX100.01.B inaelekeza kujaza TIN na taarifa zilizobadilika pekee. Nyaraka za kuthibitisha mabadiliko hutegemea hali yako; tuma ulivyonavyo tukusaidie kuandaa taarifa na viambatisho.', [$entityAmendment]);
        return $profiles;
    }
}
