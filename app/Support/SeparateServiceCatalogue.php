<?php

namespace App\Support;

/** Resolves previously combined actions into individual service definitions. */
final class SeparateServiceCatalogue
{
    public static function groups(): array
    {
        // slug, title, selected action(s), relevant common fields (null = all).
        return [
            'nida-registration-correction' => ['request_type', [
                ['nida-registration-correction', 'Usajili wa NIDA', ['Usajili mpya'], null],
                ['nida-correction', 'Marekebisho ya Taarifa za NIDA', ['Marekebisho ya taarifa'], ['full_name', 'nida_number', 'email', 'correction_details', 'upload_correction_evidence']],
                ['nida-number-lookup', 'Kutafuta Namba ya NIN', ['Kutafuta NIN'], ['full_name', 'date_of_birth', 'residential_address', 'primary_school', 'primary_school_year', 'email']],
                ['nida-card-replacement', 'Kubadilisha Kitambulisho cha NIDA', ['Kitambulisho kilichopotea / kuharibika'], ['full_name', 'nida_number', 'email']],
            ]],
            'birth-certificate-application-rita' => ['request_type', [
                ['birth-certificate-application-rita', 'Usajili wa Cheti cha Kuzaliwa', ['Usajili ndani ya siku 90', 'Usajili baada ya siku 90'], null],
                ['birth-certificate-verification', 'Uhakiki wa Cheti cha Kuzaliwa', ['Cheti cha zamani kuwa kipya / uhakiki'], ['jurisdiction', 'child_full_name', 'applicant_full_name', 'applicant_relationship', 'upload_existing_certificate', 'upload_applicant_id', 'official_reference']],
                ['birth-certificate-old-to-new', 'Kubadilisha Cheti cha Kuzaliwa cha Zamani kuwa Kipya', ['Cheti cha zamani kuwa kipya / uhakiki'], ['jurisdiction', 'child_full_name', 'applicant_full_name', 'applicant_relationship', 'upload_existing_certificate', 'upload_applicant_id', 'official_reference']],
                ['birth-certificate-correction', 'Marekebisho ya Cheti cha Kuzaliwa', ['Marekebisho'], ['jurisdiction', 'child_full_name', 'applicant_full_name', 'applicant_relationship', 'upload_existing_certificate', 'correction_details', 'upload_correction_evidence', 'upload_applicant_id', 'official_reference']],
            ]],
            'death-certificate-application-rita' => ['request_type', [
                ['death-certificate-application-rita', 'Usajili wa Cheti cha Kifo', ['Usajili ndani ya siku 30', 'Usajili uliochelewa'], null],
                ['death-certificate-verification', 'Uhakiki wa Cheti cha Kifo', ['Uhakiki wa cheti'], ['jurisdiction', 'deceased_full_name', 'applicant_full_name', 'relationship_to_deceased', 'upload_existing_certificate', 'upload_applicant_id', 'official_reference']],
                ['death-certificate-correction', 'Marekebisho ya Cheti cha Kifo', ['Marekebisho'], ['jurisdiction', 'deceased_full_name', 'applicant_full_name', 'relationship_to_deceased', 'upload_existing_certificate', 'correction_details', 'upload_applicant_id', 'upload_supporting_docs', 'official_reference']],
                ['foreign-death-registration', 'Usajili wa Kifo Kilichotokea Nje ya Nchi', ['Kifo nje ya nchi'], ['deceased_full_name', 'date_of_death', 'death_location', 'applicant_full_name', 'relationship_to_deceased', 'upload_foreign_death_certificate', 'upload_embassy_letter', 'upload_medical_certificate', 'upload_deceased_passport', 'upload_applicant_id', 'official_reference']],
            ]],
            'passport-renewal' => ['request_type', [
                ['passport-renewal', 'Kuhuisha Pasipoti', ['Kuisha muda / kurasa kujaa'], null],
                ['passport-replacement', 'Kubadilisha Pasipoti Iliyopotea au Kuharibika', ['Kupotea / kuibiwa', 'Kuharibika'], null],
            ]],
            'driving-licence-application-renewal' => ['request_type', [
                ['driving-licence-application-renewal', 'Maombi ya Leseni Mpya ya Udereva', ['Leseni mpya'], ['jurisdiction', 'full_name', 'date_of_birth', 'nida_number', 'tin_number', 'licence_category', 'upload_competence_certificate', 'upload_medical_form']],
                ['learner-driving-licence', 'Maombi ya Leseni ya Kujifunzia Udereva', ['Leseni ya kujifunzia'], ['jurisdiction', 'full_name', 'date_of_birth', 'nida_number', 'tin_number', 'licence_category']],
                ['driving-licence-renewal', 'Kuhuisha Leseni ya Udereva', ['Kuhuisha leseni'], ['jurisdiction', 'full_name', 'date_of_birth', 'nida_number', 'tin_number', 'licence_category', 'existing_licence_number', 'upload_existing_licence']],
                ['driving-licence-replacement', 'Kubadilisha Leseni ya Udereva Iliyopotea au Kuharibika', ['Leseni iliyopotea / kuharibika'], ['jurisdiction', 'full_name', 'nida_number', 'tin_number', 'existing_licence_number', 'upload_existing_licence']],
                ['driving-licence-class-addition', 'Kuongeza Daraja la Leseni ya Udereva', ['Kuongeza daraja'], null],
            ]],
            'vehicle-related-applications' => ['request_type', [
                ['vehicle-related-applications', 'Kubadili Umiliki wa Usafiri', ['Uhamisho wa umiliki'], ['owner_type', 'owner_full_name', 'vehicle_registration_number', 'transfer_parties', 'seller_tin', 'buyer_tin', 'upload_vehicle_registration', 'upload_seller_identity', 'upload_buyer_identity', 'upload_sale_agreement', 'upload_lawyer_receipt']],
                ['vehicle-registration', 'Usajili wa Kwanza wa Gari', ['Usajili wa kwanza / gari kutoka nje'], null],
                ['vehicle-information-correction', 'Marekebisho ya Taarifa za Gari', ['Kubadilisha taarifa'], ['jurisdiction', 'owner_type', 'owner_full_name', 'nida_number', 'vehicle_registration_number', 'chassis_number', 'engine_number', 'vehicle_make_model', 'upload_proof_of_ownership', 'upload_supporting_docs']],
                ['vehicle-document-replacement', 'Nyaraka za Gari Zilizopotea', ['Nyaraka zilizopotea'], ['jurisdiction', 'owner_type', 'owner_full_name', 'nida_number', 'vehicle_registration_number', 'upload_proof_of_ownership']],
            ]],
            'madai-eardhi' => ['claim_type', [
                ['madai-eardhi', 'Madai ya Kiwanja — eArdhi', ['Madai ya kiwanja'], null],
                ['marekebisho-majina-eardhi', 'Marekebisho ya Majina — eArdhi', ['Marekebisho ya majina'], ['upload_identity', 'upload_deed_poll', 'upload_birth_certificate', 'upload_other_documents']],
            ]],
            'business-name-search-reservation' => ['request_type', [
                ['business-name-search-reservation', 'Uhakiki wa Jina — BRELA', ['Uhakiki wa awali'], null],
                ['business-name-reservation', 'Msaada wa Kuhifadhi Jina — BRELA', ['Hifadhi rasmi kama inapatikana kwa ombi hili'], null],
            ]],
            'loss-report' => ['request_type', [
                ['loss-report', 'Maombi ya Ripoti ya Upotevu', ['Ripoti mpya'], null],
                ['loss-report-follow-up', 'Kufuatilia au Kupakua Ripoti ya Upotevu', ['Kufuatilia / kupakua ripoti iliyopo'], ['full_name', 'reporter_identity_type', 'reporter_identity_number', 'official_reference']],
            ]],
            'residence-immigration-application' => ['request_type', [
                ['residence-immigration-application', 'Maombi ya Kibali Kipya cha Ukaazi', ['Ombi la kwanza'], null],
                ['residence-permit-renewal', 'Kuhuisha Kibali cha Ukaazi', ['Kuhuisha'], null],
                ['residence-permit-change', 'Kubadilisha Mwajiri au Kundi la Kibali cha Ukaazi', ['Kubadilisha mwajiri / kundi'], null],
            ]],
            'leseni-ya-biashara' => ['request_type', [
                ['leseni-ya-biashara', 'Maombi ya Leseni Mpya ya Biashara', ['Leseni mpya'], null],
                ['business-licence-renewal', 'Kuhuisha Leseni ya Biashara', ['Kuhuisha leseni'], null],
            ]],
            'school-application-assistance' => ['request_type', [
                ['school-application-assistance', 'Msaada wa Maombi ya Kujiunga na Shule', ['Kujiunga'], null],
                ['school-transfer-assistance', 'Msaada wa Maombi ya Kuhama Shule', ['Kuhama'], null],
                ['school-selection-follow-up', 'Kufuatilia Uchaguzi wa Shule', ['Kufuatilia uchaguzi'], null],
            ]],
            'madai-ya-nssf' => ['benefit_type', [
                ['madai-ya-nssf', 'NSSF — Fao la Ukosefu wa Ajira', ['Ukosefu wa ajira'], null],
                ['nssf-old-age-benefit', 'NSSF — Fao la Uzeeni', ['Uzeeni'], ['full_name', 'date_of_birth', 'nida_number', 'nssf_member_number', 'employer_name', 'bank_details', 'upload_certificate_of_service', 'upload_nssf_card', 'upload_nida_copy', 'upload_bank_statement', 'registered_email', 'registered_phone']],
                ['nssf-survivors-benefit', 'NSSF — Fao la Wategemezi wa Marehemu', ['Urithi / kifo'], ['nssf_member_number', 'employer_name', 'upload_nssf_card']],
                ['nssf-maternity-benefit', 'NSSF — Fao la Uzazi', ['Uzazi'], ['full_name', 'date_of_birth', 'nida_number', 'nssf_member_number', 'bank_details', 'upload_nssf_card', 'upload_nida_copy', 'registered_email', 'registered_phone']],
                ['nssf-invalidity-benefit', 'NSSF — Fao la Ulemavu', ['Ulemavu'], ['full_name', 'date_of_birth', 'nida_number', 'nssf_member_number', 'employer_name', 'bank_details', 'upload_nssf_card', 'upload_nida_copy', 'registered_email', 'registered_phone']],
            ]],
        ];
    }

