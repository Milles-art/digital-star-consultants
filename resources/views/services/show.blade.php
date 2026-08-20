@extends('layouts.app')

@section('title', $service->name.' — Digital Star Consultants')
@section('meta_description', $service->description ?? 'Submit a request for '.$service->name)

@section('content')
    <section class="border-b border-ink-100 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <nav class="mb-4 text-sm text-ink-500" aria-label="Breadcrumb">
                <a href="{{ route('public.services.index') }}" class="hover:text-brand-700">Services</a>
                <span class="mx-1.5">/</span>
                @if ($service->category)
                    <a href="{{ route('public.services.index', ['category' => $service->category->slug]) }}" class="hover:text-brand-700">
                        {{ $service->category->name }}
                    </a>
                    <span class="mx-1.5">/</span>
                @endif
                <span class="text-ink-800">{{ $service->name }}</span>
            </nav>

            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="max-w-2xl">
                    <h1 class="font-display text-3xl font-bold text-ink-900 sm:text-4xl">{{ $service->name }}</h1>
                    @if ($service->description)
                        <p class="mt-3 text-ink-600 leading-relaxed">{{ $service->description }}</p>
                    @endif
                </div>
                <div class="rounded-2xl border border-ink-200 bg-ink-50 px-5 py-4 text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-ink-500">Price</p>
                    <p class="font-display text-xl font-bold text-ink-900">
                        {{ $service->formatted_price ?? ($service->price ? 'TSh '.number_format($service->price, 0) : 'Free') }}
                    </p>
                    @if ($service->duration ?? null)
                        <p class="mt-1 text-xs text-ink-500">{{ $service->duration }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-5">
            {{-- Form --}}
            <div class="lg:col-span-3">
                <div class="surface-card p-6 sm:p-8" style="transform:none;box-shadow:0 1px 2px rgba(15,23,42,.04)">
                    <h2 class="font-display text-xl font-bold text-ink-900">Start your request</h2>
                    <p class="mt-1 text-sm text-ink-600">Fill in your details. You’ll get a reference number to track status.</p>

                    <div id="form-alert" class="mt-4 hidden rounded-xl px-4 py-3 text-sm" role="status"></div>

                    <form id="submission-form" class="mt-6 space-y-5" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="form-label" for="customer_name">Full name *</label>
                                <input type="text" name="customer_name" id="customer_name" required maxlength="255" class="form-input">
                                <p class="form-error hidden" data-error="customer_name"></p>
                            </div>
                            <div>
                                <label class="form-label" for="customer_phone">Phone *</label>
                                <input type="text" name="customer_phone" id="customer_phone" required maxlength="20" class="form-input" placeholder="+255...">
                                <p class="form-error hidden" data-error="customer_phone"></p>
                            </div>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="form-label" for="customer_email">Email</label>
                                <input type="email" name="customer_email" id="customer_email" maxlength="255" class="form-input">
                                <p class="form-error hidden" data-error="customer_email"></p>
                            </div>
                            <div>
                                <label class="form-label" for="preferred_date">Preferred date</label>
                                <input type="date" name="preferred_date" id="preferred_date" class="form-input" min="{{ date('Y-m-d') }}">
                                <p class="form-error hidden" data-error="preferred_date"></p>
                            </div>
                        </div>

                        <div>
                            <label class="form-label" for="customer_notes">Notes</label>
                            <textarea name="customer_notes" id="customer_notes" rows="3" maxlength="2000" class="form-input" placeholder="Anything we should know…"></textarea>
                            <p class="form-error hidden" data-error="customer_notes"></p>
                        </div>

                        {{-- Dynamic fields from service --}}
                        @if ($service->fields->isNotEmpty())
                            <div class="border-t border-ink-100 pt-5">
                                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wider text-ink-500">Additional details</h3>
                                <div class="space-y-4">
                                    @foreach ($service->fields as $field)
                                        <div>
                                            <label class="form-label" for="field_{{ $field->field_key }}">
                                                {{ $field->label }}
                                                @if ($field->is_required)<span class="text-red-500">*</span>@endif
                                            </label>

                                            @switch($field->field_type)
                                                @case('textarea')
                                                    <textarea
                                                        name="fields[{{ $field->field_key }}]"
                                                        id="field_{{ $field->field_key }}"
                                                        rows="3"
                                                        class="form-input"
                                                        @if($field->is_required) required @endif
                                                        placeholder="{{ $field->placeholder ?? '' }}"
                                                    ></textarea>
                                                    @break

                                                @case('select')
                                                    <select
                                                        name="fields[{{ $field->field_key }}]"
                                                        id="field_{{ $field->field_key }}"
                                                        class="form-input"
                                                        @if($field->is_required) required @endif
                                                    >
                                                        <option value="">Select…</option>
                                                        @foreach (($field->options ?? []) as $optKey => $optLabel)
                                                            <option value="{{ is_int($optKey) ? $optLabel : $optKey }}">
                                                                {{ is_array($optLabel) ? ($optLabel['label'] ?? $optKey) : $optLabel }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @break

                                                @case('file')
                                                    <input
                                                        type="file"
                                                        name="fields[{{ $field->field_key }}]"
                                                        id="field_{{ $field->field_key }}"
                                                        class="form-input"
                                                        @if($field->is_required) required @endif
                                                        accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.webp"
                                                    >
                                                    @break

                                                @case('number')
                                                    <input
                                                        type="number"
                                                        name="fields[{{ $field->field_key }}]"
                                                        id="field_{{ $field->field_key }}"
                                                        class="form-input"
                                                        @if($field->is_required) required @endif
                                                        placeholder="{{ $field->placeholder ?? '' }}"
                                                    >
                                                    @break

                                                @case('email')
                                                    <input
                                                        type="email"
                                                        name="fields[{{ $field->field_key }}]"
                                                        id="field_{{ $field->field_key }}"
                                                        class="form-input"
                                                        @if($field->is_required) required @endif
                                                        placeholder="{{ $field->placeholder ?? '' }}"
                                                    >
                                                    @break

                                                @case('date')
                                                    <input
                                                        type="date"
                                                        name="fields[{{ $field->field_key }}]"
                                                        id="field_{{ $field->field_key }}"
                                                        class="form-input"
                                                        @if($field->is_required) required @endif
                                                    >
                                                    @break

                                                @default
                                                    <input
                                                        type="text"
                                                        name="fields[{{ $field->field_key }}]"
                                                        id="field_{{ $field->field_key }}"
                                                        class="form-input"
                                                        @if($field->is_required) required @endif
                                                        placeholder="{{ $field->placeholder ?? '' }}"
                                                    >
                                            @endswitch

                                            <p class="form-error hidden" data-error="fields.{{ $field->field_key }}"></p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <button type="submit" id="submit-btn" class="btn-primary w-full sm:w-auto">
                            Submit request
                        </button>
                    </form>

                    {{-- Success state --}}
                    <div id="success-panel" class="mt-6 hidden rounded-2xl border border-green-200 bg-green-50 p-6">
                        <h3 class="font-display text-lg font-bold text-green-900">Request submitted</h3>
                        <p class="mt-1 text-sm text-green-800">Save your reference number to track status.</p>
                        <p class="mt-4 font-display text-2xl font-bold tracking-wide text-green-900" id="ref-number"></p>
                        <a href="#" id="track-link" class="btn-primary mt-4 inline-flex text-sm">Track this request</a>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <aside class="lg:col-span-2">
                <div class="sticky top-24 space-y-4">
                    <div class="rounded-2xl border border-ink-200 bg-white p-5">
                        <h3 class="font-display text-base font-semibold text-ink-900">What happens next?</h3>
                        <ol class="mt-3 space-y-3 text-sm text-ink-600">
                            <li class="flex gap-3"><span class="font-bold text-brand-600">1</span> We receive your request and assign a reference number.</li>
                            <li class="flex gap-3"><span class="font-bold text-brand-600">2</span> Our team reviews the details.</li>
                            <li class="flex gap-3"><span class="font-bold text-brand-600">3</span> We contact you if anything else is needed.</li>
                            <li class="flex gap-3"><span class="font-bold text-brand-600">4</span> You track progress anytime with your reference.</li>
                        </ol>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.getElementById('submission-form')?.addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const btn = document.getElementById('submit-btn');
    const alertBox = document.getElementById('form-alert');
    const successPanel = document.getElementById('success-panel');

    // Clear previous errors
    form.querySelectorAll('[data-error]').forEach(el => {
        el.classList.add('hidden');
        el.textContent = '';
    });
    alertBox.classList.add('hidden');

    btn.disabled = true;
    btn.textContent = 'Submitting…';

    try {
        const formData = new FormData(form);
        const res = await fetch('{{ route('public.submissions.store') }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            if (res.status === 422 && data.errors) {
                Object.entries(data.errors).forEach(([key, messages]) => {
                    const el = form.querySelector(`[data-error="${key}"]`);
                    if (el) {
                        el.textContent = Array.isArray(messages) ? messages[0] : messages;
                        el.classList.remove('hidden');
                    }
                });
                alertBox.textContent = 'Please fix the highlighted fields.';
                alertBox.className = 'mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800';
                alertBox.classList.remove('hidden');
            } else {
                alertBox.textContent = data.message || 'Something went wrong. Please try again.';
                alertBox.className = 'mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800';
                alertBox.classList.remove('hidden');
            }
            return;
        }

        // Success
        form.classList.add('hidden');
        successPanel.classList.remove('hidden');
        const ref = data.data?.reference_number || '';
        document.getElementById('ref-number').textContent = ref;
        document.getElementById('track-link').href = '/track/' + encodeURIComponent(ref);

    } catch (err) {
        alertBox.textContent = 'Network error. Please check your connection and try again.';
        alertBox.className = 'mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800';
        alertBox.classList.remove('hidden');
    } finally {
        btn.disabled = false;
        btn.textContent = 'Submit request';
    }
});
</script>
@endpush
