<?php

use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\ServiceField;
use App\Models\Submission;
use App\Models\SubmissionFieldValue;
use App\Models\User;
use App\Support\GovernmentServiceCatalogue;
use App\Support\ServiceRequirementReview;
use Database\Seeders\GovernmentRequirementsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

function governmentTestService(string $slug): Service
{
    $service = Service::factory()->create(['slug' => $slug, 'price' => 12345, 'is_active' => true]);
    (new GovernmentRequirementsSeeder)->run();

    return $service->fresh('fields');
}

test('every reviewed service accepts a contact-only request and leaves follow-up items for staff', function () {
    Queue::fake();
    $this->withoutMiddleware(ThrottleRequests::class);
    $category = ServiceCategory::factory()->create();
    foreach (array_keys(GovernmentServiceCatalogue::definitions()) as $slug) {
        Service::factory()->create(['service_category_id' => $category->id, 'slug' => $slug, 'is_active' => true]);
    }
    (new GovernmentRequirementsSeeder)->run();
    foreach (Service::with('fields')->get() as $service) {
        expect($service->fields->where('is_required', true))->toHaveCount(0);
        $response = $this->postJson('/submit', [
            'service_id' => $service->id, 'customer_name' => 'Customer needing help', 'customer_phone' => '0712345678',
        ]);
        $response->assertCreated()->assertJsonPath('data.status', Submission::STATUS_AWAITING_CUSTOMER);
    }
    expect(Submission::count())->toBe(count(GovernmentServiceCatalogue::definitions()));
});

test('refresh preserves prices identities inactive services and old submission answers', function () {
    Storage::fake('private');
    $service = Service::factory()->create(['slug' => 'birth-certificate-application-rita', 'price' => 12345]);
    $field = ServiceField::create(['service_id' => $service->id, 'field_key' => 'parents_birth_date', 'label' => 'Old combined parent date', 'field_type' => 'date']);
    $submission = Submission::factory()->create(['service_id' => $service->id]);
    $answer = SubmissionFieldValue::create(['submission_id' => $submission->id, 'service_field_id' => $field->id, 'value' => '1970-01-01', 'field_label_snapshot' => 'Original label']);
    $inactive = Service::factory()->create(['slug' => 'passport-renewal', 'is_active' => false]);
    $this->artisan('catalogue:refresh-government')->assertSuccessful();
    $this->artisan('catalogue:refresh-government')->assertSuccessful();
    expect($service->fresh()->slug)->toBe('birth-certificate-application-rita')
        ->and($service->fresh()->price)->toBe('12345.00')
        ->and($field->fresh()->is_active)->toBeFalse()
        ->and($answer->fresh()->value)->toBe('1970-01-01')
        ->and($answer->fresh()->field_label_snapshot)->toBe('Original label')
        ->and($inactive->fresh()->is_active)->toBeFalse()
        ->and(User::count())->toBe(0);
    expect(Storage::disk('private')->files('catalogue-backups'))->toHaveCount(2);
});

test('home birth and company document drafting do not demand irrelevant uploads for review', function () {
    $birth = governmentTestService('birth-certificate-application-rita');
    $missing = ServiceRequirementReview::missing($birth, ['jurisdiction' => 'Tanzania Bara', 'request_type' => 'Usajili baada ya siku 90', 'birth_place_type' => 'Nyumbani'])->pluck('field_key');
    expect($missing)->not->toContain('birth_hospital', 'upload_birth_notification');
    expect($missing)->toContain('upload_birth_supporting_docs');
    $company = governmentTestService('company-registration-brela');
    $missing = ServiceRequirementReview::missing($company, ['needs_draft_documents' => 'Naomba mnisaidie kuandaa'])->pluck('field_key');
    expect($missing)->not->toContain('upload_memorandum', 'upload_articles');
    $missing = ServiceRequirementReview::missing($company, ['needs_draft_documents' => 'Tayari nina nyaraka'])->pluck('field_key');
    expect($missing)->toContain('upload_memorandum', 'upload_articles');
});

