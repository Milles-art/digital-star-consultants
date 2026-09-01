@extends('layouts.app')

@section('content')
@php
    $category = $service->category;
    $parentCategory = $category?->parent;
    $fileFields = $service->fields->filter(fn ($field) => $field->field_type === 'file');
    $detailFields = $service->fields->filter(fn ($field) => ! in_array($field->field_type, ['file', 'hidden']));
    $slug = strtolower(($category?->slug ?? '') . ' ' . $service->slug . ' ' . $service->name);
    $iconKey = match (true) {
        str_contains($slug, 'passport') || str_contains($slug, 'visa') || str_contains($slug, 'immigration') => 'passport',
        str_contains($slug, 'nida') || str_contains($slug, 'identification') || str_contains($slug, 'birth') || str_contains($slug, 'death') || str_contains($slug, 'police') || str_contains($slug, 'clearance') || str_contains($slug, 'driving') => 'government',
        str_contains($slug, 'job') || str_contains($slug, 'career') => 'jobs',
        str_contains($slug, 'education') || str_contains($slug, 'school') || str_contains($slug, 'scholar') || str_contains($slug, 'exam') => 'education',
        str_contains($slug, 'tra') || str_contains($slug, 'tax') || str_contains($slug, 'tin') => 'tax',
        str_contains($slug, 'travel') || str_contains($slug, 'flight') || str_contains($slug, 'hotel') || str_contains($slug, 'booking') => 'travel',
        str_contains($slug, 'printing') => 'printing',
        str_contains($slug, 'branding') || str_contains($slug, 'design') => 'branding',
        str_contains($slug, 'stationery') => 'stationery',
        str_contains($slug, 'business') || str_contains($slug, 'brela') || str_contains($slug, 'company') || str_contains($slug, 'ngo') => 'business',
        str_contains($slug, 'mobile') || str_contains($slug, 'app') => 'mobile',
        str_contains($slug, 'website') || str_contains($slug, 'web') => 'website',
        str_contains($slug, 'it') || str_contains($slug, 'technology') || str_contains($slug, 'software') || str_contains($slug, 'hosting') => 'it',
        str_contains($slug, 'support') || str_contains($slug, 'assistance') => 'support',
        default => 'forms',
    };
@endphp

