<?php

namespace App\Support;

/** Reviewed assistance checklists. All fields are optional at customer intake.
 * Review rules identify information staff should collect for the selected case;
 * they never certify that an authority has approved or accepted an application.
 */
final class GovernmentServiceCatalogue
{
    public const VERSION = '2026-11-06.7';

    public static function definition(string $slug): ?array
    {
        return self::definitions()[$slug] ?? null;
    }

    public static function definitions(): array
    {
        return LegacyServiceForms::apply(RemainingRequirementCorrections::apply(OfficialRequirementCorrections::apply(SeparateServiceCatalogue::apply(DocumentServiceRequirements::apply(self::baseDefinitions())))));
    }

    public static function baseDefinitions(): array
    {
        $f = fn (string $key, string $label, string $type = 'text', bool $review = true, array $when = [], ?string $help = null): array => [
            'field_key' => $key, 'label' => $label, 'field_type' => $type, 'is_required' => false,
            'help_text' => $help, 'review_required' => $review, 'applies_when' => $when,
        ];
        $s = fn (string $key, string $label, array $options, bool $review = true, array $when = [], ?string $help = null): array =>
            $f($key, $label, 'select', $review, $when, $help) + ['options' => $options];
        $u = fn (string $key, string $label, bool $review = true, array $when = [], ?string $help = null): array => $f($key, $label, 'file', $review, $when, $help);
        $p = fn (string $name, string $description, array $fields, array $sources = [], ?string $category = null): array => compact('name', 'description', 'fields', 'sources', 'category');
        $jurisdiction = $s('jurisdiction', 'Huduma / tukio linahusu eneo gani?', ['Tanzania Bara', 'Zanzibar', 'Nje ya Tanzania', 'Sijui bado']);
        $dob = $f('date_of_birth', 'Tarehe ya kuzaliwa', 'date');
        $nin = $f('nida_number', 'Namba ya Utambulisho wa Taifa (NIN), kama unayo', 'text', false);
        $address = $f('residential_address', 'Anwani ya makazi: mkoa, wilaya, kata, mtaa/kijiji na nyumba');
        $photo = $u('upload_passport_photo', 'Picha ya mwombaji');
        $identity = $u('upload_identity_document', 'Kitambulisho / ushahidi wa utambulisho unaokubalika kwa ombi lako');
        $existing = $f('official_reference', 'Namba ya ombi / kumbukumbu rasmi, kama tayari umeanza', 'text', false);
        $documents = $u('upload_supporting_docs', 'Nyaraka zinazosaidia ombi lako', false, [], 'Unaweza kuunganisha nyaraka katika PDF moja au kuleta zilizobaki baada ya kuwasiliana nasi.');
        $email = $f('email', 'Barua pepe yako inayotumika', 'email', false);
        $ritaBirth = 'https://www.rita.go.tz/page.php?lang=en&m=Registration&p=9';
        $ritaDeath = 'https://www.rita.go.tz/page.php?lang=en&m=Registration&p=10';
        $passport = 'https://eservices.immigration.go.tz/online/web/passport/apply';
        $brela = 'https://www.brela.go.tz/services/registration-of-companies';
        $business = 'https://www.brela.go.tz/services/registration-of-business-names';
        $ngo = 'https://www.jamii.go.tz/services/ngos-registration';
        $tra = 'https://taxpayerportal.tra.go.tz/';
        $heslb = 'https://www.heslb.go.tz/';
        $fullName = $f('full_name', 'Majina kamili kama yalivyo kwenye nyaraka zako');
        $parents = [
            $f('father_full_name', 'Majina ya baba, kama yanajulikana', 'text', false),
            $f('mother_full_name', 'Majina ya mama, kama yanajulikana', 'text', false),
            $f('father_nationality', 'Uraia wa baba, kama unajulikana', 'text', false),
            $f('mother_nationality', 'Uraia wa mama, kama unajulikana', 'text', false),
        ];
        $passportFields = [
            $fullName, $dob, $s('gender', 'Jinsia', ['Mwanaume', 'Mwanamke']),
            $f('place_of_birth', 'Nchi, mkoa na wilaya ya kuzaliwa'), $nin, $address, $email,
            $s('age_group', 'Umri wa mwombaji', ['Chini ya miaka 16', 'Miaka 16–17', 'Miaka 18 au zaidi', 'Sijui bado']),
            $s('citizenship_basis', 'Msingi wa uraia', ['Kuzaliwa', 'Kurithi', 'Kuandikishwa', 'Sijui bado']),
            ...$parents,
            $f('destination_country', 'Nchi unayokusudia kusafiri', 'text', false),
            $f('purpose_of_travel', 'Madhumuni ya safari / kuomba pasipoti', 'textarea', false),
            $f('guarantor1_name', 'Mdhamini 1: majina kamili', 'text', false),
            $f('guarantor1_details', 'Mdhamini 1: anwani, simu na uhusiano', 'textarea', false),
            $f('guarantor2_name', 'Mdhamini 2: majina kamili', 'text', false),
            $f('guarantor2_details', 'Mdhamini 2: anwani, simu na uhusiano', 'textarea', false),
            $u('upload_birth_certificate', 'Ushahidi wa kuzaliwa / uraia wa mwombaji', true, [], 'Cheti cha kuzaliwa, kiapo au cheti cha uraia kama kinavyokubalika na Uhamiaji kwa hali yako.'),
            $u('upload_parent_birth_certificate', 'Ushahidi wa kuzaliwa / uraia wa mzazi', true, [], 'Cheti cha kuzaliwa, kiapo au cheti cha uraia cha mzazi kinachokubalika. Ukikosa, endelea; tutakuelekeza.'),
            $photo,
            $u('upload_guardian_consent', 'Ridhaa ya mzazi / mlezi wa kisheria', true, ['age_group' => ['Chini ya miaka 16']]),
            $u('upload_travel_evidence', 'Ushahidi wa madhumuni ya safari, kama umeombwa', false),
        ];

        return [
            'nida-registration-correction' => $p('NIDA — Usajili, Marekebisho na Msaada wa NIN',
                'Chagua huduma ya NIDA unayohitaji. Kwa usajili, utahitaji kufikisha fomu iliyochapishwa na kuthibitishwa kwa saini/muhuri kwenye ofisi ya NIDA wilayani kwa hatua za biometria. Ukikosa taarifa au nyaraka, tuma ombi; tutawasiliana nawe kukuelekeza.', [
                    $s('request_type', 'Huduma unayohitaji', ['Usajili mpya', 'Marekebisho ya taarifa', 'Kutafuta NIN', 'Kitambulisho kilichopotea / kuharibika', 'Sijui bado']),
                    $jurisdiction, $fullName, $dob, $nin, $address, $email,
                    $s('citizenship', 'Uraia / hadhi', ['Raia wa kuzaliwa', 'Raia wa kurithi', 'Raia wa kuandikishwa', 'Mgeni mkazi', 'Sijui bado']),
                    ...$parents,
                    $f('father_nida_number', 'NIN ya baba, kama ipo', 'text', false),
                    $f('mother_nida_number', 'NIN ya mama, kama ipo', 'text', false),
                    $u('upload_birth_certificate', 'Cheti cha kuzaliwa / kiapo kinachokubalika', true, ['request_type' => ['Usajili mpya']], 'NIDA inaelekeza waliozaliwa 1980 na kuendelea kuleta cheti; kabla ya 1980 cheti au kiapo. Tutakushauri kuhusu hali yako.'),
                    $u('upload_parent_birth_certificate', 'Cheti cha kuzaliwa / kiapo cha mzazi mmoja', false, ['request_type' => ['Usajili mpya']]),
                    $u('upload_citizenship_certificate', 'Cheti cha uraia wa kuandikishwa (Dossier Number)', true, ['citizenship' => ['Raia wa kuandikishwa']]),
                    $u('upload_resident_permit', 'Pasipoti na kibali cha ukaazi / kazi', true, ['citizenship' => ['Mgeni mkazi']]),
                    $f('correction_details', 'Taarifa iliyopo, sahihi inayotakiwa na sababu', 'textarea', true, ['request_type' => ['Marekebisho ya taarifa']]),
                    $u('upload_correction_evidence', 'Ushahidi wa marekebisho yanayoombwa', true, ['request_type' => ['Marekebisho ya taarifa']]),
                    $u('upload_residence_letter', 'Barua / fomu ya uthibitisho wa makazi, kama tayari unayo', false), $documents,
                ], ['https://eonline.nida.go.tz/']),
            'birth-certificate-application-rita' => $p('Cheti cha Kuzaliwa — Usajili, Uhakiki au Marekebisho',
                'Tanzania Bara hutumia RITA/eRITA; Zanzibar ina utaratibu wa ZCSRA. Mahitaji hutegemea muda na mahali pa kuzaliwa, na kama cheti kimeshasajiliwa. Tuma ulivyonavyo; tutakuelekeza kuhusu vilivyobaki.', [
                    $jurisdiction,
                    $s('request_type', 'Aina ya ombi', ['Usajili ndani ya siku 90', 'Usajili baada ya siku 90', 'Cheti cha zamani kuwa kipya / uhakiki', 'Marekebisho', 'Sijui bado']),
                    $f('child_full_name', 'Majina ya mwenye cheti'), $dob,
                    $s('gender', 'Jinsia ya mwenye cheti', ['Mwanaume', 'Mwanamke']),
                    $s('birth_place_type', 'Aina ya mahali pa kuzaliwa', ['Hospitali / kituo cha afya', 'Nyumbani', 'Nyingine', 'Sijui bado']),
                    $f('birth_location', 'Nchi, mkoa, wilaya, kata na mtaa/kijiji alipozaliwa'),
                    $f('birth_hospital', 'Jina la hospitali / kituo cha afya', 'text', false, ['birth_place_type' => ['Hospitali / kituo cha afya']]),
                    $s('birth_type', 'Aina ya uzazi', ['Mtoto mmoja', 'Mapacha wawili', 'Mapacha watatu au zaidi', 'Sijui bado'], false),
                    $f('applicant_full_name', 'Majina ya anayetoa taarifa / kuomba cheti'),
                    $f('applicant_relationship', 'Uhusiano na mwenye cheti'), ...$parents,
                    $f('parents_information', 'Taarifa nyingine za kila mzazi, kama zinajulikana', 'textarea', false, [], 'Andika taarifa za baba na mama tofauti. Kama taarifa hazijulikani au mzazi hayupo, eleza hapa.'),
                    $u('upload_birth_notification', 'Tangazo / taarifa ya kuzaliwa', true, ['request_type' => ['Usajili ndani ya siku 90']]),
                    $u('upload_birth_supporting_docs', 'Ushahidi wa usajili uliochelewa', true, ['request_type' => ['Usajili baada ya siku 90']], 'Kadi ya kliniki, cheti cha ubatizo kama kipo, au barua ya mamlaka ya eneo kuhusu kuzaliwa/uraia wa wazazi kulingana na hali yako.'),
                    $u('upload_existing_certificate', 'Cheti kilichopo', true, ['request_type' => ['Cheti cha zamani kuwa kipya / uhakiki', 'Marekebisho']]),
                    $f('correction_details', 'Taarifa ya sasa na marekebisho unayotaka', 'textarea', true, ['request_type' => ['Marekebisho']]),
                    $u('upload_correction_evidence', 'Ushahidi wa marekebisho', true, ['request_type' => ['Marekebisho']]), $existing,
                ], [$ritaBirth, 'https://www.zcsra.go.tz/']),
            'death-certificate-application-rita' => $p('Cheti cha Kifo — Usajili, Uhakiki au Marekebisho',
                'RITA hutofautisha usajili ndani ya siku 30, usajili uliochelewa, uhakiki, marekebisho na kifo kilichotokea nje ya nchi. Hutahitaji cheti cha kuzaliwa cha mzazi kwa kila ombi. Tuma nyaraka ulizonazo ili tukuelekeze.', [
                    $jurisdiction, $s('request_type', 'Aina ya ombi', ['Usajili ndani ya siku 30', 'Usajili uliochelewa', 'Uhakiki wa cheti', 'Marekebisho', 'Kifo nje ya nchi', 'Sijui bado']),
                    $f('deceased_full_name', 'Majina kamili ya marehemu'), $f('date_of_death', 'Tarehe ya kifo', 'date'),
                    $f('death_location', 'Nchi, mkoa, wilaya na mahali kifo kilipotokea'),
                    $f('applicant_full_name', 'Majina ya mwombaji / mtoa taarifa'), $f('relationship_to_deceased', 'Uhusiano na marehemu'),
                    $u('upload_burial_permit', 'Kibali cha mazishi / taarifa ya kifo', true, ['request_type' => ['Usajili ndani ya siku 30', 'Usajili uliochelewa']]),
                    $u('upload_weo_death_letter', 'Barua ya mtendaji / mamlaka ya eneo', true, ['request_type' => ['Usajili uliochelewa']]),
                    $u('upload_family_minutes', 'Muhtasari wa kikao cha familia', true, ['request_type' => ['Usajili uliochelewa']]),
                    $u('upload_existing_certificate', 'Cheti kilichopo', true, ['request_type' => ['Uhakiki wa cheti', 'Marekebisho']]),
                    $f('correction_details', 'Marekebisho yanayoombwa na sababu', 'textarea', true, ['request_type' => ['Marekebisho']]),
                    $u('upload_foreign_death_certificate', 'Cheti cha kifo cha nchi tukio lilipotokea', true, ['request_type' => ['Kifo nje ya nchi']]),
                    $u('upload_embassy_letter', 'Barua ya ubalozi kuhusu tukio', true, ['request_type' => ['Kifo nje ya nchi']]),
                    $u('upload_medical_certificate', 'Taarifa ya kitabibu', true, ['request_type' => ['Kifo nje ya nchi']]),
                    $u('upload_deceased_passport', 'Pasipoti ya marehemu', true, ['request_type' => ['Kifo nje ya nchi']]),
                    $u('upload_applicant_id', 'Kitambulisho cha mwombaji', false), $documents, $existing,
                ], [$ritaDeath]),
            'police-clearance-good-conduct' => $p('Cheti cha Tabia Njema — Polisi',
                'Chagua uraia na mahali unapoishi ili tupange nyaraka sahihi. Picha ya mfumo wa Polisi ni upana 480px na urefu 640px. Tutakuelekeza kuhusu hatua za alama za vidole na ofisi husika.', [
                    $s('applicant_route', 'Uraia na makazi ya mwombaji', ['Mtanzania anayeishi Tanzania', 'Mtanzania anayeishi nje', 'Raia wa kigeni anayeishi Tanzania', 'Sijui bado']),
                    $fullName, $dob, $address,
                    $s('identity_type', 'Ushahidi wa utambulisho', ['NIDA', 'Cheti cha kuzaliwa', 'Pasipoti', 'Sijui bado'], true, ['applicant_route' => ['Mtanzania anayeishi Tanzania']]),
                    $u('upload_identity_document', 'NIDA, cheti cha kuzaliwa au pasipoti', true, ['applicant_route' => ['Mtanzania anayeishi Tanzania']]),
                    $u('upload_passport_copy', 'Nakala ya pasipoti', true, ['applicant_route' => ['Mtanzania anayeishi nje', 'Raia wa kigeni anayeishi Tanzania']]),
                    $u('upload_passport_photo', 'Picha ya mwombaji — 480px × 640px'), $existing,
                ], ['https://pcc.tpf.go.tz/']),
            'loss-report' => $p('Ripoti ya Upotevu — Maombi au Ufuatiliaji',
                'Huhitaji kuwa na ripoti ya Polisi ili kuomba msaada wa kuipata. Tutakusaidia kuandaa taarifa kwa mfumo wa Polisi (LORMIS). Ukishaanza, weka kumbukumbu ulizonazo.', [
                    $s('request_type', 'Unahitaji msaada gani?', ['Ripoti mpya', 'Kufuatilia / kupakua ripoti iliyopo', 'Sijui bado']),
                    $fullName, $f('nationality', 'Utaifa'), $s('gender', 'Jinsia', ['Mwanaume', 'Mwanamke']),
                    $f('reporter_identity_type', 'Aina ya kitambulisho cha anayetoa taarifa'),
                    $f('reporter_identity_number', 'Namba ya kitambulisho cha anayetoa taarifa'), $address,
                    $u('upload_passport_photo', 'Picha ya anayetoa taarifa — 480px × 640px'),
                    $f('document_type_lost', 'Aina na jina la mali / nyaraka iliyopotea'),
                    $f('document_number', 'Namba ya mali / nyaraka iliyopotea, kama unajua', 'text', false),
                    $f('date_of_loss', 'Tarehe ya kupotea', 'date'), $f('place_of_loss', 'Mkoa, wilaya na kata ilikopotea'),
                    $f('circumstances_of_loss', 'Maelezo mafupi ya tukio', 'textarea'), $existing,
                ], ['https://lormis.tpf.go.tz/registration/step-1']),
            'driving-licence-application-renewal' => $p('Leseni ya Udereva — Maombi, Uhuishaji au Msaada',
                'Taja aina ya ombi na eneo. Leseni mpya, uhuishaji, upotevu na kuongeza daraja zina hatua tofauti. Tutahakiki orodha ya sasa na TRA/Polisi au mamlaka ya Zanzibar kwa ombi lako; ukikosa nyaraka unaweza kuendelea.', [
                    $jurisdiction, $s('request_type', 'Aina ya ombi', ['Leseni mpya', 'Leseni ya kujifunzia', 'Kuhuisha leseni', 'Leseni iliyopotea / kuharibika', 'Kuongeza daraja', 'Sijui bado']),
                    $fullName, $dob, $nin, $f('tin_number', 'TIN, kama unayo', 'text', false),
                    $f('licence_category', 'Daraja la leseni / gari'),
                    $f('existing_licence_number', 'Namba ya leseni ya sasa', 'text', true, ['request_type' => ['Kuhuisha leseni', 'Kuongeza daraja']]),
                    $u('upload_existing_licence', 'Nakala ya leseni iliyopo', false),
                    $u('upload_competence_certificate', 'Cheti cha ufaulu / mafunzo ya udereva, kama unacho', false),
                    $u('upload_medical_form', 'Fomu ya afya/maono kama umeelekezwa kuiwasilisha', false), $documents,
                ], [$tra, 'https://www.zanrevenue.org/tax/driving-license']),
            'vehicle-related-applications' => $p('Maombi ya Gari — Usajili, Uhamisho au Nyaraka',
                'Mahitaji ya gari hutegemea huduma na aina ya mmiliki. Tuma taarifa ulizonazo; tutathibitisha hatua na nyaraka za sasa na mamlaka husika kabla ya kuwasilisha.', [
                    $jurisdiction, $s('request_type', 'Huduma ya gari', ['Usajili wa kwanza / gari kutoka nje', 'Uhamisho wa umiliki', 'Kubadilisha taarifa', 'Nyaraka zilizopotea', 'Nyingine / sijui']),
                    $s('owner_type', 'Aina ya mmiliki', ['Mtu binafsi', 'Kampuni / taasisi', 'Sijui bado']),
                    $f('owner_full_name', 'Jina la mmiliki / taasisi'), $nin,
                    $f('vehicle_registration_number', 'Namba ya usajili, kama gari limesajiliwa', 'text', false),
                    $f('chassis_number', 'Namba ya chassis'), $f('engine_number', 'Namba ya injini', 'text', false),
                    $f('vehicle_make_model', 'Aina na modeli ya gari'),
                    $f('transfer_parties', 'Majina na mawasiliano ya muuzaji na mnunuzi', 'textarea', true, ['request_type' => ['Uhamisho wa umiliki']]),
                    $u('upload_proof_of_ownership', 'Ushahidi wa umiliki uliopo', false),
                    $u('upload_import_documents', 'Nyaraka za uingizaji nchini, kama zipo', false, ['request_type' => ['Usajili wa kwanza / gari kutoka nje']]), $documents,
                ], [$tra]),
            'nhif-registration' => $p('NHIF — Msaada wa Usajili wa Uanachama',
                'Mahitaji hutegemea aina ya uanachama. Mtoto hahitaji NIN ya mtu mzima ili kutuma ombi hili la msaada. Kwa mtoto wa kundi la shule huandaliwa picha na taarifa za usajili shuleni; kwa mtoto mmoja huandaliwa picha na taarifa za kuzaliwa zilizohakikiwa.', [
                    $s('membership_route', 'Aina ya usajili', ['Mtoto mmoja', 'Mtoto kupitia shule / kundi', 'Mwanafunzi wa chuo', 'Mwajiriwa / wategemezi', 'Mtu binafsi / familia', 'Sijui bado']),
                    $fullName, $dob, $nin, $photo,
                    $f('guardian_details', 'Majina, simu na uhusiano wa mzazi/mlezi', 'textarea', true, ['membership_route' => ['Mtoto mmoja', 'Mtoto kupitia shule / kundi']]),
                    $f('birth_verification_number', 'Namba ya uhakiki wa cheti cha kuzaliwa', 'text', true, ['membership_route' => ['Mtoto mmoja']]),
                    $f('school_registration_number', 'Shule/chuo na namba ya usajili', 'text', true, ['membership_route' => ['Mtoto kupitia shule / kundi', 'Mwanafunzi wa chuo']]),
                    $f('dependants_details', 'Taarifa za wategemezi unaotaka kusajili', 'textarea', false, ['membership_route' => ['Mwajiriwa / wategemezi', 'Mtu binafsi / familia']]),
                    $documents, $existing,
                ], ['https://nhif.or.tz/faqs']),
            'new-passport-application' => $p('Maombi ya Pasipoti Mpya',
                'Tunaandaa na kusaidia maombi; Uhamiaji ndiyo hutoa pasipoti na kuamua muda wa mchakato. Tuma taarifa na vielelezo ulivyonavyo. Kila mdhamini atasaini mwenyewe sehemu zinazotakiwa na Uhamiaji. Tutawasiliana nawe kuhusu nyaraka, ridhaa na hatua zinazobaki.', $passportFields, [$passport, 'https://elibrary.osg.go.tz/bitstreams/cadb8ab8-14b1-450e-8912-0c62a8634013/download']),
            'passport-renewal' => $p('Pasipoti — Kuhuisha au Kubadilisha Iliyopotea/Kuharibika',
                'Kuhuisha kwa kuisha muda ni tofauti na kubadilisha pasipoti iliyopotea, kuibiwa au kuharibika. Ukiikosa ya zamani, tuma ombi na tutakuelekeza kuhusu taarifa ya tukio na nyaraka husika.', [
                    $s('request_type', 'Sababu ya ombi', ['Kuisha muda / kurasa kujaa', 'Kupotea / kuibiwa', 'Kuharibika', 'Sijui bado']),
                    $fullName, $dob, $nin, $f('passport_number', 'Namba ya pasipoti ya zamani, kama unajua', 'text', false),
                    $f('passport_expiry_date', 'Tarehe ya kuisha muda, kama unajua', 'date', false),
                    $u('upload_old_passport', 'Pasipoti ya zamani', true, ['request_type' => ['Kuisha muda / kurasa kujaa', 'Kuharibika']]),
                    $f('loss_details', 'Maelezo ya tukio na mamlaka iliyopokea taarifa', 'textarea', true, ['request_type' => ['Kupotea / kuibiwa', 'Kuharibika']]),
                    $u('upload_sworn_declaration', 'Tamko la kiapo kuhusu tukio, kama tayari unalo', false, ['request_type' => ['Kupotea / kuibiwa', 'Kuharibika']]), $photo, $existing,
                ], [$passport, 'https://www.za.tzembassy.go.tz/services/tanzania-online-passport-application']),
            'passport-update-correction' => $p('Marekebisho ya Taarifa za Pasipoti',
                'Taja kosa au mabadiliko unayotaka. Tutathibitisha na Uhamiaji hatua na ushahidi unaohusika; kiapo/deed poll si sharti la kila kosa la tahajia.', [
                    $s('correction_type', 'Aina ya marekebisho', ['Kosa la tahajia / taarifa', 'Jina limebadilishwa kisheria', 'Nyingine / sijui']),
                    $fullName, $nin, $f('passport_number', 'Namba ya pasipoti, kama unajua', 'text', false),
                    $f('information_to_update', 'Taarifa ilivyo sasa na sahihi unayotaka', 'textarea'),
                    $u('upload_current_passport', 'Pasipoti ya sasa', false),
                    $u('upload_supporting_document', 'Ushahidi wa taarifa sahihi / mabadiliko', false), $existing,
                ], [$passport]),
            'visa-application' => $p('Msaada wa Maombi ya Visa',
                'Chagua nchi unayoomba kuingia. Kanuni za Tanzania hazitumiki moja kwa moja kwa visa za nchi nyingine. Visa ya Tanzania hutegemea uraia na aina ya safari; mamlaka ndiyo huamua maombi. Tuma ulivyonavyo ili tukuelekeze.', [
                    $s('visa_destination', 'Visa ni ya kuingia nchi gani?', ['Tanzania', 'Nchi nyingine', 'Sijui bado']),
                    $f('destination_country', 'Jina la nchi unayoomba visa', 'text', true, ['visa_destination' => ['Nchi nyingine']]),
                    $fullName, $f('nationality', 'Uraia wa pasipoti'), $f('passport_number', 'Namba ya pasipoti'),
                    $f('passport_expiry_date', 'Tarehe ya kuisha pasipoti', 'date'),
                    $s('visa_category', 'Aina ya visa ya Tanzania', ['Ordinary / utalii au ziara', 'Multiple entry', 'Business', 'Transit', 'Student', 'Gratis', 'Sijui bado'], true, ['visa_destination' => ['Tanzania']]),
                    $f('purpose_of_visit', 'Madhumuni ya safari'),
                    $f('arrival_date', 'Tarehe inayokusudiwa kufika', 'date', false), $f('departure_date', 'Tarehe ya kutoka', 'date', false),
                    $f('host_details', 'Mwenyeji/hoteli: jina, anwani na mawasiliano', 'textarea', false),
                    $u('upload_passport_biodata', 'Ukurasa wa taarifa za pasipoti'),
                    $u('upload_passport_photo', 'Picha ya visa', true, [], 'Kwa Tanzania: JPG/JPEG, hadi 500KB kwa picha ya mwisho. Tuma uliyonayo; tunaweza kusaidia kuiandaa.'),
                    $u('upload_return_ticket', 'Tiketi ya kurudi / kuendelea na safari', true, ['visa_destination' => ['Tanzania']]),
                    $u('upload_invitation', 'Mwaliko / ushahidi wa shughuli kulingana na aina ya visa', false),
                    $documents,
                ], ['https://visa.immigration.go.tz/guidelines']),
            'residence-immigration-application' => $p('Kibali cha Ukaazi — Uhamiaji',
                'Kibali cha ukaazi kina makundi A, B na C na mahitaji tofauti. Kwa ajira, kibali cha kazi na nyaraka za mwajiri huandaliwa kabla ya hatua husika; masomo na baadhi ya shughuli zina tofauti. Tutathibitisha kundi lako na muda wa kuomba kabla ya safari.', [
                    $s('permit_class', 'Kundi la kibali', ['A — Uwekezaji / kujiajiri', 'B — Ajira', 'C — Masomo / utafiti / shughuli nyingine', 'Sijui bado']),
                    $s('request_type', 'Hatua ya ombi', ['Ombi la kwanza', 'Kuhuisha', 'Kubadilisha mwajiri / kundi', 'Sijui bado']),
                    $fullName, $f('nationality', 'Uraia'), $f('purpose_of_residence', 'Madhumuni ya ukaazi', 'textarea'),
                    $f('sponsoring_institution', 'Taasisi/mwajiri anayesimamia, kama inahusika', 'text', false),
                    $u('upload_passport_copy', 'Pasipoti ya mwombaji'), $photo,
                    $u('upload_work_permit', 'Kibali cha kazi', true, ['permit_class' => ['B — Ajira']]),
                    $u('upload_employment_contract', 'Mkataba wa ajira na maelezo ya kazi', true, ['permit_class' => ['B — Ajira']]),
                    $u('upload_qualifications', 'CV na vyeti vya kitaaluma', true, ['permit_class' => ['B — Ajira']]),
                    $u('upload_employer_documents', 'Usajili, leseni, TIN na taarifa za kodi za mwajiri', true, ['permit_class' => ['B — Ajira']]),
                    $u('upload_previous_permit', 'Kibali kilichopo/cha zamani', true, ['request_type' => ['Kuhuisha', 'Kubadilisha mwajiri / kundi']]),
                    $documents, $existing,
                ], ['https://eservices.immigration.go.tz/online/web/permit/guidelines']),
            'tin-registration' => $p('Usajili wa TIN — Mtu Binafsi au Taasisi',
                'Taja kama TIN ni ya mtu binafsi au taasisi. Taasisi ina nyaraka zake za usajili na eneo la shughuli; TIN ya mwakilishi si TIN ya kampuni. Tutakuelekeza kupitia utaratibu wa sasa wa TRA.', [
                    $s('taxpayer_type', 'Mwombaji wa TIN', ['Mtu binafsi asiye na biashara', 'Mfanyabiashara binafsi', 'Kampuni / taasisi', 'Sijui bado']),
                    $fullName, $dob, $nin, $email, $address,
                    $f('business_name', 'Jina la biashara / taasisi', 'text', true, ['taxpayer_type' => ['Mfanyabiashara binafsi', 'Kampuni / taasisi']]),
                    $f('business_address', 'Anwani na eneo la shughuli', 'textarea', true, ['taxpayer_type' => ['Mfanyabiashara binafsi', 'Kampuni / taasisi']]),
                    $u('upload_nida_copy', 'Ushahidi wa utambulisho ulionao', false),
                    $u('upload_incorporation', 'Cheti cha usajili wa kampuni / taasisi', true, ['taxpayer_type' => ['Kampuni / taasisi']]),
                    $u('upload_constitution', 'Memorandum/Articles au katiba ya taasisi', true, ['taxpayer_type' => ['Kampuni / taasisi']]),
                    $f('representative_details', 'Mwakilishi wa taasisi na mamlaka yake', 'textarea', true, ['taxpayer_type' => ['Kampuni / taasisi']]), $documents,
                ], [$tra, 'https://www.tiseza.go.tz/pages/tax-identification-number-tin']),
            'kubalishwa-jina-tin' => $p('Marekebisho ya Jina / Taarifa kwenye TIN',
                'Taja kama ni kosa la tahajia au mabadiliko ya jina kisheria. Tutahakiki ushahidi na hatua na TRA kulingana na ombi. Hakuna ulazima wa kuwa na control number au deed poll ili kutuma ombi hili la msaada.', [
                    $s('reason_for_change', 'Aina ya marekebisho', ['Kosa la tahajia / taarifa', 'Jina limebadilishwa kisheria', 'Nyingine / sijui']),
                    $f('tin_number', 'Namba ya TIN'), $f('current_tin_name', 'Jina lililopo sasa kwenye TIN'),
                    $f('correct_full_name', 'Jina / taarifa sahihi inayotakiwa'), $nin,
                    $u('upload_nida_copy', 'Ushahidi wa jina sahihi ulionao', false),
                    $u('upload_name_change_document', 'Hati ya mabadiliko ya jina, kama tayari ipo', false, ['reason_for_change' => ['Jina limebadilishwa kisheria']]), $existing,
                ], [$tra]),
            'tax-clearance-certificate' => $p('Cheti cha Kutodaiwa Kodi — Tax Clearance',
                'Kuwa na TIN pekee hakuthibitishi hali ya marejesho na madeni ya kodi. Tuma taarifa zako; tutakusaidia kukagua hatua na kumbukumbu zinazohitajika na TRA, bila kukuomba kila nyaraka iliyopo tayari kwenye mfumo wa TRA.', [
                    $s('taxpayer_type', 'Aina ya mlipakodi', ['Mtu binafsi', 'Biashara / kampuni / taasisi', 'Sijui bado']),
                    $fullName, $f('tin_number', 'TIN'), $f('purpose', 'Madhumuni ya cheti'),
                    $s('tax_records_status', 'Hali ya marejesho/madeni ya kodi', ['Yamewasilishwa na hali inajulikana', 'Kuna yaliyobaki / madeni', 'Sijui bado']),
                    $u('upload_application_letter', 'Barua/ombi la cheti, kama tayari unalo', false), $documents, $existing,
                ], [$tra, 'https://procedures.tiseza.go.tz/procedure/print/296/step/0?l=en']),
            'tax-return-filing-assistance' => $p('Msaada wa Kuwasilisha Marejesho ya Kodi',
                'Huduma hii ni ya kuwasilisha marejesho; si ombi la kurudishiwa fedha za kodi. Aina ya kodi na kipindi huamua taarifa, nyaraka na tarehe husika. Tuma ulivyonavyo tukuelekeze.', [
                    $f('tin_number', 'TIN'), $s('tax_type', 'Aina ya marejesho', ['Income tax', 'VAT', 'PAYE / SDL', 'Withholding tax', 'Nyingine / sijui']),
                    $f('filing_period', 'Mwaka na mwezi/robo/kipindi cha marejesho'),
                    $s('return_type', 'Aina ya uwasilishaji', ['Kwa mara ya kwanza', 'Marekebisho', 'Nil return', 'Sijui bado']),
                    $f('income_details', 'Taarifa za mapato / shughuli', 'textarea', false),
                    $f('expenses', 'Taarifa za matumizi yanayohusika', 'textarea', false),
                    $u('upload_financial_statements', 'Hesabu/taarifa za fedha kama zinahusika', false),
                    $u('upload_tax_records', 'Kumbukumbu za mauzo, manunuzi, mishahara au kodi zinazohusika', false), $existing,
                ], [$tra]),
            'leseni-ya-biashara' => $p('Leseni ya Biashara — BRELA au Halmashauri',
                'Leseni za kundi A hutolewa na BRELA na kundi B na mamlaka za serikali za mitaa. Taja shughuli na eneo ili tutambue mamlaka sahihi. TIN ni sehemu ya maandalizi, si leseni ya biashara yenyewe.', [
                    $s('request_type', 'Aina ya ombi', ['Leseni mpya', 'Kuhuisha leseni', 'Sijui bado']),
                    $f('business_name', 'Jina la biashara / taasisi'), $f('business_activities', 'Shughuli za biashara', 'textarea'),
                    $f('business_address', 'Anwani, mkoa na halmashauri ya biashara', 'textarea'),
                    $s('licence_authority', 'Mamlaka/kundi, kama unajua', ['BRELA — Kundi A', 'Halmashauri — Kundi B', 'Sijui bado'], false),
                    $f('tin_number', 'TIN, kama unayo', 'text', false),
                    $u('upload_registration', 'Cheti cha usajili / taarifa ya jina la biashara'),
                    $u('upload_premises_evidence', 'Ushahidi wa eneo: mkataba wa pango / umiliki'),
                    $u('upload_tax_clearance', 'Tax clearance kwa uhuishaji', true, ['request_type' => ['Kuhuisha leseni']]),
                    $u('upload_sector_permit', 'Kibali cha msimamizi wa sekta, kama shughuli inahitaji', false), $existing,
                ], ['https://www.brela.go.tz/services/patents'], 'business-services'),
            'business-registration-brela' => $p('Usajili wa Jina la Biashara — BRELA',
                'Kwa jina la biashara, BRELA inaelekeza mwombaji awe Mtanzania wa miaka 18 au zaidi, mwenye NIN na simu/barua pepe binafsi. Biashara ya ushirikiano inahitaji taarifa za kila mmiliki. Ukikosa kitu, tuma ombi tukusaidie kujiandaa.', [
                    $f('business_name', 'Jina la biashara linalopendekezwa'), $f('backup_business_name', 'Jina mbadala', 'text', false),
                    $s('ownership_type', 'Umiliki', ['Mtu mmoja', 'Ushirikiano / watu 2–20', 'Sijui bado']),
                    $f('owner_full_name', 'Majina ya mmiliki / mwakilishi'), $dob, $nin,
                    $f('citizenship', 'Uraia wa mmiliki / wamiliki'),
                    $f('email', 'Barua pepe binafsi inayotumika', 'email'),
                    $f('registered_phone', 'Simu iliyosajiliwa kwa mwombaji'),
                    $f('owner_residential_address', 'Anwani ya makazi ya mmiliki / wamiliki', 'textarea'),
                    $f('business_address', 'Anwani ya eneo la biashara', 'textarea'), $f('business_activities', 'Shughuli za biashara', 'textarea'),
                    $f('partners_details', 'Taarifa za kila mshirika', 'textarea', true, ['ownership_type' => ['Ushirikiano / watu 2–20']], 'Kwa kila mtu andika majina, NIN kama ipo, uraia, simu, barua pepe na anwani. Rudia kwa kila mshirika; tunaweza kusaidia kukamilisha baadaye.'), $documents,
                ], [$business]),
            'company-registration-brela' => $p('Usajili wa Kampuni — BRELA',
                'Tutasaidia kuandaa taarifa za kampuni, kila mwanahisa na kila mkurugenzi pamoja na hatua za wamiliki manufaa. Kama huna Memorandum/Articles, unaweza kutuma ombi ili tuziandae na kupanga uhakiki na saini.', [
                    $f('company_name', 'Jina la kampuni linalopendekezwa'),
                    $s('company_type', 'Aina ya kampuni', ['Kampuni binafsi', 'Kampuni ya umma', 'Kampuni ya kigeni', 'Sijui bado']),
                    $f('business_address', 'Anwani ya ofisi / shughuli', 'textarea'), $f('business_activities', 'Shughuli za kampuni', 'textarea'),
                    $f('shareholders_details', 'Taarifa za kila mwanahisa', 'textarea', true, [], 'Kwa kila mwanahisa: majina/jina la taasisi, uraia, NIN/pasipoti inapohusika, anwani, simu, barua pepe na hisa.'),
                    $f('directors_details', 'Taarifa za kila mkurugenzi', 'textarea', true, [], 'Kwa kila mkurugenzi: majina, uraia, NIN/pasipoti, TIN, anwani na mawasiliano.'),
                    $f('share_capital', 'Mtaji na mgawanyo wa hisa, kama inahusika', 'textarea', false),
                    $f('beneficial_owners', 'Wamiliki manufaa / watu wenye umiliki au udhibiti wa mwisho', 'textarea', false),
                    $s('needs_draft_documents', 'Memorandum/Articles au katiba', ['Naomba mnisaidie kuandaa', 'Tayari nina nyaraka', 'Sijui bado']),
                    $u('upload_memorandum', 'Memorandum / katiba iliyopo', true, ['needs_draft_documents' => ['Tayari nina nyaraka']]),
                    $u('upload_articles', 'Articles of Association zilizopo', true, ['needs_draft_documents' => ['Tayari nina nyaraka']]),
                    $u('upload_signed_forms', 'Fomu za uadilifu/majumuisho zilizosainiwa, kama zimeandaliwa', false), $documents,
                ], [$brela, 'https://bo.brela.go.tz/']),
            'business-name-search-reservation' => $p('Uhakiki wa Jina / Msaada wa Kuhifadhi Jina — BRELA',
                'Uhakiki wa awali na hifadhi rasmi ni hatua tofauti. Tutathibitisha huduma ya sasa kulingana na jina la biashara au kampuni. Hakuna uhakika wa jina kukubaliwa kabla ya uamuzi wa BRELA.', [
                    $s('entity_type', 'Jina linahusu', ['Jina la biashara', 'Kampuni', 'Sijui bado']),
                    $s('request_type', 'Msaada unaohitaji', ['Uhakiki wa awali', 'Hifadhi rasmi kama inapatikana kwa ombi hili', 'Sijui bado']),
                    $f('proposed_names', 'Majina yanayopendekezwa', 'textarea'), $existing,
                ], [$business, $brela]),
            'ngo-registration-brela' => $p('Usajili wa NGO — Msajili wa Mashirika Yasiyo ya Kiserikali',
                'Kwa Tanzania Bara, NGO husajiliwa kupitia Msajili wa NGOs na mfumo wa NIS, si usajili wa kampuni BRELA. Chagua shirika la ndani au la kimataifa. Ukikosa katiba, muhtasari au barua ya utambulisho, tuma ombi tukuelekeze.', [
                    $jurisdiction, $s('ngo_type', 'Aina ya shirika', ['La ndani — Local NGO', 'La kimataifa — International NGO', 'Sijui bado']),
                    $f('ngo_name', 'Jina la NGO'), $f('ngo_purpose', 'Malengo ya NGO', 'textarea'),
                    $f('contact_address', 'Anwani ya ofisi na ngazi ya utendaji', 'textarea'),
                    $f('founders_names', 'Taarifa za kila mwanzilishi', 'textarea', true, [], 'La ndani: waanzilishi wasiopungua 5 Watanzania. La kimataifa: wasiopungua 5, wakiwemo angalau 2 Watanzania. Andika jina, uraia na mawasiliano ya kila mmoja.'),
                    $u('upload_constitution', 'Katiba ya NGO'), $u('upload_founding_minutes', 'Muhtasari wa kikao cha waanzilishi uliosainiwa'),
                    $u('upload_recommendation', 'Barua ya utambulisho ya Msajili Msaidizi wa Halmashauri / Mkoa'),
                    $u('upload_foreign_registration', 'Cheti cha usajili cha nchi ya asili', true, ['ngo_type' => ['La kimataifa — International NGO']]),
                    $u('upload_foreign_constitution', 'Katiba kutoka nchi ya usajili', true, ['ngo_type' => ['La kimataifa — International NGO']]), $existing,
                ], [$ngo, 'https://nis.jamii.go.tz/'], 'business-services'),
            'maombi-ya-mkopo-heslb' => $p('Msaada wa Maombi ya Mkopo — HESLB',
                'Mahitaji na dirisha hutegemea mwaka, ngazi ya masomo na intake. Tutahakiki mwongozo/tangazo la sasa la HESLB kabla ya kuwasilisha. Unaweza kutuma ombi hili la msaada bila nyaraka zote; tutakusaidia kukamilisha taarifa na hatua za saini/uhakiki.', [
                    $f('student_name', 'Majina ya mwanafunzi kama yalivyo kwenye vyeti'), $dob,
                    $s('study_level', 'Ngazi ya masomo', ['Shahada — Bachelor', 'Stashahada — Diploma', 'Nyingine / nahitaji ushauri']),
                    $f('academic_year_intake', 'Mwaka wa masomo na intake unayoomba'),
                    $s('applicant_type', 'Hali ya mwombaji', ['Mwaka wa kwanza', 'Anaendelea na masomo', 'Anarejea masomoni', 'Sijui bado']),
                    $f('institution', 'Chuo/programu na hali ya udahili'), $nin,
                    $f('napa_student', 'Anwani ya makazi ya kitaifa (NaPA) ya mwanafunzi', 'text', false),
                    $f('exam_numbers', 'Namba za mitihani na miaka', 'textarea'),
                    $f('bank_details', 'Benki, akaunti hai na jina la mwenye akaunti', 'textarea', false, [], 'Jina la akaunti liendane na cheti cha CSEE kulingana na mwongozo husika.'),
                    $f('birth_certificate_verification_number', 'Namba ya uhakiki wa cheti cha kuzaliwa RITA/ZCSRA'),
                    $f('guarantor_name', 'Majina ya mdhamini'), $f('guarantor_identity', 'NIN / ZanID na umri wa mdhamini', 'text', true, [], 'Mwongozo unamtaka mdhamini awe na miaka 18 au zaidi na NIN/ZanID.'),
                    $f('guarantor_contact', 'Simu, anwani na uhusiano wa mdhamini'),
                    $s('parent_death', 'Mzazi amefariki?', ['Hapana', 'Ndiyo', 'Nahitajia ushauri'], false),
                    $f('parent_death_verification', 'Namba ya uhakiki wa kifo cha mzazi RITA/ZCSRA', 'text', true, ['parent_death' => ['Ndiyo']]),
                    $s('disability_status', 'Mwombaji au mzazi ana ulemavu?', ['Hapana', 'Ndiyo', 'Nahitajia ushauri'], false),
                    $f('disability_evidence', 'Namba ya utambulisho wa ulemavu / taarifa ya DMO au RMO', 'textarea', true, ['disability_status' => ['Ndiyo']]),
                    $f('tasaf_number', 'Namba ya mnufaika TASAF kama inahusika', 'text', false),
                    $u('upload_student_photo', 'Picha ya mwanafunzi', false), $u('upload_guarantor_photo', 'Picha ya mdhamini', false),
                    $u('upload_education_certificates', 'Vyeti vya elimu / ulinganifu wa sifa za nje inapohusika', false),
                    $u('upload_signed_application_form', 'Fomu iliyosainiwa/kuhakikiwa, kama tayari umeifikia hatua hiyo', false),
                    $u('upload_special_circumstances', 'Ushahidi wa hali maalum inayohusika', false, [], 'Mfano: ufadhili, kituo cha kulelea, mzazi gerezani au tukio la kuzaliwa/kifo nje ya nchi. Tutakuelekeza kulingana na mwongozo.'), $existing,
                ], [$heslb]),
            'maombi-ya-chuo' => $p('Msaada wa Maombi ya Chuo',
                'Dirisha la udahili hutegemea chuo, ngazi, mwaka, intake na awamu. Hakuna tarehe moja inayofunga maombi yote. Taja unachotaka; tutakagua taarifa rasmi za TCU/NACTVET/chuo na kukuelekeza.', [
                    $fullName, $f('institution', 'Chuo / vyuo unavyopendelea'), $f('preferred_programme', 'Programu na ngazi unayotaka'),
                    $f('academic_year_intake', 'Mwaka, intake na awamu kama unajua', 'text', false),
                    $s('entry_route', 'Sifa za kuingilia', ['Kidato cha sita', 'Diploma / AVN', 'Kidato cha nne', 'Sifa za nje', 'Nyingine / sijui']),
                    $f('exam_numbers', 'Namba za mitihani na miaka', 'textarea'), $f('exam_results', 'Matokeo / sifa ulizonazo', 'textarea', false),
                    $f('award_verification_number', 'AVN / namba ya ulinganifu kama inahusika', 'text', false),
                    $u('upload_exam_certificates', 'Vyeti / transcripts ulizonazo', false), $existing,
                ], ['https://tcu.go.tz/node/2706', 'https://www.nactvet.go.tz/']),
            'school-application-assistance' => $p('Msaada wa Maombi ya Shule',
                'Taja shule, mwaka na kama ni kujiunga, kuhama au kufuatilia uchaguzi. Tutathibitisha mahitaji ya shule/mpango husika; ukikosa nyaraka, tuma ombi ili tukuelekeze.', [
                    $f('student_name', 'Majina ya mwanafunzi'), $dob, $f('guardian_name', 'Jina na uhusiano wa mzazi/mlezi'),
                    $f('school_name', 'Shule unayoomba'), $f('grade_applied', 'Darasa na mwaka wa masomo'),
                    $s('request_type', 'Aina ya msaada', ['Kujiunga', 'Kuhama', 'Kufuatilia uchaguzi', 'Nyingine / sijui']),
                    $u('upload_academic_reports', 'Matokeo / taarifa za masomo, kama zipo', false), $u('upload_birth_certificate', 'Cheti cha kuzaliwa, kama kipo', false), $documents,
                ]),
            'scholarship-application-assistance' => $p('Msaada wa Maombi ya Ufadhili',
                'Mahitaji hutoka kwenye tangazo la ufadhili husika. Tuma jina au kiungo cha tangazo, mwaka na nyaraka ulizonazo; tutakagua sifa, muda na vielelezo vinavyohitajika.', [
                    $fullName, $f('scholarship_name', 'Jina la ufadhili / mfadhili'),
                    $f('scholarship_call', 'Kiungo au kumbukumbu ya tangazo na mwaka', 'textarea'),
                    $f('programme_destination', 'Programu, ngazi na nchi/chuo'),
                    $f('education_background', 'Sifa za elimu ulizonazo', 'textarea'),
                    $u('upload_call', 'Tangazo la ufadhili, kama unalo', false), $u('upload_transcripts', 'Vyeti / transcripts', false),
                    $u('upload_recommendations', 'Mapendekezo kama tangazo linahitaji na tayari unayo', false), $documents,
                ], [$heslb]),
            'exam-registration-assistance' => $p('Msaada wa Usajili wa Mitihani',
                'Taja baraza, mtihani halisi, mwaka/session na kama ni mtahiniwa binafsi au wa shule. Mitihani ya NECTA hutumia taratibu tofauti na Cambridge, IELTS au TOEFL. Tutakuelekeza kulingana na mtihani wako.', [
                    $fullName, $s('exam_body', 'Baraza / mtoa mtihani', ['NECTA', 'Cambridge', 'IELTS', 'TOEFL', 'Nyingine / sijui']),
                    $f('exam_type', 'Jina/ngazi ya mtihani (mfano CSEE, ACSEE, QT)'), $f('exam_session', 'Mwaka na session'),
                    $s('candidate_type', 'Aina ya mtahiniwa', ['Binafsi', 'Kupitia shule / kituo', 'Sijui bado']),
                    $f('candidate_reference', 'Namba ya mtahiniwa/reference kama tayari unayo', 'text', false),
                    $f('exam_subjects', 'Masomo unayotaka kusajili', 'textarea', false), $f('exam_center', 'Kituo unachotumia / kupendelea', 'text', false),
                    $u('upload_passport_photo', 'Picha ya mtahiniwa, kama unayo', false), $documents, $existing,
                ], ['https://ores.necta.go.tz/ores/']),
            'job-application-assistance' => $p('Msaada wa Maombi ya Kazi — Ajira au Mwajiri Mwingine',
                'Taja tangazo na mfumo unaotumika. Kwa Ajira Portal, wasifu, elimu, picha, waamuzi na barua ya nafasi husika huandaliwa. Tuma ulivyonavyo; tutawasiliana nawe kuhusu taarifa na nyaraka zinazobaki.', [
                    $s('application_portal', 'Mfumo wa maombi', ['Ajira Portal — PSRS', 'Mfumo mwingine wa serikali', 'Mwajiri binafsi', 'Sijui bado']),
                    $fullName, $nin, $f('vacancy_reference', 'Kiungo/kumbukumbu ya tangazo, mwajiri na nafasi', 'textarea'),
                    $f('vacancy_deadline', 'Tarehe ya mwisho kama unajua', 'date', false),
                    $f('education_background', 'Elimu na namba za mitihani inapohusika', 'textarea', false),
                    $f('referees_details', 'Waamuzi: majina, taasisi, cheo, simu na barua pepe', 'textarea', true, ['application_portal' => ['Ajira Portal — PSRS']]),
                    $u('upload_passport_photo', 'Picha ya wasifu', true, ['application_portal' => ['Ajira Portal — PSRS']]),
                    $u('upload_certificates', 'Vyeti na transcripts zinazohusika', true, ['application_portal' => ['Ajira Portal — PSRS']]),
                    $u('upload_application_letter', 'Barua ya maombi ya nafasi husika', true, ['application_portal' => ['Ajira Portal — PSRS']]),
                    $u('upload_professional_registration', 'Usajili/leseni ya taaluma kama tangazo linahitaji', false),
                    $u('upload_cv', 'CV kama unayo', false), $u('upload_birth_certificate', 'Cheti cha kuzaliwa kama kinahitajika kwa tangazo', false), $existing,
                ], ['https://portal.ajira.go.tz/']),
            'job-search-assistance' => $p('Msaada wa Kutafuta Kazi',
                'Tuma ujuzi na kazi unazotafuta. Hili ni ombi la ushauri/utafutaji; tukipata tangazo linalofaa tutakuelekeza kuhusu sifa na nyaraka za maombi husika.', [
                    $f('job_type', 'Aina ya kazi unayotafuta'), $f('education_background', 'Elimu/ujuzi/uzoefu', 'textarea'),
                    $f('preferred_location', 'Eneo unalopendelea', 'text', false), $u('upload_cv', 'CV, kama unayo', false),
                ]),
            'madai-ya-nssf' => $p('NSSF — Msaada wa Maombi ya Mafao',
                'Mafao hutegemea aina na sifa za mwanachama. Kwa ukosefu wa ajira, NSSF inaelekeza kuacha kazi bila kujiuzulu, umri chini ya miaka 55, kutokuwa na ajira mpya na muda wa kuomba ndani ya miezi 6 isipokuwa maelekezo ya NSSF. Michango ya miezi 18 au zaidi na iliyo chini ya hapo ina utaratibu tofauti; tutahakiki hali yako. Tuma ombi hata kama huna nyaraka zote au huwezi kufikia simu/email ya zamani.', [
                    $s('benefit_type', 'Aina ya fao', ['Ukosefu wa ajira', 'Uzeeni', 'Urithi / kifo', 'Uzazi', 'Ulemavu', 'Nyingine / sijui']),
                    $fullName, $dob, $nin, $f('nssf_member_number', 'Namba ya uanachama NSSF, kama unajua', 'text', false),
                    $f('employer_name', 'Mwajiri wa mwisho', 'text', false),
                    $f('termination_date', 'Tarehe ya mwisho wa ajira', 'date', true, ['benefit_type' => ['Ukosefu wa ajira']]),
                    $s('cessation_reason', 'Sababu ya mwisho wa ajira', ['Mwisho wa mkataba / kusitishwa', 'Kujiuzulu', 'Nyingine / sijui'], true, ['benefit_type' => ['Ukosefu wa ajira']]),
                    $f('contribution_months', 'Miezi ya michango, kama unajua', 'number', false, ['benefit_type' => ['Ukosefu wa ajira']]),
                    $s('reemployment', 'Umepata ajira nyingine?', ['Ndiyo', 'Hapana', 'Nahitajia ushauri'], true, ['benefit_type' => ['Ukosefu wa ajira']]),
                    $f('bank_details', 'Benki, akaunti na jina la mwenye akaunti', 'textarea', false),
                    $u('upload_termination_letter', 'Barua ya mwisho wa ajira, kama unayo', false, ['benefit_type' => ['Ukosefu wa ajira']]),
                    $u('upload_certificate_of_service', 'Cheti cha kazi, kama unacho', false),
                    $u('upload_nssf_card', 'Kadi/taarifa ya uanachama NSSF, kama unayo', false), $documents,
                ], ['https://www.nssf.go.tz/benefits/unemployment-benefit']),
            'kufuatilia-simu-iliyopotea' => $p('Msaada wa Taarifa na Ufuatiliaji wa Simu Iliyopotea',
                'Tunasaidia kuandaa na kufuatilia taarifa kupitia Polisi/mtoa huduma. TCRA inaelekeza matukio ya jinai kwa Polisi na kufungia kifaa kupitia mtoa huduma kwa IMEI na ushahidi wa umiliki. Hakuna ahadi ya kupata eneo au kuirejesha simu. Tuma ulivyonavyo; hatuhitaji password au OTP yako.', [
                    $s('report_stage', 'Umeshatoa taarifa Polisi?', ['Bado — nahitaji msaada', 'Ndiyo', 'Sijui bado']),
                    $f('phone_model', 'Aina/modeli ya simu'), $f('imei_1', 'IMEI 1, kama unajua', 'text', false),
                    $f('imei_2', 'IMEI 2, kama ipo', 'text', false), $f('sim_number_in_use', 'Namba/laini iliyokuwa kwenye simu', 'text', false),
                    $f('date_lost', 'Tarehe ya kupotea/kuibiwa', 'date'), $f('place_lost', 'Mahali ilipopotea/kuibiwa'),
                    $f('incident_description', 'Maelezo ya tukio', 'textarea'),
                    $f('police_reference', 'RB/namba ya ripoti na kituo', 'text', false, ['report_stage' => ['Ndiyo']]),
                    $f('investigator_contact', 'Mpelelezi/mawasiliano kama umepewa', 'text', false, ['report_stage' => ['Ndiyo']]),
                    $s('blocking_status', 'Hali ya taarifa kwa mtoa huduma / kufungia', ['Bado', 'Nimetoa taarifa', 'Kimefungwa', 'Sijui bado'], false),
                    $u('upload_police_report', 'Ripoti uliyonayo, kama ipo', false, ['report_stage' => ['Ndiyo']]),
                    $u('upload_purchase_receipt', 'Risiti/ushahidi wa umiliki', false), $u('upload_phone_box', 'Box lenye IMEI, kama lipo', false),
                ], ['https://www.address.tcra.go.tz/faqs']),
            'general-form-assistance' => $p('Msaada wa Jumla wa Fomu',
                'Taja taasisi na fomu halisi. Tutathibitisha toleo, hatua na vielelezo vya taasisi husika kisha tutakuelekeza; unaweza kutuma ombi kabla ya kuwa na nyaraka zote.', [
                    $f('form_name', 'Jina la fomu na taasisi'), $f('form_url', 'Kiungo / toleo la fomu kama unalo', 'textarea', false),
                    $f('form_purpose', 'Unahitaji msaada gani?', 'textarea'), $documents,
                ]),
            'ngo-membership-application' => $p('Msaada wa Kujiunga na NGO Iliyopo',
                'Hii ni huduma ya kuomba uanachama wa shirika lililopo, si kuanzisha/kusajili NGO mpya. Taja shirika ili tufuate kanuni zake za uanachama. Kwa NGO mpya tumia huduma ya usajili wa NGO.', [
                    $f('organization_name', 'Shirika unalotaka kujiunga nalo'), $f('membership_details', 'Aina ya uanachama na lengo lako', 'textarea'),
                    $f('membership_rules', 'Kiungo/maelekezo ya shirika kama unayo', 'textarea', false), $documents,
                ], [$ngo]),
            'welfarecommunity-assistance-request' => $p('Msaada wa Ombi la Ustawi / Jamii',
                'Taja mpango au taasisi unayoomba msaada. Hili ni ombi la usaidizi wa kuandaa taarifa; sifa na uamuzi hutegemea mpango husika. Ukikosa nyaraka, tutawasiliana nawe kukuelekeza.', [
                    $f('programme_name', 'Mpango / mfadhili / taasisi, kama unaijua', 'text', false),
                    $f('locality', 'Eneo unaloishi'), $f('assistance_type', 'Aina ya msaada unaotafuta'),
                    $f('reason', 'Sababu na hali unayohitaji kusaidiwa', 'textarea'), $documents,
                ]),
        ];
    }
}