test('minor passport consent is followed up without blocking an incomplete request or losing uploaded files', function () {
    Queue::fake();
    Storage::fake('private');
    $service = governmentTestService('new-passport-application');
    $missing = ServiceRequirementReview::missing($service, ['age_group' => 'Chini ya miaka 16'])->pluck('field_key');
    expect($missing)->toContain('upload_guardian_consent', 'upload_parent_birth_certificate');
    $response = $this->post('/submit', [
        'service_id' => $service->id, 'customer_name' => 'Parent', 'customer_phone' => '0712345678',
        'fields' => ['age_group' => 'Chini ya miaka 16', 'upload_birth_certificate' => UploadedFile::fake()->create('birth.pdf', 50, 'application/pdf')],
    ], ['Accept' => 'application/json']);
    $response->assertCreated()->assertJsonPath('data.status', Submission::STATUS_AWAITING_CUSTOMER);
    $submission = Submission::latest('id')->firstOrFail();
    $value = $submission->values()->whereHas('field', fn ($q) => $q->where('field_key', 'upload_birth_certificate'))->firstOrFail();
    Storage::disk('private')->assertExists($value->file_path);
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'is_active' => true]);
    $this->actingAs($admin)->get(route('admin.submissions.show', $submission))->assertOk()->assertSee('Ridhaa ya mzazi');
});

test('loss report preparation has no circular prerequisite and public guidance is optional in both locales', function () {
    $service = governmentTestService('loss-report');
    expect($service->fields->pluck('field_key'))->not->toContain('upload_police_report');
    foreach (['en', 'sw'] as $locale) {
        $this->withSession(['locale' => $locale])->get(route('public.services.show', $service->slug))
            ->assertOk()->assertSee(__('requirements.guidance', [], $locale));
    }
    $this->get(route('public.forms.show', 'pasipoti'))->assertOk()->assertSee('SEHEMU E');
});

test('a complete selected-case preparation can be queued normally while an unsure selection needs follow-up', function () {
    Queue::fake();
    $service = governmentTestService('general-form-assistance');
    $this->postJson('/submit', [
        'service_id' => $service->id, 'customer_name' => 'Customer', 'customer_phone' => '0712345678',
        'fields' => ['form_name' => 'Specific institution form', 'form_purpose' => 'Help completing the form'],
    ])->assertCreated()->assertJsonPath('data.status', Submission::STATUS_PENDING);
    $service = governmentTestService('nida-registration-correction');
    expect(ServiceRequirementReview::missing($service, [])->pluck('field_key'))->toContain('full_name', 'upload_birth_certificate');
    expect($service->fields->pluck('field_key'))->not->toContain('request_type');
});

test('PDF alignment retains the transfer checklist and separates land claims from name corrections', function () {
    $vehicle = governmentTestService('vehicle-related-applications');
    $transfer = ServiceRequirementReview::missing($vehicle, ['request_type' => 'Uhamisho wa umiliki'])->pluck('field_key');
    expect($transfer)->toContain('seller_tin', 'buyer_tin', 'upload_vehicle_registration', 'upload_seller_identity', 'upload_buyer_identity', 'upload_sale_agreement', 'upload_lawyer_receipt');
    $replacement = governmentTestService('vehicle-document-replacement');
    $other = ServiceRequirementReview::missing($replacement, [])->pluck('field_key');
    expect($other)->not->toContain('seller_tin', 'buyer_tin', 'upload_sale_agreement');
    $land = governmentTestService('madai-eardhi');
    expect(ServiceRequirementReview::missing($land, ['claim_type' => 'Madai ya kiwanja'])->pluck('field_key'))->not->toContain('upload_deed_poll', 'upload_birth_certificate');
    $correction = governmentTestService('marekebisho-majina-eardhi');
    expect(ServiceRequirementReview::missing($correction, [])->pluck('field_key'))->toContain('existing_name', 'correct_name', 'upload_land_title')
        ->not->toContain('upload_deed_poll', 'upload_birth_certificate');
    expect($land->fields->pluck('field_key'))->not->toContain('claim_type', 'upload_deed_poll');
    expect($correction->fields->pluck('field_key'))->not->toContain('claim_type')
        ->toContain('upload_land_title');
});

