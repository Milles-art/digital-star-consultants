@extends('layouts.app')

@section('title', $service->name.' — Digital Star Consultants')
@section('meta_description', $service->description ?? 'Submit a request for '.$service->name)

@php
    $price = $service->formatted_price ?? ($service->price ? 'TSh '.number_format($service->price, 0) : 'Free');
@endphp

@section('content')
<section class="border-b border-[color:var(--color-line)] bg-white">
    <div class="shell py-8 md:py-10">
        <nav class="mb-4 text-sm text-[color:var(--color-ink-faint)]" aria-label="Breadcrumb">
            <a href="{{ route('public.services.index') }}" class="hover:text-[color:var(--color-brand-700)]">Services</a>
            <span class="mx-1.5">/</span>
            @if ($service->category)
                <a href="{{ route('public.services.index', ['category' => $service->category->slug]) }}"
                   class="hover:text-[color:var(--color-brand-700)]">{{ $service->category->name }}</a>
                <span class="mx-1.5">/</span>
            @endif
            <span class="text-[color:var(--color-ink)]">{{ $service->name }}</span>
        </nav>

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="max-w-2xl">
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $service->name }}</h1>
                @if ($service->description)
                    <p class="mt-3 leading-relaxed text-[color:var(--color-ink-soft)]">{{ $service->description }}</p>
                @endif
            </div>
            <div class="rounded-2xl border border-[color:var(--color-line)] bg-[color:var(--color-surface-muted)] px-5 py-4 text-right">
                <p class="text-xs font-semibold uppercase tracking-wider text-[color:var(--color-ink-faint)]">Price</p>
                <p class="text-xl font-bold">{{ $price }}</p>
            </div>
        </div>
    </div>
</section>

