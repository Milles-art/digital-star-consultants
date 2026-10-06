@extends('layouts.digitalstar')

@section('title', $service->name . ' | Digital Star Consultants')

@php
    $category = $service->category;
    $parentCategory = $category?->parent;
    $fileFields = $service->fields->filter(fn ($field) => $field->field_type === 'file');
    $detailFields = $service->fields->filter(fn ($field) => ! in_array($field->field_type, ['file', 'hidden']));
    $price = $service->is_free ? 'Quote on request' : $service->formatted_price;
    $wizardSteps = [
        ['title' => 'Contact information', 'sub' => 'Basic contact details'],
        ['title' => 'Service details', 'sub' => 'Information for this service'],
        ['title' => 'Documents', 'sub' => 'Upload required documents'],
        ['title' => 'Review & confirm', 'sub' => 'Check before sending'],
    ];
    $highlights = [
        ['icon' => 'compass', 'title' => 'Guided process', 'copy' => 'Four short steps, written in plain language — with our team beside you from submission to completion.'],
        ['icon' => 'file-check', 'title' => 'Document check', 'copy' => 'Know exactly what to prepare before you start, so nothing bounces back for missing paperwork.'],
        ['icon' => 'radar', 'title' => 'Reference tracking', 'copy' => 'A unique reference number lets you follow your request online until it is delivered.'],
    ];
@endphp

@section('hero-crumbs')
    <a href="{{ route('home') }}">Home</a>
    <i data-lucide="chevron-right"></i>
    <a href="{{ route('public.services.index') }}">Services</a>
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
    <a href="#application" class="ds-btn ds-btn-mint">Start application <i data-lucide="arrow-down"></i></a>
    <a href="{{ route('public.services.index', $category ? ['category' => $category->slug] : []) }}" class="ds-btn ds-btn-ghost-light">Back to services</a>
@endsection

@section('hero-aside')
    <div class="ds-hero-card">
        <div class="ds-hero-card-chip" aria-hidden="true"></div>
        <span class="ds-hero-card-label">Ready to start?</span>
        <h3>Apply in 4 steps.</h3>
        <small>Tracked with a unique reference</small>
        <p>Your request is handled by our team and tracked with a unique reference number.</p>
        <a href="#application" class="ds-btn ds-btn-white">Start application <i data-lucide="arrow-down"></i></a>
    </div>
@endsection