test('PDF refresh updates only covered services and records all thirteen guides', function () {
    Storage::fake('private');
    $category = ServiceCategory::factory()->create(['slug' => 'other-online-forms']);
    $nssf = Service::factory()->create(['slug' => 'madai-ya-nssf', 'price' => 20000]);
    $untouched = Service::factory()->create(['slug' => 'police-clearance-good-conduct', 'description' => 'Keep this content']);
    $untouchedField = ServiceField::create(['service_id' => $untouched->id, 'field_key' => 'custom_field', 'label' => 'Keep this field']);
    $this->artisan('catalogue:refresh-government', ['--documents' => true])->assertSuccessful();
    expect($untouched->fresh()->description)->toBe('Keep this content')
        ->and($untouchedField->fresh()->is_active)->toBeTrue()
        ->and($nssf->fresh()->price)->toBe('20000.00')
        ->and($nssf->fresh('fields')->fields->pluck('field_key'))->toContain('registered_email', 'registered_phone', 'upload_bank_statement', 'upload_nida_copy');
    expect(Service::where('slug', 'madai-eardhi')->exists())->toBeTrue();
    expect(Service::where('slug', 'latra-registration')->exists())->toBeTrue();
    $names = collect(GovernmentServiceCatalogue::definitions())->pluck('document_sources')->filter()->flatten()->unique();
    expect($names)->toHaveCount(13);
    foreach (['uchapishaji-wa-nyuzi', 'vifungashio-vya-kisasa'] as $slug) {
        expect(collect(GovernmentServiceCatalogue::definition($slug)['fields'])->where('is_required', true))->toHaveCount(0);
    }
});

test('preparation lists show named documents and case notes before the form without optional badges', function () {
    $service = governmentTestService('nida-registration-correction');
    $response = $this->withSession(['locale' => 'sw'])->get(route('public.services.show', $service->slug));
    $response->assertOk()->assertDontSee('Si lazima sasa')
        ->assertSee('Aina za faili: PDF, JPG, PNG, DOC, DOCX');
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
    $xpath = new DOMXPath($dom);
    $overview = $xpath->query('//*[@id="requirements"]')->item(0);
    expect($overview)->not->toBeNull();
    expect($overview->textContent)->toContain('Cheti cha kuzaliwa cha mwombaji', 'mzazi mmoja (baba au mama)', 'kitambulisho cha mpiga kura')
        ->not->toContain('Nyaraka zinazosaidia ombi lako', 'kuunganisha nyaraka', 'kiapo cha baba, kama unacho');
    expect($xpath->query('//*[@id="requirements"]//*[@data-service-condition]')->length)->toBe(0);
});

test('the preparation overview does not wait for a minor passport selection to explain guardian consent', function () {
    $service = governmentTestService('new-passport-application');
    $response = $this->withSession(['locale' => 'en'])->get(route('public.services.show', $service->slug));
    $response->assertOk()->assertDontSee('Optional now');
    $dom = new DOMDocument;
    @$dom->loadHTML('<?xml encoding="UTF-8">'.$response->getContent());
    $xpath = new DOMXPath($dom);
    $overview = $xpath->query('//*[@id="requirements"]')->item(0);
    expect($overview->textContent)->toContain('Ridhaa ya mzazi', 'Chini ya miaka 16');
    // Only the actual upload remains conditional; there is no required attribute.
    $input = $xpath->query('//*[@id="field-upload_guardian_consent"]')->item(0);
    expect($input->hasAttribute('required'))->toBeFalse();
});

test('separating services preserves existing identities prices history and inactive choices', function () {
    Storage::fake('private');
    $service = Service::factory()->create(['slug' => 'nida-registration-correction', 'price' => 12345]);
    $field = ServiceField::create(['service_id' => $service->id, 'field_key' => 'correction_details', 'label' => 'Old correction answer']);
    $submission = Submission::factory()->create(['service_id' => $service->id]);
    $answer = SubmissionFieldValue::create(['submission_id' => $submission->id, 'service_field_id' => $field->id, 'value' => 'Historical answer']);
    $inactive = Service::factory()->create(['slug' => 'nida-card-replacement', 'is_active' => false]);
    $other = Service::factory()->create(['slug' => 'unrelated-service', 'description' => 'Unchanged']);
    $this->artisan('catalogue:separate-services', ['--dry-run' => true])->assertSuccessful();
    expect(Service::count())->toBe(3);
    $this->artisan('catalogue:separate-services')->assertSuccessful();
    $this->artisan('catalogue:separate-services')->assertSuccessful();
    expect(Service::count())->toBe(5)
        ->and($service->fresh()->slug)->toBe('nida-registration-correction')
        ->and($service->fresh()->price)->toBe('12345.00')
        ->and($field->fresh()->is_active)->toBeFalse()
        ->and($answer->fresh()->value)->toBe('Historical answer')
        ->and($inactive->fresh()->is_active)->toBeFalse()
        ->and($other->fresh()->description)->toBe('Unchanged');
    $correction = Service::where('slug', 'nida-correction')->firstOrFail();
    expect($correction->id)->not->toBe($service->id);
    expect($correction->price)->toBeNull();
    expect($correction->fields->pluck('field_key'))->toContain('correction_details', 'upload_correction_evidence')
        ->not->toContain('request_type', 'upload_birth_certificate', 'father_full_name');
    expect($service->fresh('fields')->fields->pluck('field_key'))->not->toContain('request_type', 'correction_details');
    $this->get(route('public.services.show', $correction->slug))->assertOk()->assertSee('Marekebisho ya Taarifa za NIDA')->assertDontSee('Si lazima sasa');
    expect(Storage::disk('private')->files('catalogue-backups'))->toHaveCount(2);
});

