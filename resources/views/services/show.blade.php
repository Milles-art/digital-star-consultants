@extends('layouts.app')
@section('title', data_get($service, 'name', 'Service request').' — Digital Star Consultants')
@section('content')

<section class="relative overflow-hidden bg-ink text-white">
    <div class="hero-mesh absolute inset-0"></div>
    <div class="shell relative py-16 lg:py-24">
        <a class="text-sm font-semibold text-slate-300 hover:text-white" href="{{ route('public.services.index') }}">← Back to services</a>
        <div class="mt-12 max-w-3xl">
            <p class="eyebrow-dark">{{ data_get($service, 'category.name', 'Digital Star service') }}</p>
            <h1 class="section-title mt-5">{{ data_get($service, 'name') }}</h1>
            <p class="mt-6 text-lg text-slate-300">{{ data_get($service, 'description', data_get($service, 'short_description')) }}</p>
        </div>
    </div>
</section>

<section class="shell grid gap-12 py-14 lg:grid-cols-[.7fr_1.3fr] lg:gap-20 lg:py-24">
    <aside class="lg:sticky lg:top-32 lg:self-start">
        <div class="rounded-[22px] border border-line bg-white p-6 shadow-[0_18px_48px_#08244b0c]">
            <p class="eyebrow">Your request</p>
            <div class="mt-6 flex items-center gap-3 text-sm">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-yellow font-bold text-ink">1</span>
                <span class="font-semibold">Details</span>
                <span class="h-px flex-1 bg-line"></span>
                <span class="text-muted">2</span>
                <span class="text-muted">Review</span>
            </div>
            <div class="mt-7 border-t border-line pt-5 text-sm text-muted">
                <p class="flex justify-between gap-4"><span>Service</span><strong class="text-right text-ink">{{ data_get($service, 'name') }}</strong></p>
                <p class="mt-4 flex justify-between gap-4"><span>Typical response</span><strong class="text-right text-ink">Within 2 business days</strong></p>
            </div>
        </div>
        <p class="mt-5 text-xs leading-5 text-muted">Your information is used only to respond to this request. We'll never make you hunt for an update.</p>
    </aside>
    <div>
        <div id="request-form-panel" class="rounded-[26px] border border-line bg-white p-6 shadow-[0_18px_52px_#08244b0b] sm:p-10">
            <div class="border-b border-line pb-8">
                <p class="eyebrow">Step 1 of 2</p>
                <h2 class="mt-4 text-3xl font-bold">Start your request.</h2>
                <p class="mt-3 text-sm text-muted">The essentials help us route your request quickly. Required fields are marked with <span class="text-red-700">*</span>.</p>
            </div>
            <form id="service-request-form" class="mt-8 space-y-7" action="{{ route('public.submissions.store') }}" method="POST" enctype="multipart/form-data" novalidate data-service-id="{{ data_get($service, 'id') }}">
                <input type="hidden" name="service_id" value="{{ data_get($service, 'id') }}">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold" for="customer_name">Full name <span class="text-red-700">*</span></label>
                        <input class="field" id="customer_name" name="customer_name" required type="text" autocomplete="name">
                        <p class="mt-2 hidden text-xs text-red-700" data-error-for="customer_name">Please enter your name.</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold" for="customer_phone">Phone number <span class="text-red-700">*</span></label>
                        <input class="field" id="customer_phone" name="customer_phone" required type="tel" autocomplete="tel">
                        <p class="mt-2 hidden text-xs text-red-700" data-error-for="customer_phone">Please enter a phone number.</p>
                    </div>
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-semibold" for="customer_email">Email address</label>
                        <input class="field" id="customer_email" name="customer_email" type="email" autocomplete="email">
                        <p class="mt-2 hidden text-xs text-red-700" data-error-for="customer_email">Please enter a valid email address.</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-semibold" for="preferred_date">Preferred date</label>
                        <input class="field" id="preferred_date" name="preferred_date" type="date">
                    </div>
                </div>
                @foreach(data_get($service, 'fields', []) as $field)
                    @php
                        $key = data_get($field, 'field_key', data_get($field, 'key'));
                        $type = data_get($field, 'type', 'text');
                        $label = data_get($field, 'label', str($key)->headline());
                        $required = (bool) data_get($field, 'required', false);
                        $options = data_get($field, 'options', []);
                        if (is_string($options)) {
                            $options = json_decode($options, true) ?: array_filter(array_map('trim', explode(',', $options)));
                        }
                    @endphp
                    <div>
                        <label class="mb-2 block text-sm font-semibold" for="field-{{ $key }}">{{ $label }} @if($required)<span class="text-red-700">*</span>@endif</label>
                        @if($type === 'textarea')
                            <textarea class="field min-h-32" id="field-{{ $key }}" name="fields[{{ $key }}]" {{ $required ? 'required' : '' }}></textarea>
                        @elseif(in_array($type, ['select']))
                            <select class="field" id="field-{{ $key }}" name="fields[{{ $key }}]" {{ $required ? 'required' : '' }}>
                                <option value="">Choose an option</option>
                                @foreach($options as $option)
                                    <option value="{{ is_array($option) ? data_get($option, 'value', data_get($option, 'label')) : $option }}">{{ is_array($option) ? data_get($option, 'label', data_get($option, 'value')) : $option }}</option>
                                @endforeach
                            </select>
                        @elseif(in_array($type, ['radio', 'checkbox']))
                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach($options as $option)
                                    @php $value = is_array($option) ? data_get($option, 'value', data_get($option, 'label')) : $option; $optionLabel = is_array($option) ? data_get($option, 'label', $value) : $option; @endphp
                                    <label class="flex items-center gap-3 rounded-xl border border-line px-4 py-3 text-sm transition hover:border-blue">
                                        <input class="h-4 w-4 accent-[#1557a6]" type="{{ $type }}" name="fields[{{ $key }}]{{ $type === 'checkbox' ? '[]' : '' }}" value="{{ $value }}" {{ $required ? 'required' : '' }}>
                                        {{ $optionLabel }}
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <input class="field" id="field-{{ $key }}" name="fields[{{ $key }}]" type="{{ in_array($type, ['file', 'date', 'number', 'email']) ? $type : 'text' }}" {{ $required ? 'required' : '' }} @if($type === 'file') accept="{{ data_get($field, 'accept', '') }}" @endif>
                        @endif
                    </div>
                @endforeach
                <div>
                    <label class="mb-2 block text-sm font-semibold" for="customer_notes">Anything else we should know?</label>
                    <textarea class="field min-h-32" id="customer_notes" name="customer_notes" placeholder="Share context, deadlines, or helpful details."></textarea>
                </div>
                <div class="flex flex-col gap-4 border-t border-line pt-7 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-xs text-muted">By submitting, you agree that we may contact you about this request.</p>
                    <button class="button-primary" id="submit-request" type="submit">Submit request <span aria-hidden="true">↗</span></button>
                </div>
                <div class="hidden rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-900" id="form-error" role="alert"></div>
            </form>
            <div class="hidden" id="request-success" role="status">
                <div class="flex h-14 w-14 items-center justify-center rounded-full bg-yellow text-2xl text-ink">✓</div>
                <p class="eyebrow mt-8">Request received</p>
                <h2 class="mt-4 text-3xl font-bold">You're on your way.</h2>
                <p class="mt-4 max-w-lg text-muted">We've received your request. Keep this reference number for updates:</p>
                <p class="mt-6 rounded-2xl bg-sky px-5 py-4 font-mono text-2xl font-bold tracking-wider text-blue" id="reference-number"></p>
                <a class="button-secondary mt-8" href="{{ url('/track') }}">Track your request <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('service-request-form');
    if (!form) return;
    const panel = document.getElementById('request-form-panel'),
          success = document.getElementById('request-success'),
          errorBox = document.getElementById('form-error'),
          button = document.getElementById('submit-request');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        errorBox.classList.add('hidden');
        let valid = true;
        form.querySelectorAll('[required]').forEach(function(field) {
            const filled = field.type === 'checkbox' || field.type === 'radio'
                ? form.querySelector('[name="' + CSS.escape(field.name) + '"]:checked')
                : field.value.trim();
            if (!filled) {
                valid = false;
                field.setAttribute('aria-invalid', 'true');
                const message = form.querySelector('[data-error-for="' + field.name + '"]');
                if (message) message.classList.remove('hidden');
            } else {
                field.removeAttribute('aria-invalid');
                const message = form.querySelector('[data-error-for="' + field.name + '"]');
                if (message) message.classList.add('hidden');
            }
        });
        if (!valid) {
            errorBox.textContent = 'Please complete the required fields before submitting.';
            errorBox.classList.remove('hidden');
            return;
        }
        button.disabled = true;
        button.innerHTML = 'Sending request…';
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            });
            const payload = await response.json();
            if (!response.ok || payload.status !== 'success')
                throw new Error(payload.message || 'We could not submit your request.');
            document.getElementById('reference-number').textContent = payload.data.reference_number;
            form.classList.add('hidden');
            success.classList.remove('hidden');
            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (exception) {
            errorBox.textContent = exception.message || 'We could not submit your request. Please try again.';
            errorBox.classList.remove('hidden');
            button.disabled = false;
            button.innerHTML = 'Submit request <span aria-hidden="true">↗</span>';
        }
    });
});
</script>
@endpush
@endsection