    public static function apply(array $profiles): array
    {
        foreach (self::groups() as $origin => [$selector, $variants]) {
            $source = $profiles[$origin];
            foreach ($variants as [$slug, $name, $choices, $keys]) {
                $profile = $source;
                $profile['name'] = $name;
                $profile['origin_slug'] = $origin;
                $profile['description'] = 'Msaada wa '.mb_strtolower($name).'. Angalia taarifa na viambatisho vya huduma hii hapa chini. Tuma ulivyonavyo; timu yetu itakuelekeza kuhusu vinavyobaki.';
                $profile['fields'] = [];
                foreach ($source['fields'] as $field) {
                    $key = $field['field_key'];
                    if ($key === $selector && count($choices) === 1) {
                        continue;
                    }
                    if ($keys !== null && ! in_array($key, $keys, true) && $key !== $selector) {
                        continue;
                    }
                    if ($key === $selector) {
                        $field['options'] = $choices;
                        $field['label'] = match ($origin) {
                            'birth-certificate-application-rita', 'death-certificate-application-rita' => 'Muda wa usajili',
                            default => 'Sababu ya kubadilisha',
                        };
                    }
                    $conditions = $field['applies_when'] ?? [];
                    if (isset($conditions[$selector])) {
                        $matching = array_values(array_intersect($choices, $conditions[$selector]));
                        if (! $matching) {
                            continue;
                        }
                        if (count($choices) === 1 || $matching === $choices) {
                            unset($conditions[$selector]);
                        } else {
                            $conditions[$selector] = $matching;
                        }
                        $field['applies_when'] = $conditions;
                    }
                    $profile['fields'][] = $field;
                }
                // A land claim can begin with whichever ownership evidence exists;
                // staff still need to establish the customer's request.
                if ($slug === 'madai-eardhi' && $profile['fields']) {
                    $profile['fields'][0]['review_required'] = true;
                }
                if ($slug === 'loss-report-follow-up') {
                    foreach ($profile['fields'] as &$field) {
                        if ($field['field_key'] === 'official_reference') $field['review_required'] = true;
                    }
                    unset($field);
                }
                $profiles[$slug] = $profile;
            }
        }

        return $profiles;
    }

    public static function slugs(): array
    {
        $slugs = [];
        foreach (self::groups() as [$selector, $variants]) {
            foreach ($variants as $variant) $slugs[] = $variant[0];
        }

        return $slugs;
    }
}
