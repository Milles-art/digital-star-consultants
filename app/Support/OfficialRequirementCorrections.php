<?php

namespace App\Support;

/** Narrow corrections supported by the public authority pages recorded in the audit. */
final class OfficialRequirementCorrections
{
    public static function apply(array $profiles): array
    {
        $f = fn ($key, $label, $type = 'text', $review = false, $when = [], $help = null) => [
            'field_key' => $key, 'label' => $label, 'field_type' => $type, 'is_required' => false,
            'review_required' => $review, 'applies_when' => $when, 'help_text' => $help,
        ];
        $u = fn ($key, $label, $review = false, $when = [], $help = null) => $f($key, $label, 'file', $review, $when, $help);
        $s = fn ($key, $label, $options, $review = true) => $f($key, $label, 'select', $review) + ['options' => $options];
        $patch = function ($slug, $fields = [], $description = null, $sources = []) use (&$profiles) {
            if (! isset($profiles[$slug])) return;
            $current = array_column($profiles[$slug]['fields'], null, 'field_key');
            foreach ($fields as $field) $current[$field['field_key']] = $field;
            $profiles[$slug]['fields'] = array_values($current);
            if ($description !== null) $profiles[$slug]['description'] = $description;
            if ($sources) $profiles[$slug]['sources'] = $sources;
        };
        $hidePreparation = function ($slug, $keys) use (&$profiles) {
            foreach ($profiles[$slug]['fields'] as &$field) {
                if (in_array($field['field_key'], $keys, true)) $field['preparation_visible'] = false;
            }
            unset($field);
        };

        $patch('nida-number-lookup', [
            $f('mother_first_name', 'Jina la kwanza la mama', review: true),
            $f('mother_last_name', 'Jina la mwisho la mama', review: true),
        ], 'Kutafuta NIN kupitia huduma rasmi ya NIDA hutumia jina la kwanza, jina la mwisho, tarehe ya kuzaliwa na majina ya mama. Tuma taarifa ulizonazo tukusaidie; hutahitaji kutuma nyaraka ili kuanza ombi hili.', ['https://services.nida.go.tz/get_nin']);
        // School details and address are not inputs of the published NIN lookup.
        $profiles['nida-number-lookup']['fields'] = array_values(array_filter($profiles['nida-number-lookup']['fields'], fn ($field) => ! in_array($field['field_key'], ['primary_school', 'primary_school_year', 'residential_address'], true)));
        $patch('nida-card-replacement', [
            $s('replacement_reason', 'Sababu ya kubadilisha kitambulisho', ['Kimepotea', 'Kimechakaa / kuharibika', 'Sijui bado']),
            $u('upload_police_loss_report', 'Fomu ya Polisi ya upotevu', true, ['replacement_reason' => ['Kimepotea']]),
            $u('upload_damaged_card', 'Kitambulisho kilichochakaa / kuharibika', true, ['replacement_reason' => ['Kimechakaa / kuharibika']]),
            $u('upload_payment_receipt', 'Risiti ya malipo ya kutengeneza kitambulisho kingine, kama tayari imelipiwa'),
        ], 'Kwa kitambulisho kilichopotea, NIDA inaelekeza kuwasilisha fomu ya Polisi ya upotevu; kilichochakaa huwasilishwa chenyewe. Namba ya malipo na hatua zilizobaki hufuatwa kupitia NIDA. Tuma ulivyonavyo tukuelekeze.', ['https://nida.go.tz/index.php/Usajili-Huisha', 'https://services.nida.go.tz/']);
        $patch('nida-correction', [
            $s('correction_case', 'Taarifa unayotaka kurekebisha', ['Majina', 'Tarehe / mwezi wa kuzaliwa', 'Makazi', 'Namba ya simu', 'Nyingine / nahitaji ushauri']),
            $u('upload_correction_evidence', 'Nyaraka zinazoonyesha taarifa sahihi', true, [], 'Kwa majina: nyaraka za awali zenye jina sahihi, mfano cheti cha kuzaliwa, kadi ya kliniki au vyeti vya shule. Nyaraka hutegemea aina ya marekebisho.'),
            $u('upload_name_change_documents', 'Deed poll iliyosajiliwa na tangazo la Gazeti la Serikali, kama vinahusika', false, ['correction_case' => ['Majina']], 'NIDA hutofautisha mabadiliko ya jina, makosa ya uandishi na hali ya ndoa/talaka; timu itahakiki nyaraka za hali yako kabla ya kuwasilisha.'),
            $u('upload_marriage_divorce_evidence', 'Cheti cha ndoa / talaka au hukumu, ikiwa ndiyo sababu ya kubadili jina', false, ['correction_case' => ['Majina']]),
        ], 'Marekebisho huwasilishwa kwa NIDA wilayani kulingana na aina ya taarifa na kundi la mwombaji. Baadhi ya mabadiliko yanahitaji kibali maalumu; kutuma ombi hili hakumaanishi mabadiliko yameruhusiwa. Tuma ulivyonavyo tukuelekeze.', ['https://nida.go.tz/index.php/Usajili-Marekebisho', 'https://nida.go.tz/index.php/Mabadiliko-Majina', 'https://www.nida.go.tz/index.php/Mabadiliko-Watumishi']);
        $patch('nida-registration-correction', [
            $u('upload_parent_identity', 'Nakala ya NIDA ya mzazi mmoja kwa uraia wa kurithi', false, ['citizenship' => ['Raia wa kurithi']]),
        ], 'Usajili wa awali wa NIDA hufanywa mtandaoni au ofisini. Baada ya kujaza, fomu ichapishwe, ithibitishwe kwa saini/muhuri wa Mwenyekiti wa Serikali ya Mtaa, kisha ipelekwe NIDA wilayani pamoja na viambatisho kwa biometria. Tuma ulivyonavyo tukuelekeze.', ['https://eonline.nida.go.tz/']);

        $birth = 'https://www.rita.go.tz/page.php?p=9';
        $death = 'https://www.rita.go.tz/page.php?p=10';
        $birthEvidence = 'Kwa usajili uliochelewa Tanzania Bara, RITA inaelekeza vielelezo viwili: kadi ya kliniki ya mama/mtoto, tangazo la kuzaliwa, cheti cha ubatizo cha utotoni, cheti cha kumaliza shule, pasipoti au kitambulisho. Si kila kimoja kinahitajika kwa pamoja.';
        $parentEvidence = 'Kitambulisho cha mzazi/mlezi, pasipoti ya mzazi au utambulisho wa Mtendaji wa Kata. Vitambulisho vya wazazi hutumika ikiwa wazazi wapo hai.';
        $mainland = ['jurisdiction' => ['Tanzania Bara']];
        $late = $mainland + ['request_type' => ['Usajili baada ya siku 90']];
        $patch('birth-certificate-application-rita', [
            $u('upload_birth_notification', 'Tangazo / taarifa ya kuzaliwa', true, $mainland + ['request_type' => ['Usajili ndani ya siku 90']], 'Kwa waliozaliwa kituo cha afya hupatikana huko; kwa waliozaliwa nyumbani, fuata utaratibu wa Mtendaji wa Kata au Msajili wa Wilaya.'),
            $u('upload_birth_supporting_docs', 'Vielelezo viwili vya kuthibitisha kuzaliwa', true, $late, $birthEvidence),
            $u('upload_parent_guardian_identity', 'Kitambulisho / utambulisho wa mzazi au mlezi', false, $mainland, $parentEvidence),
        ], 'RITA hushughulikia vizazi vya Tanzania Bara; Zanzibar hutumia ZCSRA. Usajili wa watoto wachanga ndani ya siku 90 na usajili uliochelewa vina hatua tofauti. Kwa mtoto aliyezaliwa nje ya nchi, utaratibu wa utambuzi wa taarifa hutumika. Tuma ulivyonavyo tukuelekeze.', [$birth, 'https://ecrvs.zcsra.go.tz/Birth/Form']);
        $hidePreparation('birth-certificate-application-rita', ['upload_additional_birth_evidence']);
        $patch('birth-certificate-old-to-new', [
            $u('upload_birth_supporting_docs', 'Vielelezo viwili vya kuthibitisha taarifa za cheti', true, $mainland, $birthEvidence),
            $u('upload_parent_guardian_identity', 'Kitambulisho cha mzazi/mlezi au pasipoti ya mzazi', false, $mainland, 'Hutumiwa ikiwa wazazi wapo hai.'),
        ], 'Hii ni kubadilisha cheti cha zamani kuwa cha kielektroniki kupitia huduma ya Old to New, tofauti na uhakiki pekee. Kwa Tanzania Bara, andaa cheti kilichopo na vielelezo vya taarifa zake kama RITA inavyoelekeza. Tuma ulivyonavyo tukuelekeze.', [$birth]);
        $patch('birth-certificate-verification', [], 'Uhakiki wa RITA hutumia nakala ya cheti cha kuzaliwa kwenye huduma ya Verification; si usajili mpya wala kubadilisha cheti cha zamani kuwa kipya. Tuma cheti ulicho nacho tukusaidie.', [$birth]);
        foreach (['birth-certificate-correction' => $birth, 'death-certificate-correction' => $death] as $slug => $source) {
            $patch($slug, [
                $u('upload_correction_letter', 'Barua ya kuomba marekebisho', true, $mainland),
                $u('upload_correction_evidence', 'Nyaraka zinazoonyesha taarifa sahihi', true, $mainland, 'Vitambulisho au vyeti vinavyothibitisha taarifa inayorekebishwa; affidavit inaweza kuhitajika kulingana na ombi.'),
            ], 'Kwa Tanzania Bara, RITA inaelekeza marekebisho kushughulikiwa ofisi za Makao Makuu au Wilaya. Andaa barua ya ombi, cheti halisi na ushahidi wa taarifa sahihi; timu itakuelekeza kabla ya kuwasilisha.', [$source]);
        }
        $profiles['death-certificate-correction']['description'] .= ' Marekebisho ya jina la marehemu yanahusu herufi zilizokosewa; si kubadilisha au kuondoa jina lote.';
        $patch('death-certificate-application-rita', [
            $u('upload_family_minutes', 'Muhtasari wa kikao cha ndugu wa kumteua msimamizi wa mirathi', false, $mainland, 'RITA hutaja muhtasari uliothibitishwa na Mtendaji wa Kata/Kijiji pamoja na utambulisho wa msimamizi.'),
            $u('upload_weo_death_letter', 'Barua ya Mtendaji wa Kata/Kijiji kuthibitisha kifo na kumtambulisha mwombaji', false, $mainland),
            $u('upload_deceased_identity', 'NIDA au kitambulisho cha mpiga kura cha marehemu', false, $mainland),
            $u('upload_relationship_evidence', 'Cheti cha ndoa au cheti cha kuzaliwa cha mtoto, ikiwa mwombaji ni mwenza au mtoto', false, $mainland),
            $s('death_age_band', 'Muda tangu kifo kitokee', ['Miaka 10 au chini', 'Zaidi ya miaka 10', 'Sijui bado']),
            $u('upload_ward_security_minutes', 'Muhtasari wa Kamati ya Ulinzi na Usalama ya Kata na vielelezo vya wajumbe', false, $mainland + ['death_age_band' => ['Zaidi ya miaka 10']]),
            $u('upload_delay_affidavit', 'Hati ya kiapo inayoeleza sababu za kuchelewa kusajili kifo', false, $mainland + ['death_age_band' => ['Zaidi ya miaka 10']]),
        ], 'Kwa Tanzania Bara kifo husajiliwa ndani ya siku 30; kilichochelewa huhitaji vielelezo vya kuthibitisha tukio. RITA ina maelekezo ya ziada kwa kifo cha zaidi ya miaka 10. Zanzibar hutumia ZCSRA. Tuma ulivyonavyo tukuelekeze.', [$death, 'https://www.zcsra.go.tz/vifo/']);
        $hidePreparation('death-certificate-application-rita', ['upload_deceased_birth_certificate', 'upload_parent_birth_certificate', 'upload_family_identity', 'upload_residence_letter']);
        $patch('death-certificate-verification', [], 'Uhakiki wa RITA hutumia nakala ya cheti cha kifo kilichopo kupitia huduma ya Verification. Hii si usajili mpya wa tukio la kifo. Tuma cheti ulicho nacho tukusaidie.', [$death]);
        $patch('foreign-death-registration', [
            $u('upload_foreign_death_certificate', 'Cheti cha kifo cha nchi tukio lilipotokea au ripoti ya kitabibu ya hospitali', true),
            $u('upload_medical_certificate', 'Ripoti ya kitabibu, ikiwa ndiyo ushahidi wa kifo unaotumika', false),
            $u('upload_deceased_birth_certificate', 'Cheti cha kuzaliwa cha marehemu, ikiwa kipo'),
            $u('upload_certificate_translation', 'Tafsiri ya cheti, ikiwa hakijaandikwa kwa Kiswahili au Kiingereza'),
        ], 'Huduma hii ni ya utambuzi na RITA wa kifo kilichotokea nje ya Tanzania, kilichosajiliwa nchini tukio lilipotokea. Andaa ushahidi wa kifo, pasipoti ya marehemu na barua ya Ubalozi wa Tanzania. Tafsiri huhitajika kwa cheti kisicho cha Kiswahili au Kiingereza.', [$death]);
        $profiles['foreign-death-registration']['name'] = 'Utambuzi wa Kifo Kilichotokea Nje ya Nchi';

        $passport = 'https://eservices.immigration.go.tz/online/web/passport/guidelines';
        $photo = $u('upload_passport_photo', 'Picha ya hivi karibuni yenye mandhari ya buluu isiyokoza', true);
        $identity = $u('upload_national_identity', 'Kitambulisho cha Taifa', true);
        $travel = $u('upload_travel_evidence', 'Ushahidi wa safari au wa shughuli anazofanya mwombaji', true);
        $passportLaw = 'https://elibrary.osg.go.tz/bitstreams/cadb8ab8-14b1-450e-8912-0c62a8634013/download';
        $patch('new-passport-application', [$photo, $identity, $travel,
            $u('upload_birth_certificate', 'Cheti cha kuzaliwa cha mwombaji', true, [], 'Kwa ushahidi mbadala wa kuzaliwa/uraia, timu itathibitisha na Uhamiaji kulingana na hali yako.'),
            $u('upload_parent_birth_certificate', 'Cheti au kiapo cha kuzaliwa cha mzazi wa mwombaji', true),
        ], null, [$passport, $passportLaw, 'https://eservices.immigration.go.tz/online/web/passport/']);
        $hidePreparation('new-passport-application', ['upload_education_certificates', 'upload_residence_letter', 'upload_passport_request_letter']);
        foreach (['passport-renewal', 'passport-replacement'] as $slug) {
            $patch($slug, [$photo, $identity, $travel,
                $u('upload_birth_certificate', 'Cheti cha kuzaliwa cha mwombaji', false),
                $u('upload_parent_birth_certificate', 'Cheti au kiapo cha kuzaliwa cha mzazi', false),
            ], null, [$passport, 'https://www.uk.tzembassy.go.tz/services/tanzania-citizen-services']);
        }
        $patch('passport-replacement', [$u('upload_police_loss_report', 'Ripoti ya Polisi kuhusu pasipoti iliyopotea au kuibiwa', false, ['request_type' => ['Kupotea / kuibiwa']])]);
        $patch('passport-update-correction', [], null, ['https://www.uk.tzembassy.go.tz/services/tanzania-citizen-services', $passport]);

        foreach (['residence-immigration-application', 'residence-permit-renewal', 'residence-permit-change'] as $slug) {
            $classA = ['permit_class' => ['A — Uwekezaji / kujiajiri']];
            $patch($slug, [
                $u('upload_application_letter', 'Barua ya maombi ya kibali cha ukaazi', false),
                $u('upload_work_permit', 'Kibali cha kazi', true, ['permit_class' => ['A — Uwekezaji / kujiajiri', 'B — Ajira']], 'Sharti hutegemea kundi na shughuli; walio kwenye msamaha hufuata maelekezo ya Uhamiaji.'),
                $u('upload_investment_registration', 'Usajili wa biashara/kampuni na katiba au taarifa ya msajili', false, $classA),
                $u('upload_investment_tax_records', 'TIN na kumbukumbu za kodi zinazohusika na uwekezaji', false, $classA),
                $u('upload_investment_licence', 'Leseni ya biashara na vibali vya sekta vinavyohusika', false, $classA),
                $u('upload_investment_premises', 'Mkataba wa pango au hati ya eneo la shughuli', false, $classA),
                $f('class_c_activity', 'Kwa Kundi C: shughuli au aina ya kibali unachoomba', 'textarea', false, ['permit_class' => ['C — Masomo / utafiti / shughuli nyingine']]),
            ], 'Mahitaji ya ukaazi hutegemea kundi A, B au C na shughuli husika. Nyaraka za maombi rasmi zinahitaji kuthibitishwa, na zilizo katika lugha nyingine hutafsiriwa kwa Kiswahili au Kiingereza. Tutathibitisha orodha ya kundi lako na hatua ya kibali cha kazi kabla ya kuwasilisha.', ['https://eservices.immigration.go.tz/online/web/permit/guidelines']);
        }
        $patch('residence-permit-change', [$u('upload_no_objection', 'Barua ya kutokuwa na pingamizi kutoka taasisi/mwajiri wa awali, ikiwa unahamia nyingine')]);
        $patch('nhif-registration', [
            $s('family_dependants', 'Kwa uanachama wa familia: unamsajili nani pamoja nawe?', ['Hakuna', 'Mwenza', 'Watoto', 'Mwenza na watoto', 'Sijui bado'], false),
            $u('upload_spouse_identity', 'Kitambulisho cha Taifa cha mwenza', false, ['membership_route' => ['Mtu binafsi / familia'], 'family_dependants' => ['Mwenza', 'Mwenza na watoto']]),
            $f('marriage_verification_number', 'Namba ya uhakiki wa cheti cha ndoa RITA', 'text', false, ['membership_route' => ['Mtu binafsi / familia'], 'family_dependants' => ['Mwenza', 'Mwenza na watoto']]),
            $u('upload_dependant_photos', 'Picha za kila mtegemezi anayesajiliwa', false, ['membership_route' => ['Mtu binafsi / familia'], 'family_dependants' => ['Mwenza', 'Watoto', 'Mwenza na watoto']]),
            $f('children_birth_verification', 'Namba za uhakiki wa vyeti vya kuzaliwa vya watoto RITA', 'textarea', false, ['membership_route' => ['Mtu binafsi / familia'], 'family_dependants' => ['Watoto', 'Mwenza na watoto']]),
        ], null, ['https://www.nhif.or.tz/faqs/je-ni-mahitaji-gani-yanatakiwa-ili-kumsajili-mtoto-katika-bima-ya-afya', 'https://www.nhif.or.tz/faqs/utaratibu-wa-kusajiliwa-katika-vifurushi-vya-ngorongoro-afya-na-serengeti-afya-ni-upi']);
        // Family-only selector is an intake convenience and never a government prerequisite.
        foreach ($profiles['nhif-registration']['fields'] as &$field) {
            if ($field['field_key'] === 'family_dependants') $field['applies_when'] = ['membership_route' => ['Mtu binafsi / familia']];
        }
        unset($field);
        $patch('maombi-ya-mkopo-heslb', [
            $f('napa_guarantor', 'Anwani ya makazi ya kitaifa (NaPA) ya mdhamini', 'text', false),
        ], null, ['https://www.heslb.go.tz/images/storage/downloads/1781868431_2-bachelor-degree-guidelines-for-2026-2027.pdf', 'https://www.heslb.go.tz/images/storage/downloads/1781874797_1-diploma-guidelines-for-2026-2027-fnl-resized-pdfresizercom.pdf', 'https://www.heslb.go.tz/index.php/news/37']);
        $hidePreparation('maombi-ya-mkopo-heslb', ['upload_father_photo', 'upload_mother_photo']);
        $patch('maombi-ya-chuo', [], 'Mahitaji hutegemea chuo, kozi, ngazi na mwaka wa udahili. Shahada huombwa moja kwa moja chuoni; kwa baadhi ya kozi za afya za astashahada/stashahada Tanzania Bara hutumika NACTVET CAS. Taja chuo na kozi ili tuthibitishe orodha yake. Tuma ulivyonavyo tukuelekeze.', ['https://www.tcu.go.tz/index.php/services/students-admissions-and-transfers/undergraduate-admissions', 'https://www.nactvet.go.tz/page/downloads']);
        $patch('job-application-assistance', [$u('upload_certificates', 'Vyeti vya elimu na transcripts zinazohusika', true, ['application_portal' => ['Ajira Portal — PSRS']], 'Fuata tangazo la nafasi husika kuhusu uthibitisho wa nakala; muhuri wa mwanasheria si sharti la jumla kwa kila ombi.')]);
        $patch('business-registration-brela', [], null, ['https://www.brela.go.tz/index.php/services/registration-of-business-names', 'https://bos.brela.go.tz/']);
        $patch('company-registration-brela', [$f('applicant_nin', 'NIN ya mwombaji anayefungua ombi', review: true)], null, ['https://www.brela.go.tz/services/registration-of-companies', 'https://bos.brela.go.tz/', 'https://bo.brela.go.tz/']);
        foreach (['leseni-ya-biashara', 'business-licence-renewal'] as $slug) {
            $patch($slug, [
                $s('licence_applicant_type', 'Leseni inaombwa na nani?', ['Mtu binafsi', 'Jina la biashara', 'Kampuni / taasisi', 'Sijui bado']),
                $u('upload_business_extract', 'Taarifa ya msajili (extract) ya jina la biashara', false, ['licence_applicant_type' => ['Jina la biashara']], 'Orodha ya BRELA ya Kundi A; kwa Halmashauri tutathibitisha maelekezo ya eneo lako.'),
                $u('upload_citizenship_residence', 'Ushahidi wa uraia/ukaazi wa wakurugenzi na wanahisa, kama unahusika', false, ['licence_applicant_type' => ['Kampuni / taasisi']]),
            ]);
        }
        $patch('leseni-ya-biashara', [$u('upload_tax_clearance', 'Tax clearance kwa mwombaji binafsi, kulingana na maelekezo ya mamlaka', false, ['licence_applicant_type' => ['Mtu binafsi']], 'BRELA huitaja kwa mtu binafsi; kwa kampuni/jina la biashara huitaja kwa uhuishaji. Tutathibitisha mamlaka ya leseni yako.')]);
        $patch('business-name-reservation', [], 'Hifadhi ya jina la kampuni ina msingi kwenye Sheria ya Kampuni; kukubalika na muda hutegemea uamuzi wa BRELA. Kwa jina la biashara tutathibitisha huduma inayopatikana kwenye BOS. Hifadhi si cheti cha usajili.', ['https://brela.go.tz/uploads/documents/sw-1758107576-THE%20COMPANIES%20ACT%2C%20REVISED%20EDITION%2C%202023.pdf', 'https://bos.brela.go.tz/']);
        $patch('vehicle-related-applications', [
            $u('upload_sale_agreement', 'Mkataba wa mauziano ya usafiri', true, [], 'Timu itathibitisha na TRA namna ya uthibitisho na nyaraka zinazotakiwa kwa uhamisho wako.'),
        ], 'Andaa TIN za muuzaji na mnunuzi, kadi ya usafiri na nyaraka za mauziano ulizonazo. Timu itathibitisha na TRA uthibitisho na hatua zinazohusika kwa uhamisho wako. Unaweza kutuma ombi bila nyaraka zote.', ['https://taxpayerportal.tra.go.tz/']);
        // The follow-up TRA motor-vehicle page explicitly lists the lawyer's EFD receipt.
        $patch('driving-licence-replacement', [
            $f('jurisdiction', 'Leseni imetolewa wapi?', 'select', true) + ['options' => ['Tanzania Bara', 'Zanzibar', 'Sijui bado']],
            $u('upload_zanzibar_loss_declaration', 'Fomu TLD 606 ya tamko la leseni iliyopotea/kuibiwa', false, ['jurisdiction' => ['Zanzibar']]),
            $u('upload_zanzibar_police_statement', 'Taarifa ya Polisi kwa leseni ya Zanzibar iliyopotea/kuibiwa', false, ['jurisdiction' => ['Zanzibar']]),
        ], null, ['https://taxpayerportal.tra.go.tz/', 'https://www.zanrevenue.org/uploads/forms/zrb_vehicle_tld_606.pdf']);
        $patch('latra-registration', [
            $s('registrant_type', 'Aina ya mwombaji wa usajili RRIMS', ['Mtu binafsi', 'Kampuni / taasisi', 'Sijui bado']),
            $f('email', 'Barua pepe inayotumika', 'email', true),
            $f('company_registration_number', 'Namba ya usajili wa kampuni/taasisi', 'text', true, ['registrant_type' => ['Kampuni / taasisi']]),
            $u('upload_company_registration', 'Cheti cha usajili wa kampuni/taasisi', false, ['registrant_type' => ['Kampuni / taasisi']]),
        ], 'Huduma hii ni ya usajili wa mwombaji kwenye RRIMS, si leseni ya gari. Mtu binafsi hutumia NIDA; kampuni/taasisi hutumia usajili wake. Utahitaji barua pepe kwa hatua za akaunti. Tuma taarifa ulizonazo tukuelekeze.', ['https://www.latra.go.tz/howdois/kujisajili-kwenye-mfumo-wa-rrims', 'https://www.latra.go.tz/howdois/how-to-apply-for-licence']);
        foreach ($profiles['latra-registration']['fields'] as &$field) {
            if ($field['field_key'] === 'nida_number') $field['applies_when'] = ['registrant_type' => ['Mtu binafsi']];
            if ($field['field_key'] === 'vehicle_registration_number') $field['review_required'] = false;
        }
        unset($field);
        $profiles['latra-registration']['name'] = 'Usajili wa Mwombaji kwenye RRIMS — LATRA';
        $patch('madai-eardhi', [], 'Tunakusaidia kuandaa taarifa na nyaraka za malalamiko/mgogoro wa ardhi kupitia njia inayohusika. e-Ardhi ina huduma ya malalamiko na migogoro; kuwasilisha malalamiko hakumaanishi kufungua shauri rasmi Barazani. Tuma vielelezo ulivyonavyo ili tuthibitishe hatua sahihi.', ['https://eardhi.lands.go.tz/', 'https://lands.go.tz/news/kilimanjaro-waanza-kutumia-mfumo-wa-e-ardhi']);
        $patch('marekebisho-majina-eardhi', [], 'Taja jina lililopo na linalotakiwa kwenye kumbukumbu ya ardhi. Andaa hati au kumbukumbu inayohusika na ushahidi wa jina. Tutathibitisha nyaraka na ofisi ya ardhi; deed poll na cheti cha kuzaliwa si orodha ya lazima iliyothibitishwa kwa kila marekebisho.', ['https://www.lands.go.tz/services/registration-of-tittle']);
        $patch('madai-ya-nssf', [], 'Fao hili linahusu kuacha ajira kwa sababu nyingine isipokuwa kujiuzulu, kwa mwanachama aliye chini ya miaka 55 na ambaye hajapata ajira nyingine. Kwa kawaida maombi huwa ndani ya miezi 6; NSSF inaweza kuidhinisha kuchelewa kwa sababu zinazokubalika. Tuma ulivyonavyo tukuhakikie sifa na viambatisho.', ['https://www.nssf.go.tz/benefits/unemployment-benefit']);
        $patch('nssf-old-age-benefit', [], 'Fao la uzeeni hutegemea umri na michango ya mwanachama. Tuma taarifa ulizonazo ili timu ithibitishe fomu na nyaraka za fao lako na NSSF. Hii si huduma ya fao la ukosefu wa ajira.', ['https://www.nssf.go.tz/benefits/old-age-pension']);
        $patch('nssf-survivors-benefit', [
            $f('deceased_member_name', 'Majina ya mwanachama aliyefariki', review: true),
            $f('claimant_name', 'Majina ya mtegemezi / mwombaji', review: true),
            $f('relationship_to_member', 'Uhusiano na mwanachama aliyefariki', review: true),
        ], 'Fao hili ni la wategemezi wa mwanachama aliyefariki. Uhusiano na sifa za mwanachama huamua ombi; timu itathibitisha fomu na ushahidi wa uhusiano unaohusika na NSSF.', ['https://www.nssf.go.tz/benefits/survivors-pension']);
        $patch('nssf-maternity-benefit', [
            $u('upload_maternity_medical_evidence', 'Cheti / uthibitisho wa kitabibu kuhusu ujauzito au kujifungua', true),
        ], 'Fao la uzazi hutegemea sifa za mwanachama, michango na uthibitisho wa kitabibu. Tuma taarifa ulizonazo ili timu ithibitishe hatua na viambatisho vyako na NSSF.', ['https://www.nssf.go.tz/benefits/maternity-benefits']);
        $patch('nssf-invalidity-benefit', [], 'Fao la ulemavu hutegemea tathmini ya ulemavu wa kudumu, umri na michango. Tuma taarifa ulizonazo ili timu ithibitishe tathmini, fomu na viambatisho vinavyohusika na NSSF.', ['https://www.nssf.go.tz/uploads/publications/en-1771921274-NSSF%20ACT%20CAP%2050%20RE.%202023.pdf']);
        $patch('scholarship-application-assistance', [], null, ['https://www.moe.go.tz/sites/default/files/TANGAZO%20LA%20CHINA.pdf']);
        $patch('school-transfer-assistance', [], 'Taja shule ya sasa, unayotaka kuhamia, darasa na mwaka. Uhamisho huhitaji maelekezo ya shule na mamlaka ya elimu husika; nyaraka na uamuzi hutegemea hali yako. Tuma ulivyonavyo tukuelekeze.', ['https://tamisemi.go.tz/']);
        $patch('school-selection-follow-up', [], 'Taja mwaka wa uchaguzi, ngazi na namba ya mtihani/usajili. Tutakusaidia kufuatilia taarifa rasmi za uchaguzi; picha au cheti cha kuzaliwa si sharti la jumla la kuangalia matokeo ya uchaguzi.', ['https://selform.tamisemi.go.tz/']);
        $hidePreparation('school-selection-follow-up', ['upload_academic_reports', 'upload_birth_certificate']);
        // Persist why preparation and intake can differ, without exposing research labels in the form.
        return $profiles;
    }
}