<section class="shell section !pt-10">
    <div class="grid gap-10 lg:grid-cols-12">
        <div class="lg:col-span-7 xl:col-span-8">
            <div id="dsc-form-wrap" class="card p-6 sm:p-8">
                <h2 class="text-xl font-bold tracking-tight">Start your request</h2>
                <p class="mt-1 text-sm text-[color:var(--color-ink-soft)]">
                    Fill in your details. You’ll get a reference number to track status.
                </p>

                <div id="dsc-form-error" class="mt-4 hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert"></div>

                <form id="dsc-request-form"
                      action="{{ route('public.submissions.store') }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="mt-6 space-y-6"
                      novalidate>
                    @csrf
                    <input type="hidden" name="service_id" value="{{ $service->id }}">

                    <fieldset>
                        <legend class="text-xs font-bold uppercase tracking-[0.14em] text-[color:var(--color-brand-600)]">
                            Your details
                        </legend>
                        <div class="mt-4 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="customer_name" class="field-label">Full name <span class="text-red-600">*</span></label>
                                <input type="text" id="customer_name" name="customer_name" required maxlength="255"
                                       autocomplete="name" class="field-input" data-field-name="customer_name">
                                <span class="field-error" data-error-for="customer_name"></span>
                            </div>
                            <div>
                                <label for="customer_phone" class="field-label">Phone <span class="text-red-600">*</span></label>
                                <input type="text" id="customer_phone" name="customer_phone" required maxlength="20"
                                       autocomplete="tel" placeholder="+255…" class="field-input" data-field-name="customer_phone">
                                <span class="field-error" data-error-for="customer_phone"></span>
                            </div>
                            <div>
                                <label for="customer_email" class="field-label">Email</label>
                                <input type="email" id="customer_email" name="customer_email" maxlength="255"
                                       autocomplete="email" class="field-input" data-field-name="customer_email">
                                <span class="field-error" data-error-for="customer_email"></span>
                            </div>
                            <div>
                                <label for="preferred_date" class="field-label">Preferred date</label>
                                <input type="date" id="preferred_date" name="preferred_date"
                                       min="{{ date('Y-m-d') }}" class="field-input" data-field-name="preferred_date">
                                <span class="field-error" data-error-for="preferred_date"></span>
                            </div>
                        </div>
                    </fieldset>

                    @if ($service->fields->isNotEmpty())
                        <fieldset class="border-t border-[color:var(--color-line)] pt-6">
                            <legend class="text-xs font-bold uppercase tracking-[0.14em] text-[color:var(--color-brand-600)]">
                                Additional details
                            </legend>
                            <div class="mt-4 space-y-5">
                                @foreach ($service->fields as $field)
                                    @php
                                        $key = $field->field_key;
                                        $label = $field->label;
                                        $type = $field->field_type;
                                        $required = (bool) $field->is_required;
                                        $ph = $field->placeholder ?? '';
                                        $options = $field->options ?? [];
                                        $inputName = 'fields['.$key.']';
                                        $inputId = 'field_'.$key;
                                        $errKey = 'fields.'.$key;
                                    @endphp
                                    <div>
                                        @if ($type === 'radio')
                                            <fieldset>
                                                <legend class="field-label">
                                                    {{ $label }}
                                                    @if ($required)<span class="text-red-600">*</span>@endif
                                                </legend>
                                                <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                                    @foreach ($options as $optKey => $option)
                                                        @php
                                                            $val = is_array($option) ? ($option['value'] ?? $option['label'] ?? $optKey) : (is_string($optKey) ? $optKey : $option);
                                                            $txt = is_array($option) ? ($option['label'] ?? $option['value'] ?? $optKey) : $option;
                                                        @endphp
                                                        <label class="field-choice">
                                                            <input type="radio" name="{{ $inputName }}" value="{{ $val }}"
                                                                   @if($required) required @endif
                                                                   data-field-name="{{ $errKey }}"
                                                                   class="mt-0.5 h-4 w-4 accent-[color:var(--color-brand-600)]">
                                                            <span>{{ $txt }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <span class="field-error" data-error-for="{{ $errKey }}"></span>
                                            </fieldset>
                                        @elseif ($type === 'checkbox')
                                            <label class="field-choice" for="{{ $inputId }}">
                                                <input type="hidden" name="{{ $inputName }}" value="0">
                                                <input type="checkbox" id="{{ $inputId }}" name="{{ $inputName }}" value="1"
                                                       @if($required) required @endif
                                                       data-field-name="{{ $errKey }}"
                                                       class="mt-0.5 h-4 w-4 accent-[color:var(--color-brand-600)]">
                                                <span class="font-medium">
                                                    {{ $label }}
                                                    @if($required)<span class="text-red-600">*</span>@endif
                                                </span>
                                            </label>
                                            <span class="field-error" data-error-for="{{ $errKey }}"></span>
                                        @else
                                            <label for="{{ $inputId }}" class="field-label">
                                                {{ $label }}
                                                @if($required)<span class="text-red-600">*</span>@endif
                                            </label>

                                            @switch($type)
                                                @case('textarea')
                                                    <textarea id="{{ $inputId }}" name="{{ $inputName }}" rows="3"
                                                              @if($required) required @endif placeholder="{{ $ph }}"
                                                              data-field-name="{{ $errKey }}" class="field-input resize-y"></textarea>
                                                    @break
                                                @case('select')
                                                    <select id="{{ $inputId }}" name="{{ $inputName }}"
                                                            @if($required) required @endif
                                                            data-field-name="{{ $errKey }}" class="field-input">
                                                        <option value="">{{ $ph ?: 'Select…' }}</option>
                                                        @foreach ($options as $optKey => $option)
                                                            @php
                                                                $val = is_array($option) ? ($option['value'] ?? $option['label'] ?? $optKey) : (is_string($optKey) ? $optKey : $option);
                                                                $txt = is_array($option) ? ($option['label'] ?? $option['value'] ?? $optKey) : $option;
                                                            @endphp
                                                            <option value="{{ $val }}">{{ $txt }}</option>
                                                        @endforeach
                                                    </select>
                                                    @break
                                                @case('file')
                                                    <input type="file" id="{{ $inputId }}" name="{{ $inputName }}"
                                                           @if($required) required @endif
                                                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.webp"
                                                           data-field-name="{{ $errKey }}" class="field-file">
                                                    @break
                                                @case('number')
                                                    <input type="number" id="{{ $inputId }}" name="{{ $inputName }}"
                                                           @if($required) required @endif placeholder="{{ $ph }}" step="any"
                                                           data-field-name="{{ $errKey }}" class="field-input">
                                                    @break
                                                @case('email')
                                                    <input type="email" id="{{ $inputId }}" name="{{ $inputName }}"
                                                           @if($required) required @endif placeholder="{{ $ph }}"
                                                           data-field-name="{{ $errKey }}" class="field-input">
                                                    @break
                                                @case('date')
                                                    <input type="date" id="{{ $inputId }}" name="{{ $inputName }}"
                                                           @if($required) required @endif
                                                           data-field-name="{{ $errKey }}" class="field-input">
                                                    @break
                                                @default
                                                    <input type="text" id="{{ $inputId }}" name="{{ $inputName }}"
                                                           @if($required) required @endif placeholder="{{ $ph }}"
                                                           data-field-name="{{ $errKey }}" class="field-input">
                                            @endswitch
                                            <span class="field-error" data-error-for="{{ $errKey }}"></span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </fieldset>
                    @endif

                    <fieldset class="border-t border-[color:var(--color-line)] pt-6">
                        <legend class="text-xs font-bold uppercase tracking-[0.14em] text-[color:var(--color-brand-600)]">
                            Anything else?
                        </legend>
                        <div class="mt-4">
                            <label for="customer_notes" class="field-label">Additional notes</label>
                            <textarea id="customer_notes" name="customer_notes" rows="3"
                                      placeholder="Anything we should know…"
                                      data-field-name="customer_notes" class="field-input resize-y"></textarea>
                            <span class="field-error" data-error-for="customer_notes"></span>
                        </div>
                    </fieldset>

                    <div class="flex flex-col gap-3 border-t border-[color:var(--color-line)] pt-6 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs text-[color:var(--color-ink-faint)]">
                            By submitting, you agree to be contacted about this request.
                        </p>
                        <button type="submit" id="dsc-submit" class="btn btn-lg btn-primary sm:shrink-0">
                            <span data-submit-label>Submit request</span>
                        </button>
                    </div>
                </form>
            </div>

            <div id="dsc-success" class="mt-6 hidden rounded-2xl border border-emerald-200 bg-emerald-50 p-6" role="status">
                <h3 class="text-lg font-bold text-emerald-900">Request submitted</h3>
                <p class="mt-1 text-sm text-emerald-800">Save your reference number to track status.</p>
                <p class="mt-4 text-2xl font-extrabold tracking-wide text-emerald-900" id="dsc-reference"></p>
                <a href="#" id="dsc-track-link" class="btn btn-primary mt-4 inline-flex text-sm">Track this request</a>
            </div>
        </div>

        <aside class="lg:col-span-5 xl:col-span-4">
            <div class="lg:sticky lg:top-24 space-y-5">
                <div class="card p-6">
                    <h2 class="text-base font-bold tracking-tight">Request summary</h2>
                    <dl class="mt-5 space-y-4 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-[color:var(--color-ink-faint)]">Service</dt>
                            <dd class="text-right font-semibold">{{ $service->name }}</dd>
                        </div>
                        @if ($service->category)
                            <div class="flex justify-between gap-4">
                                <dt class="text-[color:var(--color-ink-faint)]">Category</dt>
                                <dd class="text-right font-semibold">{{ $service->category->name }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between gap-4">
                            <dt class="text-[color:var(--color-ink-faint)]">Price</dt>
                            <dd class="text-right font-semibold">{{ $price }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-[color:var(--color-ink-faint)]">Account</dt>
                            <dd class="text-right font-semibold">Not required</dd>
                        </div>
                    </dl>
                    <div class="divider my-6"></div>
                    <ul class="space-y-3 text-sm text-[color:var(--color-ink-soft)]">
                        @foreach (['Instant reference number', 'Live status tracking', 'Dedicated consultant assigned'] as $benefit)
                            <li class="flex items-start gap-2.5">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-[color:var(--color-brand-500)]" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M8.1 13.3L5 10.2l1.3-1.3 1.8 1.8 5.6-5.6L15 6.4z"/></svg>
                                <span>{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </aside>
    </div>
</section>
@endsection

@push('scripts')
<script>
(function () {
    var form = document.getElementById('dsc-request-form');
    if (!form) return;

    var wrap = document.getElementById('dsc-form-wrap');
    var successBox = document.getElementById('dsc-success');
    var refEl = document.getElementById('dsc-reference');
    var trackLink = document.getElementById('dsc-track-link');
    var formError = document.getElementById('dsc-form-error');
    var submitBtn = document.getElementById('dsc-submit');
    var btnLabel = submitBtn.querySelector('[data-submit-label]');
    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

    function clearErrors() {
        formError.classList.add('hidden');
        formError.textContent = '';
        form.querySelectorAll('[data-error-for]').forEach(function (el) { el.textContent = ''; });
        form.querySelectorAll('[aria-invalid="true"]').forEach(function (el) { el.removeAttribute('aria-invalid'); });
    }

    function showErrors(errors) {
        Object.keys(errors).forEach(function (key) {
            var messages = errors[key];
            var message = Array.isArray(messages) ? messages[0] : String(messages);
            var slot = form.querySelector('[data-error-for="' + key + '"]');
            var control = form.querySelector('[data-field-name="' + key + '"], [name="' + key + '"]');
            if (slot) slot.textContent = message;
            if (control) control.setAttribute('aria-invalid', 'true');
        });
        formError.textContent = 'Please fix the highlighted fields.';
        formError.classList.remove('hidden');
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        clearErrors();
        submitBtn.disabled = true;
        if (btnLabel) btnLabel.textContent = 'Submitting…';

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(function (res) {
            return res.json().then(function (data) {
                return { ok: res.ok, status: res.status, data: data };
            }).catch(function () {
                return { ok: res.ok, status: res.status, data: {} };
            });
        })
        .then(function (result) {
            if (result.status === 422) {
                showErrors((result.data && result.data.errors) || {});
                return;
            }
            if (!result.ok) {
                formError.textContent = (result.data && result.data.message) || 'Something went wrong. Please try again.';
                formError.classList.remove('hidden');
                return;
            }
            var d = result.data || {};
            var reference = (d.data && d.data.reference_number) || d.reference_number || '';
            refEl.textContent = reference || '—';
            trackLink.href = '/track/' + encodeURIComponent(reference);
            wrap.classList.add('hidden');
            successBox.classList.remove('hidden');
            successBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
        })
        .catch(function () {
            formError.textContent = 'Network error. Please check your connection.';
            formError.classList.remove('hidden');
        })
        .finally(function () {
            submitBtn.disabled = false;
            if (btnLabel) btnLabel.textContent = 'Submit request';
        });
    });
})();
</script>
@endpush
