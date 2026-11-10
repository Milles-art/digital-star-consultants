@extends('layouts.digitalstar')

@php
    $d = __('site.services.detail');
    $category = $service->category;
    $parentCategory = $category?->parent;
    $fileFields = $service->fields->filter(fn ($field) => $field->field_type === 'file');
    $detailFields = $service->fields->filter(fn ($field) => ! in_array($field->field_type, ['file', 'hidden']));
    $price = $service->is_free ? $d['stat_fee_value'] : $service->formatted_price;
    $requirementRules = $service->metadata['government_requirements']['fields'] ?? [];
    $preparationDocuments = \App\Support\ServicePreparationChecklist::documents($service);
@endphp

@section('title', $service->name . ' | Digital Star Consultants')
@section('description', \Illuminate\Support\Str::limit(strip_tags($service->description ?: __('site.services.detail.meta_desc', ['name' => $service->name])), 155))

@php
    $wizardSteps = [
        ['title' => $d['step1_title'], 'sub' => $d['step1_sub']],
        ['title' => $d['step2_title'], 'sub' => $d['step2_sub']],
        ['title' => $d['step3_title'], 'sub' => $d['step3_sub']],
        ['title' => $d['step4_title'], 'sub' => $d['step4_sub']],
    ];
    $highlights = [
        ['icon' => 'compass', 'title' => $d['hl_guided_title'], 'copy' => $d['hl_guided_copy']],
        ['icon' => 'file-check', 'title' => $d['hl_docs_title'], 'copy' => $d['hl_docs_copy']],
        ['icon' => 'radar', 'title' => $d['hl_track_title'], 'copy' => $d['hl_track_copy']],
    ];
    // Localised strings for application-form.js (read via window.DS_FORM_I18N).
    $wizardI18n = [
        'full_name' => $d['full_name'],
        'phone' => $d['phone'],
        'email' => $d['email'],
        'preferred_date' => $d['preferred_date'],
        'notes' => __('site.services.notes'),
        'field' => __('site.services.fields'),
        'yes' => $d['yes'],
        'no' => $d['no'],
        'additional_info' => $d['review_service'],
        'no_additional_fields' => $d['step2_empty'],
        'document' => $d['review_files'],
        'no_file_selected' => $d['file_hint'],
        'documents' => $d['review_files'],
        'no_documents_required' => $d['step3_empty'],
        'selected_prefix' => $d['selected_prefix'],
        'submitting' => $d['submitting'],
        'error' => $d['submit_error'],
    ];
@endphp

@section('hero-crumbs')
    <a href="{{ route('home') }}">{{ __('site.nav.home') }}</a>
    <i data-lucide="chevron-right"></i>
    <a href="{{ route('public.services.index') }}">{{ __('site.nav.services') }}</a>
    @if ($parentCategory)
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('public.services.index', ['category' => $parentCategory->slug]) }}">{{ $parentCategory->name }}</a>
    @endif
    @if ($category)
        <i data-lucide="chevron-right"></i>
        <a href="{{ route('public.services.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>
    @endif
@endsection

@section('hero-actions')
    <a href="#application" class="ds-btn ds-btn-mint">{{ $d['start_app'] }} <i data-lucide="arrow-down"></i></a>
    <a href="{{ route('public.services.index', $category ? ['category' => $category->slug] : []) }}" class="ds-btn ds-btn-ghost-light">{{ $d['back_services'] }}</a>
@endsection