@section('content')
    @include('partials.ds-hero', [
        'kicker' => ($category?->name ?? 'Service') . ' · Available',
        'title' => e($service->name),
        'lead' => e($service->description ?: 'Get professional assistance from Digital Star Consultants from application to completion.'),
        'points' => [$price, $service->duration, 'Guided & assisted'],
    ])

    @include('partials.ds-stats', [
        'compact' => true,
        'stats' => [
            ['value' => $service->is_free ? 'Quote' : $service->formatted_price, 'label' => 'Service fee', 'icon' => 'wallet'],
            ['value' => $service->duration, 'label' => 'Typical handling', 'icon' => 'clock'],
            ['value' => 'Guided', 'label' => 'Assisted application', 'icon' => 'badge-check'],
            ['value' => ($detailFields->count() + $fileFields->count()) . ' fields', 'label' => 'Details + documents', 'icon' => 'clipboard-list'],
        ],
    ])

    {{-- OVERVIEW --}}
    <section class="ds-section" id="overview">
        <div class="ds-container">
            @include('partials.ds-heading', [
                'kicker' => 'Service overview',
                'title' => 'Everything you need to know — upfront.',
                'copy' => ($service->description ? $service->description . ' ' : '') . 'No surprises: the fee, the documents and the timeline are all stated before you apply.',
            ])
            <div class="ds-grid-3-even">
                @foreach ($highlights as $item)
                    <article class="ds-service ds-reveal" style="--d:{{ $loop->index * 0.08 }}s">
                        <span class="ds-icon-box"><i data-lucide="{{ $item['icon'] }}"></i></span>
                        <h3>{{ $item['title'] }}</h3>
                        <p>{{ $item['copy'] }}</p>
                    </article>
                @endforeach
            </div>

            <div class="ds-two" id="requirements" style="margin-top:20px">
                <div class="ds-panel ds-reveal">
                    <span class="ds-kicker">What you'll need</span>
                    <h3>Prepare before applying</h3>
                    <ul class="ds-list">
                        <li><i data-lucide="circle-check"></i><span><strong>Accurate details</strong>: use information matching your official documents.</span></li>
                        @if ($fileFields->count())
                            <li><i data-lucide="circle-check"></i><span><strong>{{ $fileFields->count() }} {{ \Illuminate\Support\Str::plural('document', $fileFields->count()) }}</strong>: upload clear files in the document step.</span></li>
                        @endif
                        <li><i data-lucide="circle-check"></i><span><strong>Reachable contact</strong>: keep your phone available for follow-up.</span></li>
                    </ul>
                </div>
                <div class="ds-panel ds-panel-muted ds-reveal" style="--d:.1s">
                    <span class="ds-kicker">Process</span>
                    <h3>What happens next</h3>
                    <ol class="ds-numbered">
                        <li>Submit your application</li>
                        <li>We review the request</li>
                        <li>We contact you if needed</li>
                        <li>Receive updates &amp; result</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    {{-- APPLICATION WIZARD --}}
    <section class="ds-section ds-section-tint" id="application" style="scroll-margin-top:72px">
        <div class="ds-container ds-wizard">
            <aside class="ds-wizard-side">
                <span class="ds-kicker">Application</span>
                <h2 style="padding-top:12px;font-size:32px;font-weight:800;letter-spacing:-.02em;line-height:1.15">Start your request.</h2>
                <p style="padding-top:12px;color:var(--ds-muted-fg)">Complete the four steps. You can review everything before submitting.</p>

                <div class="ds-panel" style="margin-top:24px;padding:20px">
                    <span class="ds-kicker">Your progress</span>
                    <div class="ds-progress-head">
                        <strong data-progress-count>1 / 4</strong>
                        <span>Step <b data-progress-step>1</b> of 4</span>
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
                    <p class="ds-help">Need help? <a href="{{ route('public.contact.show') }}">Contact us</a></p>
                </div>
            </aside>

            <div class="ds-form-card">
                <form id="service-form" class="ds-form" enctype="multipart/form-data" novalidate
                      data-submit-url="{{ route('public.submissions.store') }}">
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">

                    {{-- Step 1 --}}
                    <section class="ds-step-panel" data-step="1">
                        <span class="ds-kicker">Step 01 of 04</span>
                        <h3>Tell us about yourself</h3>
                        <p>We'll use these details to contact you about your application.</p>
                        <div class="ds-form-row">
                            <label class="ds-field">Full name *
                                <input id="customer_name" name="customer_name" required autocomplete="name" placeholder="John Mwakyusa">
                            </label>
                            <label class="ds-field">Phone number *
                                <input id="customer_phone" name="customer_phone" type="tel" required autocomplete="tel" placeholder="07XX XXX XXX">
                            </label>
                            <label class="ds-field">Email address <span class="ds-optional">(optional)</span>
                                <input id="customer_email" type="email" name="customer_email" autocomplete="email" placeholder="example@gmail.com">
                            </label>
                            <label class="ds-field">Preferred date <span class="ds-optional">(optional)</span>
                                <input id="preferred_date" type="date" name="preferred_date">
                            </label>
                        </div>
                        <div class="ds-secure"><i data-lucide="lock"></i><span><strong>Your information is secure.</strong> It is used only to process this request.</span></div>
                    </section>

                    {{-- Step 2 --}}
                    <section class="ds-step-panel" data-step="2" hidden>
                        <span class="ds-kicker">Step 02 of 04</span>
                        <h3>Service details</h3>
                        <p>Provide the information required for <strong>{{ $service->name }}</strong>.</p>
                        @if ($detailFields->isNotEmpty())
                            <div class="ds-form-row">
                                @foreach ($detailFields as $field)
                                    @include('services.partials.field', ['field' => $field])
                                @endforeach
                            </div>
                        @else
                            <div class="ds-empty">
                                <span class="ds-icon-box"><i data-lucide="check"></i></span>
                                <h3>No additional details required</h3>
                                <p>You can continue to document upload and review.</p>
                            </div>
                        @endif
                        <label class="ds-field" style="margin-top:20px">Additional notes <span class="ds-optional">(optional)</span>
                            <textarea id="notes" name="customer_notes" rows="4" placeholder="Tell us anything else we should know"></textarea>
                        </label>
                    </section>

                    {{-- Step 3 --}}
                    <section class="ds-step-panel" data-step="3" hidden>
                        <span class="ds-kicker">Step 03 of 04</span>
                        <h3>Upload documents</h3>
                        <p>Attach clear copies of the documents needed for this service. PDF, JPG, PNG or Word.</p>
                        @if ($fileFields->isNotEmpty())
                            <div class="ds-files">
                                @foreach ($fileFields as $field)
                                    <label class="ds-file">
                                        <span class="ds-icon-box"><i data-lucide="cloud-upload"></i></span>
                                        <span style="min-width:0">
                                            <strong>{{ $field->label }} @if ($field->is_required)*@else<span class="ds-optional">(optional)</span>@endif</strong>
                                            <small data-file-name-for="field-{{ $field->field_key }}">{{ $field->help_text ?: 'Click to choose a file' }}</small>
                                        </span>
                                        <input id="field-{{ $field->field_key }}" type="file" name="fields[{{ $field->field_key }}]"
                                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" data-label="{{ $field->label }}" @required($field->is_required)>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="ds-empty">
                                <span class="ds-icon-box"><i data-lucide="file-check"></i></span>
                                <h3>No document upload required</h3>
                                <p>Continue to the final review.</p>
                            </div>
                        @endif
                    </section>

                    {{-- Step 4 --}}
                    <section class="ds-step-panel" data-step="4" hidden>
                        <span class="ds-kicker">Step 04 of 04</span>
                        <h3>Review your application</h3>
                        <p>Please check your details carefully before submitting.</p>
                        @foreach (['contact' => '01 · Contact information', 'service' => '02 · Service information', 'files' => '03 · Documents'] as $key => $label)
                            <div class="ds-review">
                                <div class="ds-review-head">
                                    <strong>{{ $label }}</strong>
                                    <button type="button" class="ds-link-btn" data-go-step="{{ $loop->iteration }}">Edit</button>
                                </div>
                                <dl class="ds-meta" data-review="{{ $key }}"></dl>
                            </div>
                        @endforeach
                        <label class="ds-check" style="margin-top:20px">
                            <input type="checkbox" id="application-consent" required>
                            <span>I confirm that the information provided is accurate and I agree that Digital Star may use it to process this request.</span>
                        </label>
                    </section>

                    {{-- Success (shown after submission) --}}
                    <section class="ds-success" data-success hidden>
                        <span class="ds-icon-box"><i data-lucide="check"></i></span>
                        <h3>Application received</h3>
                        <p>Keep this reference number. You'll need it to track your request.</p>
                        <span class="ds-ref" data-reference></span>
                        <div class="ds-actions" style="justify-content:center">
                            <a class="ds-btn ds-btn-primary" data-track-link href="#">Track your request <i data-lucide="arrow-right"></i></a>
                            <a class="ds-btn ds-btn-soft" href="{{ route('public.services.index') }}">Browse more services</a>
                        </div>
                    </section>

                    <div class="ds-alert ds-alert-error" data-form-message role="alert" hidden></div>

                    <div class="ds-wizard-nav" data-wizard-nav>
                        <button type="button" class="ds-btn ds-btn-soft" data-back hidden><i data-lucide="arrow-left"></i> Back</button>
                        <span class="ds-spacer"></span>
                        <button type="button" class="ds-btn ds-btn-primary" data-next>Save &amp; continue <i data-lucide="arrow-right"></i></button>
                        <button type="submit" class="ds-btn ds-btn-primary" data-submit hidden>Submit application <i data-lucide="send"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    @include('partials.ds-cta', [
        'kicker' => 'Need clarification?',
        'title' => 'Talk to our team first.',
        'copy' => 'Digital Star can guide you through the requirements and next steps before you apply.',
        'buttonLabel' => 'Contact us',
        'url' => route('public.contact.show'),
        'checklist' => ['Secure & confidential', 'Reference tracking', 'Team support'],
    ])
@endsection

@push('scripts')
    <script src="{{ asset('js/application-form.js') }}"></script>
@endpush