test('official NIN lookup uses mother names and lost NIDA preparation is specific to the cause', function () {
    $lookup = governmentTestService('nida-number-lookup');
    expect($lookup->fields->pluck('field_key'))->toContain('mother_first_name', 'mother_last_name')
        ->not->toContain('primary_school', 'primary_school_year', 'residential_address');
    expect($lookup->metadata['government_requirements']['official_verification']['supported_field_keys'])->toContain('mother_first_name');
    $replacement = governmentTestService('nida-card-replacement');
    $missing = ServiceRequirementReview::missing($replacement, ['replacement_reason' => 'Kimepotea'])->pluck('field_key');
    expect($missing)->toContain('upload_police_loss_report')->not->toContain('upload_damaged_card');
});

test('RITA old to new has additional evidence and mainland rules do not become Zanzibar prerequisites', function () {
    $oldToNew = governmentTestService('birth-certificate-old-to-new');
    $verification = governmentTestService('birth-certificate-verification');
    expect($oldToNew->fields->pluck('field_key'))->toContain('upload_birth_supporting_docs', 'upload_parent_guardian_identity');
    expect($verification->fields->pluck('field_key'))->not->toContain('upload_birth_supporting_docs');
    $birth = governmentTestService('birth-certificate-application-rita');
    $missing = ServiceRequirementReview::missing($birth, ['jurisdiction' => 'Zanzibar', 'request_type' => 'Usajili baada ya siku 90'])->pluck('field_key');
    expect($missing)->not->toContain('upload_birth_supporting_docs');
    $foreignDeath = governmentTestService('foreign-death-registration');
    $missing = ServiceRequirementReview::missing($foreignDeath, [])->pluck('field_key');
    expect($missing)->not->toContain('upload_medical_certificate');
});

test('office guide extras remain optional intake without becoming universal preparation documents', function () {
    $passport = governmentTestService('new-passport-application');
    $documents = App\Support\ServicePreparationChecklist::documents($passport)->pluck('key');
    expect($documents)->toContain('upload_national_identity', 'upload_travel_evidence')
        ->not->toContain('upload_education_certificates', 'upload_residence_letter', 'upload_passport_request_letter');
    expect($passport->fields->firstWhere('field_key', 'upload_education_certificates')->is_required)->toBeFalse();
    $death = governmentTestService('death-certificate-application-rita');
    expect(App\Support\ServicePreparationChecklist::documents($death)->pluck('key'))->not->toContain('upload_parent_birth_certificate');
});

test('each NSSF benefit has its own service without unrelated unemployment fields', function () {
    $category = ServiceCategory::factory()->create();
    Service::factory()->create(['service_category_id' => $category->id, 'slug' => 'madai-ya-nssf']);
    $this->artisan('catalogue:separate-services')->assertSuccessful();
    foreach (['nssf-old-age-benefit', 'nssf-survivors-benefit', 'nssf-maternity-benefit', 'nssf-invalidity-benefit'] as $slug) {
        $service = Service::where('slug', $slug)->firstOrFail();
        expect($service->fields->pluck('field_key'))->not->toContain('benefit_type', 'cessation_reason', 'reemployment', 'termination_date');
    }
    $maternity = Service::where('slug', 'nssf-maternity-benefit')->firstOrFail();
    expect($maternity->fields->pluck('field_key'))->toContain('upload_maternity_medical_evidence');
});

test('all reviewed conditional inputs refer to a field and valid option on the same service', function () {
    foreach (GovernmentServiceCatalogue::definitions() as $slug => $profile) {
        $fields = array_column($profile['fields'], null, 'field_key');
        foreach ($fields as $field) {
            foreach ($field['applies_when'] ?? [] as $key => $values) {
                expect(isset($fields[$key]), $slug.'/'.$field['field_key'].' -> '.$key)->toBeTrue();
                if (isset($fields[$key]['options'])) expect(array_diff($values, $fields[$key]['options']))->toBeEmpty();
            }
        }
    }
});