@section('hero-aside')
    <div class="ds-hero-card">
        <div class="ds-hero-card-chip" aria-hidden="true"></div>
        <span class="ds-hero-card-label">{{ $d['aside_ready'] }}</span>
        <h3>{{ $d['aside_title'] }}</h3>
        <small>{{ $d['aside_tracked'] }}</small>
        <p>{{ $d['aside_copy'] }}</p>
        <a href="#application" class="ds-btn ds-btn-white">{{ $d['start_app'] }} <i data-lucide="arrow-down"></i></a>
    </div>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => ($category?->name ?? $d['hero_service']) . ' · ' . $d['hero_available'],
        'title' => e($service->name),
        'lead' => e($service->description ?: $d['hero_lead_fallback']),
        'points' => [$price, $service->duration, $d['point_guided']],
    ])

    @include('partials.ds-stats', [
        'compact' => true,
        'stats' => [
            ['value' => $service->is_free ? $d['stat_fee_value'] : $service->formatted_price, 'label' => $d['stat_fee'], 'icon' => 'wallet'],
            ['value' => $service->duration, 'label' => $d['stat_time'], 'icon' => 'clock'],
            ['value' => $d['stat_guided'], 'label' => $d['stat_guided_label'], 'icon' => 'badge-check'],
            ['value' => __('site.services.detail.stat_fields', ['count' => $detailFields->count() + $fileFields->count()]), 'label' => $d['stat_fields_label'], 'icon' => 'clipboard-list'],
        ],
    ])

    {{-- OVERVIEW --}}
    <section class="ds-section" id="overview">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => $d['overview_kicker'],
                'title' => $d['overview_title'],
                'copy' => ($service->description ? $service->description . ' ' : '') . $d['overview_copy'],
            ])
            <div class="ds-grid-3-even">
                @foreach ($highlights as $item)
                    <article class="ds-service ds-reveal" style="--d:{{ $loop->index * 0.08 }}s">
                        <span class="ds-icon-box"><i data-lucide="{{ $item['icon'] }}"></i></span>
                        <h3>{{ $item['title'] }}</h3>
                        <p>{!! $item['copy'] !!}</p>
                    </article>
                @endforeach
            </div>

            <div class="ds-two" id="requirements" style="margin-top:20px">
                <div class="ds-panel ds-reveal">
                    <span class="ds-kicker">{{ $d['need_kicker'] }}</span>
                    <h3>{{ $d['need_title'] }}</h3>
                    <ul class="ds-list">
                        <li><i data-lucide="circle-check"></i><span>{!! $d['need_accurate'] !!}</span></li>

                        {{-- Exact documents required for this service --}}
                        @if ($preparationDocuments->isNotEmpty())
                            <li style="padding-left:8px;border-left:3px solid var(--ds-accent);margin-left:4px">
                                <span style="display:block;font-size:0.9rem;text-transform:uppercase;letter-spacing:.05em;color:var(--ds-accent);margin-bottom:8px">
                                    {{ __('requirements.documents') }}
                                </span>
                                <ul style="list-style:none;padding:0;margin:0">
                                    @foreach ($preparationDocuments as $document)
                                        <li style="margin-bottom:6px;display:flex;align-items:flex-start;gap:10px">
                                            <i data-lucide="file-text" style="width:14px;height:14px;flex-shrink:0;margin-top:2px;color:var(--ds-accent)"></i>
                                            <span>
                                                <strong>{{ $document['label'] }}</strong>
                                                @include('services.partials.requirement-context', ['conditions' => $document['conditions']])
                                                @if ($document['help'])
                                                    <small style="color:var(--ds-muted-fg);display:block;margin-top:2px">{{ $document['help'] }}</small>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                                <small style="display:block;margin-top:6px;color:var(--ds-muted-fg);font-size:0.85rem">{{ $d['accepted_formats'] }}</small>
                            </li>
                        @endif

                        {{-- Exact information required for this service --}}
                        @php $infoFields = $detailFields; @endphp
                        @if ($infoFields->isNotEmpty())
                            <li style="padding-left:8px;border-left:3px solid var(--ds-accent);margin-left:4px">
                                <span style="display:block;font-size:0.9rem;text-transform:uppercase;letter-spacing:.05em;color:var(--ds-accent);margin-bottom:8px">
                                    {{ __('requirements.information') }}
                                </span>
                                <ul style="list-style:none;padding:0;margin:0">
                                    @foreach ($infoFields as $field)
                                        <li style="margin-bottom:6px;display:flex;align-items:flex-start;gap:10px">
                                            <i data-lucide="type" style="width:14px;height:14px;flex-shrink:0;margin-top:2px;color:var(--ds-accent)"></i>
                                            <span>
                                                <strong>{{ $field->label }}</strong>
                                                @include('services.partials.requirement-context', ['conditions' => $requirementRules[$field->field_key]['applies_when'] ?? []])
                                                @if ($field->help_text)
                                                    <br><small style="color:var(--ds-muted-fg);display:block;margin-top:2px">{{ $field->help_text }}</small>
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif

                        <li><i data-lucide="circle-check"></i><span>{{ __('requirements.guidance') }}</span></li>
                    </ul>
                    @if (!empty($service->metadata['government_requirements']['sources']))
                        <h4>{{ __('requirements.authority_sources') }}</h4>
                        <ul class="ds-list">
                            @foreach ($service->metadata['government_requirements']['sources'] as $source)
                                <li><a href="{{ $source }}" target="_blank" rel="noopener noreferrer">{{ parse_url($source, PHP_URL_HOST) }}</a></li>
                            @endforeach
                        </ul>
                        <p>{{ __('requirements.source_note') }}</p>
                    @endif
                </div>
                <div class="ds-panel ds-panel-muted ds-reveal" style="--d:.1s">
                    <span class="ds-kicker">{{ $d['process_kicker'] }}</span>
                    <h3>{{ $d['process_title'] }}</h3>
                    <ol class="ds-numbered">
                        <li>{{ $d['process_1'] }}</li>
                        <li>{{ $d['process_2'] }}</li>
                        <li>{{ $d['process_3'] }}</li>
                        <li>{{ $d['process_4'] }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- APPLICATION WIZARD --}}
    <section class="ds-section ds-section-tint" id="application" style="scroll-margin-top:72px">
        <div class="ds-container ds-wizard">
            <aside class="ds-wizard-side">
                <span class="ds-kicker">{{ $d['wizard_kicker'] }}</span>
                <h2 style="padding-top:12px;font-size:32px;font-weight:800;letter-spacing:-.02em;line-height:1.15">{{ $d['wizard_title'] }}</h2>
                <p style="padding-top:12px;color:var(--ds-muted-fg)">{{ $d['wizard_lead'] }}</p>

                <div class="ds-panel" style="margin-top:24px;padding:20px">
                    <span class="ds-kicker">{{ $d['progress_kicker'] }}</span>
                    <div class="ds-progress-head">
                        <strong data-progress-count>1 / 4</strong>
                        <span>{!! $d['progress_step'] !!}</span>
                    </div>
                    <div class="ds-progress"><span data-progress-bar style="width:25%"></span></div>
                    <ol class="ds-wsteps">
                        @foreach ($wizardSteps as $step)
                            <li data-step-item="{{ $loop->iteration }}" class="{{ $loop->first ? 'is-active' : '' }}">
                                <b>{{ $loop->iteration }}</b>
                                <span><strong>{{ $step['title'] }}</strong><small>{{ $step['sub'] }}</small></span>
                            </li>
                        @endforeach
                    </ol>
                    <p class="ds-help">{{ $d['help'] }} <a href="{{ route('public.contact.show') }}">{{ $d['help_link'] }}</a></p>
                </div>
            </aside>

            <div class="ds-form-card">
                <form id="service-form" class="ds-form" enctype="multipart/form-data" novalidate
                      data-submit-url="{{ route('public.submissions.store') }}">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">

                    {{-- Step 1 --}}
                    <section class="ds-step-panel" data-step="1">
                        <span class="ds-kicker">{{ __('site.services.detail.step_of', ['no' => '01']) }}</span>
                        <h3>{{ $d['step1_title'] }}</h3>
                        <p>{{ $d['step1_copy'] }}</p>
                        <div class="ds-form-row">
                            <label class="ds-field">{{ $d['full_name'] }} *
                                <input id="customer_name" name="customer_name" required autocomplete="name" placeholder="{{ $d['name_ph'] }}">
                            </label>
                            <label class="ds-field">{{ $d['phone'] }} *
                                <input id="customer_phone" name="customer_phone" type="tel" required autocomplete="tel" placeholder="07XX XXX XXX">
                            </label>
                            <label class="ds-field">{!! __('site.services.detail.email_label', ['optional' => __('site.services.optional')]) !!}
                                <input id="customer_email" type="email" name="customer_email" autocomplete="email" placeholder="example@gmail.com">
                            </label>
                            <label class="ds-field">{!! __('site.services.detail.preferred_date_label', ['optional' => __('site.services.optional')]) !!}
                                <input id="preferred_date" type="date" name="preferred_date">
                            </label>
                        </div>
                        <div class="ds-secure"><i data-lucide="lock"></i><span>{!! $d['secure'] !!}</span></div>
                    </section>

                    {{-- Step 2 --}}
                    <section class="ds-step-panel" data-step="2" hidden>
                        <span class="ds-kicker">{{ __('site.services.detail.step_of', ['no' => '02']) }}</span>
                        <h3>{{ $d['step2_title'] }}</h3>
                        <p>{{ __('requirements.guidance') }}</p>
                        <p>{{ __('requirements.case_note') }}</p>
                        @if ($detailFields->isNotEmpty())
                            <div class="ds-form-row">
                                @foreach ($detailFields as $field)
                                    @include('services.partials.field', ['field' => $field])
                                @endforeach
                            </div>
                        @else
                            <div class="ds-empty">
                                <span class="ds-icon-box"><i data-lucide="check"></i></span>
                                <h3>{{ $d['step2_empty'] }}</h3>
                                <p>{{ $d['step2_empty_copy'] }}</p>
                            </div>
                        @endif
                        <label class="ds-field" style="margin-top:20px">{!! __('site.services.detail.notes_label', ['optional' => __('site.services.optional')]) !!}
                            <textarea id="notes" name="customer_notes" rows="4" placeholder="{{ $d['notes_ph'] }}"></textarea>
                        </label>
                    </section>

                    {{-- Step 3 --}}
                    <section class="ds-step-panel" data-step="3" hidden>
                        <span class="ds-kicker">{{ __('site.services.detail.step_of', ['no' => '03']) }}</span>
                        <h3>{{ $d['step3_title'] }}</h3>
                        <p>{{ __('requirements.guidance') }}</p>

                        @if ($fileFields->isNotEmpty())
                            {{-- Documents required checklist --}}
                            <div class="ds-panel" style="margin-bottom:20px">
                                <span class="ds-kicker">{{ __('requirements.documents') }}</span>
                                <ul class="ds-list">
                                    @foreach ($fileFields as $field)
                                        <li data-service-condition="{{ json_encode($requirementRules[$field->field_key]['applies_when'] ?? new \stdClass()) }}">
                                            <strong>{{ $field->label }}</strong>
                                            @if ($field->help_text)
                                                <br><small style="color:var(--ds-muted-fg)">{{ $field->help_text }}</small>
                                            @endif
                                        </li>
                                    @endforeach
                                    <li style="margin-top:8px;padding-top:8px;border-top:1px solid var(--ds-border);color:var(--ds-muted-fg);font-size:0.9rem">
                                        <i data-lucide="file-text" style="width:14px;height:14px;margin-right:6px"></i>
                                        {{ $d['accepted_formats'] }}
                                    </li>
                                </ul>
                            </div>

                            {{-- File upload boxes --}}
                            <div class="ds-files">
                                @foreach ($fileFields as $field)
                                    <label class="ds-file" data-service-condition="{{ json_encode($requirementRules[$field->field_key]['applies_when'] ?? new \stdClass()) }}">
                                        <span class="ds-icon-box"><i data-lucide="cloud-upload"></i></span>
                                        <span style="min-width:0">
                                            <strong>{{ $field->label }}</strong>
                                            <small data-file-name-for="field-{{ $field->field_key }}">{{ $d['file_hint'] }}</small>
                                        </span>
                                        <input id="field-{{ $field->field_key }}" type="file" name="fields[{{ $field->field_key }}]"
                                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" data-label="{{ $field->label }}">
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="ds-empty">
                                <span class="ds-icon-box"><i data-lucide="file-check"></i></span>
                                <h3>{{ $d['step3_empty'] }}</h3>
                                <p>{{ $d['step3_empty_copy'] }}</p>
                            </div>
                        @endif
                    </section>

                    {{-- Step 4 --}}
                    <section class="ds-step-panel" data-step="4" hidden>
                        <span class="ds-kicker">{{ __('site.services.detail.step_of', ['no' => '04']) }}</span>
                        <h3>{{ $d['step4_title'] }}</h3>
                        <p>{{ $d['step4_copy'] }}</p>
                        @foreach (['contact' => $d['review_contact'], 'service' => $d['review_service'], 'files' => $d['review_files']] as $key => $label)
                            <div class="ds-review">
                                <div class="ds-review-head">
                                    <strong>{{ $label }}</strong>
                                    <button type="button" class="ds-link-btn" data-go-step="{{ $loop->iteration }}">{{ $d['edit'] }}</button>
                                </div>
                                <dl class="ds-meta" data-review="{{ $key }}"></dl>
                            </div>
                        @endforeach
                        <label class="ds-check" style="margin-top:20px">
                            <input type="checkbox" id="application-consent" required>
                            <span>{{ $d['consent'] }}</span>
                        </label>
                    </section>

                    {{-- Success (shown after submission) --}}
                    <section class="ds-success" data-success role="status" aria-live="polite" hidden>
                        <span class="ds-icon-box"><i data-lucide="check"></i></span>
                        <h3>{{ $d['success_title'] }}</h3>
                        <p data-success-message>{{ $d['success_copy'] }}</p>
                        <span class="ds-ref" data-reference></span>
                        <div class="ds-actions" style="justify-content:center">
                            <a class="ds-btn ds-btn-primary" data-track-link href="#">{{ $d['track'] }} <i data-lucide="arrow-right"></i></a>
                            <a class="ds-btn ds-btn-soft" href="{{ route('public.services.index') }}">{{ $d['browse_more'] }}</a>
                        </div>
                    </section>

                    <div class="ds-alert ds-alert-error" data-form-message role="alert" hidden></div>

                    <div class="ds-wizard-nav" data-wizard-nav>
                        <button type="button" class="ds-btn ds-btn-soft" data-back hidden><i data-lucide="arrow-left"></i> {{ $d['back'] }}</button>
                        <span class="ds-spacer"></span>
                        <button type="button" class="ds-btn ds-btn-primary" data-next>{{ $d['next'] }} <i data-lucide="arrow-right"></i></button>
                        <button type="submit" class="ds-btn ds-btn-primary" data-submit hidden>{{ $d['submit'] }} <i data-lucide="send"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @include('partials.ds-cta', [
        'kicker' => $d['cta_kicker'],
        'title' => $d['cta_title'],
        'copy' => $d['cta_copy'],
        'buttonLabel' => $d['cta_btn'],
        'url' => route('public.contact.show'),
        'checklist' => $d['cta_checklist'],
    ])
@endsection

@push('scripts')
    <script>
        // Localised strings for the 4-step wizard (application-form.js).
        window.DS_FORM_I18N = {!! json_encode($wizardI18n, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!};
    </script>
    <script src="{{ asset('js/application-form.js') }}?v={{ @filemtime(public_path('js/application-form.js')) }}"></script>
@endpush