<div class="service-page">
    <div class="service-page-shell">
        <nav class="service-breadcrumbs" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a><span>›</span>
            <a href="{{ route('public.services.index') }}">Services</a>
            @if($parentCategory)<span>›</span><a href="{{ route('public.services.index', ['category' => $parentCategory->slug]) }}">{{ $parentCategory->name }}</a>@endif
            @if($category)<span>›</span><a href="{{ route('public.services.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>@endif
            <span>›</span><strong>{{ $service->name }}</strong>
        </nav>

        <section class="service-hero-card">
            <div class="service-hero-main">
                <div class="service-icon-badge">@include('partials.icon', ['iconKey' => $iconKey])</div>
                <div class="service-hero-copy">
                    <div class="service-kicker-row"><span class="service-eyebrow">{{ $category?->name ?? 'SERVICE' }}</span><span class="service-live"><i></i> Available</span></div>
                    <h1>{{ $service->name }}</h1>
                    <p>{{ $service->description ?: 'Get professional assistance from Digital Star Consultants from application to completion.' }}</p>
                    <div class="service-metrics">
                        <div><span class="metric-icon">@include('partials.icon', ['iconKey' => 'forms'])</span><span><small>Service fee</small><strong>{{ $service->is_free ? 'Quote on request' : $service->formatted_price }}</strong></span></div>
                        <div><span class="metric-icon">◷</span><span><small>Typical handling</small><strong>{{ $service->duration }}</strong></span></div>
                        <div><span class="metric-icon">✓</span><span><small>Application</small><strong>Guided & assisted</strong></span></div>
                    </div>
                </div>
            </div>
            <div class="service-hero-action">
                <div><span class="mini-label">READY TO START?</span><strong>Apply for this service</strong><p>Your request is handled by our team and tracked with a unique reference.</p></div>
                <a href="#application" class="button button-yellow">Start application <span>↓</span></a>
            </div>
        </section>

        <div class="service-page-nav" aria-label="Service sections">
            <a class="is-active" href="#overview">Overview</a>
            <a href="#requirements">What you'll need</a>
            <a href="#application">Application</a>
        </div>

        <section id="overview" class="service-overview-grid">
            <article class="service-info-panel service-main-panel">
                <div class="panel-heading"><div><span class="section-eyebrow">SERVICE OVERVIEW</span><h2>Everything you need to know.</h2></div><span class="panel-accent">DS</span></div>
                <div class="overview-copy"><p>{{ $service->description ?: 'Digital Star Consultants provides guided support for this service, helping you prepare the right information, complete the request correctly, and follow its progress.' }}</p><p>We will review the information you submit and contact you when clarification or additional documents are needed.</p></div>
                <div class="overview-highlights">
                    <div><span>01</span><strong>Guided process</strong><p>Clear steps with support from our team.</p></div>
                    <div><span>02</span><strong>Document check</strong><p>We help you identify the information required.</p></div>
                    <div><span>03</span><strong>Reference tracking</strong><p>Track your request after submission.</p></div>
                </div>
            </article>

            <aside class="service-side-stack" id="requirements">
                <section class="service-info-panel compact-panel">
                    <div class="panel-heading"><div><span class="section-eyebrow">WHAT YOU'LL NEED</span><h3>Prepare before applying</h3></div></div>
                    <ul class="service-check-list">
                        <li><span>✓</span><div><strong>Accurate details</strong><p>Use information that matches your official documents.</p></div></li>
                        @if($fileFields->count())<li><span>✓</span><div><strong>{{ $fileFields->count() }} document{{ $fileFields->count() > 1 ? 's' : '' }}</strong><p>Upload clear files in the document step.</p></div></li>@endif
                        <li><span>✓</span><div><strong>Reachable contact</strong><p>Keep your phone available for follow-up.</p></div></li>
                    </ul>
                </section>
                <section class="service-info-panel compact-panel">
                    <div class="panel-heading"><div><span class="section-eyebrow">PROCESS</span><h3>What happens next</h3></div></div>
                    <ol class="service-mini-process"><li><b>1</b><span>Submit your application</span></li><li><b>2</b><span>We review the request</span></li><li><b>3</b><span>We contact you if needed</span></li><li><b>4</b><span>Receive updates & result</span></li></ol>
                </section>
            </aside>
        </section>

        <section id="application" class="application-section-heading">
            <div><span class="section-eyebrow">APPLICATION</span><h2>Let's get your request started.</h2><p>Complete the four steps below. You can review everything before submitting.</p></div>
            <div class="application-security"><span>⌑</span><div><strong>Secure & confidential</strong><small>Your information is used only to process this request.</small></div></div>
        </section>

        <div class="application-layout application-layout-modern">
            <aside class="application-sidebar">
                <section class="progress-card">
                    <div class="progress-card-head"><strong>Your progress</strong><span id="progress-count">1 / 4</span></div>
                    <ol class="application-steps" id="application-steps">
                        <li class="is-active" data-step-item="1"><span class="step-number">1</span><div><strong>Contact information</strong><small>Your basic contact details</small></div></li>
                        <li data-step-item="2"><span class="step-number">2</span><div><strong>Service details</strong><small>Information needed for this service</small></div></li>
                        <li data-step-item="3"><span class="step-number">3</span><div><strong>Documents</strong><small>Upload required documents</small></div></li>
                        <li data-step-item="4"><span class="step-number">4</span><div><strong>Review & confirm</strong><small>Check everything before sending</small></div></li>
                    </ol>
                    <div class="help-card"><div><div class="help-icon">?</div><div><strong>Need help?</strong><p>Our team can guide you through the application.</p></div></div><a href="{{ route('public.contact.show') }}">Contact us <span>→</span></a></div>
                </section>
            </aside>

            <main class="application-main">
                <form id="service-form" class="modern-application-form" enctype="multipart/form-data" novalidate>
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">

                    <section class="form-step is-active" data-step="1">
                        <div class="form-step-heading"><span class="step-kicker">STEP 01 OF 04</span><h2>Tell us about yourself</h2><p>We'll use these details to contact you about your application.</p></div>
                        <div class="form-grid two-columns">
                            <div class="field-control"><label for="customer_name">Full name <b>*</b></label><div class="input-wrap"><span>◉</span><input id="customer_name" name="customer_name" required autocomplete="name" placeholder="Enter your full name"></div></div>
                            <div class="field-control"><label for="customer_phone">Phone number <b>*</b></label><div class="input-wrap"><span>☎</span><input id="customer_phone" name="customer_phone" required autocomplete="tel" placeholder="07XX XXX XXX"></div></div>
                            <div class="field-control"><label for="customer_email">Email address <span class="optional">Optional</span></label><div class="input-wrap"><span>✉</span><input id="customer_email" type="email" name="customer_email" autocomplete="email" placeholder="you@example.com"></div></div>
                            <div class="field-control"><label for="preferred_date">Preferred date <span class="optional">Optional</span></label><div class="input-wrap"><span>▣</span><input id="preferred_date" type="date" name="preferred_date"></div></div>
                        </div>
                        <div class="form-note"><span>🔒</span><div><strong>Your information is secure</strong><p>We only use the information you provide to process and communicate about your request.</p></div></div>
                    </section>

                    <section class="form-step" data-step="2" hidden>
                        <div class="form-step-heading"><span class="step-kicker">STEP 02 OF 04</span><h2>Service details</h2><p>Provide the information required for <strong>{{ $service->name }}</strong>.</p></div>
                        @if($detailFields->isNotEmpty())
                            <div class="dynamic-form-grid">
                                @foreach($detailFields as $field)
                                    <div class="field-control {{ $field->field_type === 'textarea' ? 'field-span-2' : '' }}">
                                        <label for="field-{{ $field->field_key }}">{{ $field->label }} @if($field->is_required)<b>*</b>@else<span class="optional">Optional</span>@endif</label>
                                        @if($field->help_text)<small class="field-help">{{ $field->help_text }}</small>@endif
                                        @if($field->field_type === 'textarea')
                                            <textarea id="field-{{ $field->field_key }}" name="fields[{{ $field->field_key }}]" {{ $field->is_required ? 'required' : '' }} placeholder="{{ $field->placeholder ?: 'Enter your answer' }}">{{ $field->default_value }}</textarea>
                                        @elseif(in_array($field->field_type, ['select','radio']))
                                            <div class="select-wrap"><select id="field-{{ $field->field_key }}" name="fields[{{ $field->field_key }}]" {{ $field->is_required ? 'required' : '' }}><option value="">Select an option</option>@foreach($field->options ?? [] as $option)<option value="{{ $option }}" @selected($field->default_value === $option)>{{ $option }}</option>@endforeach</select><span>⌄</span></div>
                                        @elseif($field->field_type === 'checkbox')
                                            <label class="checkbox-control"><input id="field-{{ $field->field_key }}" type="checkbox" name="fields[{{ $field->field_key }}]" value="1" {{ $field->default_value ? 'checked' : '' }} {{ $field->is_required ? 'required' : '' }}><span>{{ $field->label }}</span></label>
                                        @else
                                            <div class="input-wrap"><input id="field-{{ $field->field_key }}" type="{{ in_array($field->field_type, ['number','date','email','tel']) ? $field->field_type : 'text' }}" name="fields[{{ $field->field_key }}]" value="{{ $field->default_value }}" {{ $field->is_required ? 'required' : '' }} placeholder="{{ $field->placeholder ?: 'Enter your answer' }}"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-form-state"><span>✓</span><strong>No additional details required</strong><p>You can continue to document upload and review.</p></div>
                        @endif
                        <div class="field-control notes-field"><label for="notes">Additional notes <span class="optional">Optional</span></label><textarea id="notes" name="notes" placeholder="Tell us anything else we should know about your request"></textarea></div>
                    </section>

                    <section class="form-step" data-step="3" hidden>
                        <div class="form-step-heading"><span class="step-kicker">STEP 03 OF 04</span><h2>Upload documents</h2><p>Attach clear copies of the documents needed for this service.</p></div>
                        @if($fileFields->isNotEmpty())
                            <div class="upload-grid">@foreach($fileFields as $field)<div class="upload-card"><div class="upload-card-top"><span class="upload-icon">@include('partials.icon', ['iconKey' => 'forms'])</span><span class="upload-required">{{ $field->is_required ? 'Required' : 'Optional' }}</span></div><label>{{ $field->label }} @if($field->is_required)<b>*</b>@endif</label>@if($field->help_text)<p>{{ $field->help_text }}</p>@endif<label class="dropzone" for="field-{{ $field->field_key }}"><span class="dropzone-icon">↑</span><strong>Choose file</strong><small>PDF, JPG or PNG</small><input id="field-{{ $field->field_key }}" type="file" name="fields[{{ $field->field_key }}]" accept=".pdf,.jpg,.jpeg,.png" {{ $field->is_required ? 'required' : '' }}></label><div class="file-selected" data-file-name-for="field-{{ $field->field_key }}" hidden></div></div>@endforeach</div>
                        @else
                            <div class="empty-form-state"><span>✓</span><strong>No document upload is required</strong><p>Continue to the final review.</p></div>
                        @endif
                    </section>

                    <section class="form-step" data-step="4" hidden>
                        <div class="form-step-heading"><span class="step-kicker">STEP 04 OF 04</span><h2>Review your application</h2><p>Please check your details carefully before submitting.</p></div>
                        <div class="review-card"><div class="review-section"><div><span>01</span><h3>Contact information</h3></div><button type="button" data-go-step="1">Edit</button></div><dl class="review-list" id="review-contact"></dl></div>
                        <div class="review-card"><div class="review-section"><div><span>02</span><h3>Service information</h3></div><button type="button" data-go-step="2">Edit</button></div><dl class="review-list" id="review-service"></dl></div>
                        <div class="review-card"><div class="review-section"><div><span>03</span><h3>Documents</h3></div><button type="button" data-go-step="3">Edit</button></div><div class="review-files" id="review-files"></div></div>
                        <label class="consent-control"><input type="checkbox" id="application-consent" required><span>I confirm that the information provided is accurate and I agree that Digital Star may use it to process this request.</span></label>
                    </section>

                    <div id="form-message" class="form-message" hidden></div>
                    <div class="form-navigation"><button type="button" class="button button-outline" id="back-button" hidden>← Back</button><span class="navigation-status">Step <strong id="navigation-step">1</strong> of 4</span><button type="button" class="button button-blue" id="next-button">Save & Continue <span>→</span></button><button type="submit" class="button button-yellow" id="submit-button" hidden>Submit application <span>↗</span></button></div>
                </form>
            </main>

            <aside class="application-info-column">
                <section class="info-card"><span class="info-kicker">SERVICE SUPPORT</span><p>Need clarification before you apply? Digital Star can guide you through the requirements and next steps.</p><a class="side-cta" href="{{ route('public.contact.show') }}">Talk to our team <span>→</span></a></section>
                <section class="info-card"><span class="info-kicker">AFTER SUBMISSION</span><div class="after-list"><div><b>01</b><span>Receive a reference number</span></div><div><b>02</b><span>Track your request online</span></div><div><b>03</b><span>Get updates from our team</span></div></div></section>
            </aside>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('service-form'); if (!form) return;
    const steps = [...document.querySelectorAll('.form-step')], stepItems = [...document.querySelectorAll('[data-step-item]')];
    const next = document.getElementById('next-button'), back = document.getElementById('back-button'), submit = document.getElementById('submit-button');
    const count = document.getElementById('progress-count'), navStep = document.getElementById('navigation-step'), message = document.getElementById('form-message');
    let current = 1;
    const escapeHtml = str => String(str).replace(/[&<>'"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#039;','"':'&quot;'}[c]));
    const showMessage = (text, type='error') => { message.hidden=false; message.className=`form-message ${type}`; message.textContent=text; };
    const clearMessage = () => { message.hidden=true; };
    const validateStep = n => { const panel=steps[n-1]; for (const el of [...panel.querySelectorAll('input,select,textarea')].filter(e=>!e.disabled&&e.type!=='hidden')) if(!el.checkValidity()){el.reportValidity();el.focus({preventScroll:true});return false;} return true; };
    const updateReview = () => {
        const value=name => form.elements[name]?.value?.trim() || '—';
        document.getElementById('review-contact').innerHTML=`<div><dt>Full name</dt><dd>${escapeHtml(value('customer_name'))}</dd></div><div><dt>Phone number</dt><dd>${escapeHtml(value('customer_phone'))}</dd></div><div><dt>Email address</dt><dd>${escapeHtml(value('customer_email'))}</dd></div><div><dt>Preferred date</dt><dd>${escapeHtml(value('preferred_date'))}</dd></div>`;
        const dynamic=[...form.querySelectorAll('[name^="fields["]')].filter(e=>e.type!=='file');
        const rows=dynamic.map(el=>{const label=form.querySelector(`label[for="${CSS.escape(el.id)}"]`)?.childNodes?.[0]?.textContent?.trim()||el.name;const val=el.type==='checkbox'?(el.checked?'Yes':'No'):(el.value||'—');return `<div><dt>${escapeHtml(label)}</dt><dd>${escapeHtml(val)}</dd></div>`;});
        document.getElementById('review-service').innerHTML=rows.length?rows.join(''):'<div><dt>Additional information</dt><dd>No additional fields</dd></div>';
        const files=[...form.querySelectorAll('input[type="file"]')].map(input=>{const label=form.querySelector(`label[for="${CSS.escape(input.id)}"]`)?.textContent?.trim()||'Document';return `<div><span>✓</span><strong>${escapeHtml(label)}</strong><small>${input.files?.[0]?escapeHtml(input.files[0].name):'No file selected'}</small></div>`;});
        document.getElementById('review-files').innerHTML=files.length?files.join(''):'<p class="muted-review">No documents required.</p>';
    };
    const setStep=n=>{current=n;steps.forEach((p,i)=>{const active=i+1===current;p.hidden=!active;p.classList.toggle('is-active',active)});stepItems.forEach(item=>{const x=Number(item.dataset.stepItem);item.classList.toggle('is-active',x===current);item.classList.toggle('is-complete',x<current)});count.textContent=`${current} / 4`;navStep.textContent=current;back.hidden=current===1;next.hidden=current===4;submit.hidden=current!==4;if(current===4)updateReview();clearMessage();window.scrollTo({top:document.getElementById('application').getBoundingClientRect().top+window.scrollY-90,behavior:'smooth'});};
    next.addEventListener('click',()=>{if(validateStep(current))setStep(Math.min(4,current+1));});
    back.addEventListener('click',()=>setStep(Math.max(1,current-1)));
    document.querySelectorAll('[data-go-step]').forEach(b=>b.addEventListener('click',()=>setStep(Number(b.dataset.goStep))));
    form.querySelectorAll('input[type="file"]').forEach(input=>input.addEventListener('change',()=>{const target=document.querySelector(`[data-file-name-for="${CSS.escape(input.id)}"]`);if(!target)return;target.hidden=!input.files?.length;target.textContent=input.files?.length?`✓ ${input.files[0].name}`:'';}));
    form.addEventListener('submit',async e=>{e.preventDefault();if(!validateStep(4))return;clearMessage();submit.disabled=true;submit.innerHTML='Submitting…';try{const response=await fetch('{{ route('public.submissions.store') }}',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:new FormData(form)});const data=await response.json().catch(()=>({}));if(!response.ok)throw new Error(data.message||'We could not submit your application. Please check your details.');message.hidden=false;message.className='form-message success';message.innerHTML=`<strong>Application received.</strong><br>Your reference number is <b>${escapeHtml(data.data.reference_number)}</b>. <a href="/track/status/${encodeURIComponent(data.data.reference_number)}">Track your request →</a>`;submit.hidden=true;back.hidden=true;next.hidden=true;}catch(error){showMessage(error.message,'error')}finally{submit.disabled=false;submit.innerHTML='Submit application <span>↗</span>';}});
});
</script>
@endsection