test('the six legacy URLs receive distinct forms while preserving identities and prices', function () {
    $originals = [];
    foreach (App\Support\LegacyServiceForms::slugs() as $slug) {
        $originals[$slug] = Service::factory()->create(['slug' => $slug, 'price' => 24680]);
    }
    (new GovernmentRequirementsSeeder)->run();
    expect(Service::count())->toBe(6);
    foreach ($originals as $slug => $original) {
        $service = $original->fresh('fields');
        expect($service->slug)->toBe($slug)
            ->and($service->price)->toBe('24680.00')
            ->and($service->fields->count())->toBeGreaterThan(0)
            ->and($service->fields->where('is_required', true))->toHaveCount(0);
        $this->withSession(['locale' => 'sw'])->get(route('public.services.show', $slug))
            ->assertOk()->assertDontSee('Si lazima sasa');
    }
    expect($originals['brela-company-name-search']->fresh('fields')->fields->pluck('field_key'))->not->toContain('entity_type');
    $vat = $originals['vat-registration']->fresh('fields');
    expect($vat->fields->pluck('field_key'))->toContain('taxable_turnover', 'upload_intending_trade_evidence')
        ->not->toContain('passport_number', 'licence_authority');
});

test('TRA transfer preparation includes lawyer EFD and separates company and untraceable seller evidence', function () {
    $service = governmentTestService('vehicle-related-applications');
    expect(App\Support\ServicePreparationChecklist::documents($service)->pluck('key'))
        ->toContain('upload_lawyer_receipt', 'upload_application_letter', 'upload_sale_efd', 'upload_ownership_affidavit');
    $fields = $service->metadata['government_requirements']['fields'];
    expect($fields['upload_brela_confirmation']['applies_when'])->toBe([
        'owner_type' => ['Kampuni / taasisi'], 'transfer_case' => ['Muuzaji hapatikani'],
    ]);
    $this->withSession(['locale' => 'sw'])->get(route('public.services.show', $service->slug))
        ->assertOk()->assertSee('Risiti ya EFD ya mwanasheria')->assertDontSee('mwanasheria wa TLS');
});

test('follow-up distinguishes target-class competence and medical-board assessment from ordinary certificates', function () {
    $class = governmentTestService('driving-licence-class-addition');
    expect($class->fields->firstWhere('field_key', 'licence_category')->label)->toBe('Daraja unalotaka kuongeza');
    expect(App\Support\ServicePreparationChecklist::documents($class)->pluck('key'))
        ->toContain('upload_competence_certificate')->not->toContain('upload_medical_form');
    expect($class->metadata['government_requirements']['fields']['upload_competence_certificate']['applies_when'])
        ->toBe(['jurisdiction' => ['Tanzania Bara']]);
    $invalidity = governmentTestService('nssf-invalidity-benefit');
    expect($invalidity->fields->firstWhere('field_key', 'upload_medical_board_assessment')->is_required)->toBeFalse();
    $tin = governmentTestService('kubalishwa-jina-tin');
    expect($tin->fields->firstWhere('field_key', 'upload_change_notification')->is_required)->toBeFalse();
    expect($tin->metadata['government_requirements']['official_verification']['status'])->toBe('partial');
});

test('Google-discovered TRA forms keep the company amendment and mainland police report scoped to their cases', function () {
    $vehicle = governmentTestService('vehicle-document-replacement');
    expect(App\Support\ServicePreparationChecklist::documents($vehicle)->pluck('key'))
        ->toContain('upload_police_loss_report', 'upload_replacement_application')
        ->not->toContain('upload_proof_of_ownership');
    expect(ServiceRequirementReview::missing($vehicle, ['jurisdiction' => 'Zanzibar'])->pluck('field_key'))
        ->not->toContain('upload_police_loss_report', 'chassis_number');
    expect(ServiceRequirementReview::missing($vehicle, ['jurisdiction' => 'Tanzania Bara'])->pluck('field_key'))
        ->toContain('upload_police_loss_report', 'chassis_number');
    $tin = governmentTestService('kubalishwa-jina-tin');
    expect($tin->metadata['government_requirements']['fields']['upload_entity_amendment_form']['applies_when'])
        ->toBe(['taxpayer_type' => ['Kampuni / taasisi']]);
    expect($tin->fields->firstWhere('field_key', 'upload_entity_amendment_form')->is_required)->toBeFalse();
});
