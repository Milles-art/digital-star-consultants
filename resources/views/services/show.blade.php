@extends('layouts.app', ['title' => $service->name])

@section('content')
<section class="border-b border-line bg-gradient-to-b from-sky/40 to-paper">
    <div class="shell py-10 lg:py-14">
        <nav class="text-sm font-semibold text-muted" aria-label="Breadcrumb">
            <a href="{{ route('public.services.index') }}" class="hover:text-ink">{{ __('site.nav.services') }}</a>
            <span class="mx-2 text-line">/</span>
            @if ($service->category)
                <a href="{{ route('public.services.index', ['category' => $service->category->slug]) }}" class="hover:text-ink">{{ $service->category->name }}</a>
                <span class="mx-2 text-line">/</span>
            @endif
            <span class="text-ink">{{ $service->name }}</span>
        </nav>
        <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_300px] lg:items-start">
            <div class="reveal">
                <p class="eyebrow">{{ $service->category->name ?? __('site.nav.services') }}</p>
                <h1 class="section-title mt-2 text-ink">{{ $service->name }}</h1>
                @if ($service->description)
                    <p class="mt-4 max-w-2xl text-muted">{{ $service->description }}</p>
                @endif
            </div>
            <div class="reveal rounded-3xl border border-line bg-white p-5 shadow-sm">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="font-bold text-muted">{{ __('site.services.price') }}</dt>
                        <dd class="font-black text-ink">{{ $service->formatted_price }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-bold text-muted">{{ __('site.services.duration') }}</dt>
                        <dd class="font-black text-ink">{{ $service->duration }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="font-bold text-muted">{{ __('site.services.fields') }}</dt>
                        <dd class="font-black text-ink">{{ $service->fields->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</section>

<section class="shell py-12 lg:py-16">
    <div class="mx-auto max-w-2xl">
        <div class="reveal rounded-3xl border border-line bg-white p-6 shadow-sm sm:p-8">
            <h2 class="text-xl font-black text-ink">{{ __('site.services.submit_title') }}</h2>
            <p class="mt-2 text-sm text-muted">{{ __('site.services.submit_lead') }}</p>

            <form id="submission-form" class="mt-8 space-y-5" method="POST"
                  action="{{ route('public.submissions.store') }}" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">

                <fieldset class="space-y-4">
                    <legend class="text-xs font-bold uppercase tracking-[0.16em] text-blue">{{ __('site.services.your_details') }}</legend>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="customer_name">
                            {{ __('site.services.full_name') }} <span class="text-red-600">*</span>
                        </label>
                        <input id="customer_name" name="customer_name" type="text" required maxlength="255" class="field-input">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="customer_phone">
                                {{ __('site.services.phone') }} <span class="text-red-600">*</span>
                            </label>
                            <input id="customer_phone" name="customer_phone" type="tel" required maxlength="20" class="field-input" placeholder="+255 …">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="customer_email">
                                {{ __('site.services.email') }} <span class="text-muted">({{ __('site.services.optional') }})</span>
                            </label>
                            <input id="customer_email" name="customer_email" type="email" maxlength="255" class="field-input">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="preferred_date">{{ __('site.services.preferred_date') }}</label>
                        <input id="preferred_date" name="preferred_date" type="date" class="field-input">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="customer_notes">{{ __('site.services.notes') }}</label>
                        <textarea id="customer_notes" name="customer_notes" rows="3" maxlength="2000" class="field-input" placeholder="{{ __('site.services.notes_ph') }}"></textarea>
                    </div>
                </fieldset>

                @if ($service->fields->isNotEmpty())
                    <fieldset class="space-y-4 border-t border-line pt-6">
                        <legend class="text-xs font-bold uppercase tracking-[0.16em] text-blue">{{ __('site.services.service_details') }}</legend>
                        @foreach ($service->fields as $field)
                            @php
                                $inputId = 'field_'.$field->field_key;
                                $name = 'fields['.$field->field_key.']';
                                $required = $field->is_required;
                            @endphp
                            <div>
                                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="{{ $inputId }}">
                                    {{ $field->label }}
                                    @if ($required)<span class="text-red-600">*</span>@endif
                                </label>
                                @switch ($field->field_type)
                                    @case('textarea')
                                        <textarea id="{{ $inputId }}" name="{{ $name }}" rows="4" @required($required) class="field-input" placeholder="{{ $field->placeholder }}">{{ $field->default_value }}</textarea>
                                        @break
                                    @case('select')
                                        <select id="{{ $inputId }}" name="{{ $name }}" @required($required) class="field-input">
                                            <option value="">{{ __('site.services.select') }}</option>
                                            @foreach ($field->getOptionsArray() as $optKey => $optLabel)
                                                <option value="{{ $optKey }}" @selected($field->default_value == $optKey)>{{ $optLabel }}</option>
                                            @endforeach
                                        </select>
                                        @break
                                    @case('radio')
                                        <div class="space-y-2">
                                            @foreach ($field->getOptionsArray() as $optKey => $optLabel)
                                                <label class="flex items-center gap-2 text-sm font-semibold">
                                                    <input type="radio" name="{{ $name }}" value="{{ $optKey }}" @checked($field->default_value == $optKey) @required($required) class="h-4 w-4 accent-blue">
                                                    {{ $optLabel }}
                                                </label>
                                            @endforeach
                                        </div>
                                        @break
                                    @case('checkbox')
                                        <div class="space-y-2">
                                            @foreach ($field->getOptionsArray() as $optKey => $optLabel)
                                                <label class="flex items-center gap-2 text-sm font-semibold">
                                                    <input type="checkbox" name="{{ $name }}[]" value="{{ $optKey }}" class="h-4 w-4 rounded accent-blue">
                                                    {{ $optLabel }}
                                                </label>
                                            @endforeach
                                        </div>
                                        @break
                                    @case('file')
                                        <input id="{{ $inputId }}" name="{{ $name }}" type="file" @required($required)
                                            accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                            class="block w-full text-sm text-muted file:mr-4 file:rounded-full file:border-0 file:bg-sky file:px-4 file:py-2 file:text-sm file:font-bold file:text-blue">
                                        <p class="mt-1 text-xs text-muted">{{ __('site.services.file_hint') }}</p>
                                        @break
                                    @case('hidden')
                                        <input type="hidden" name="{{ $name }}" value="{{ $field->default_value }}">
                                        @break
                                    @default
                                        <input id="{{ $inputId }}" name="{{ $name }}"
                                            type="{{ in_array($field->field_type, ['email','tel','number','date','time','password'], true) ? $field->field_type : 'text' }}"
                                            value="{{ $field->default_value }}" @required($required) class="field-input" placeholder="{{ $field->placeholder }}">
                                @endswitch
                                @if ($field->help_text)
                                    <p class="mt-1 text-xs text-muted">{{ $field->help_text }}</p>
                                @endif
                            </div>
                        @endforeach
                    </fieldset>
                @endif

                <div id="submission-errors" class="hidden rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800"></div>
                <div id="submission-success" class="hidden rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm"></div>

                <button type="submit" class="button-primary w-full sm:w-auto" data-submit-btn>{{ __('site.services.submit_btn') }}</button>
            </form>
        </div>
    </div>
</section>

<style>
.field-input { width:100%; border-radius:1rem; border:1px solid #dbe4ef; background:#f7f9fc; padding:.75rem 1rem; font-size:.875rem; font-weight:600; color:#081b35; outline:none; }
.field-input:focus { border-color:#1557a6; box-shadow:0 0 0 4px rgba(21,87,166,.1); }
</style>

@push('scripts')
<script>
document.getElementById('submission-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('[data-submit-btn]');
    const errBox = document.getElementById('submission-errors');
    const okBox = document.getElementById('submission-success');
    const original = btn.textContent;
    errBox.classList.add('hidden'); okBox.classList.add('hidden');
    errBox.innerHTML = ''; okBox.innerHTML = '';
    btn.disabled = true; btn.textContent = @json(__('site.services.submitting'));
    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: new FormData(form),
        });
        const json = await res.json().catch(() => ({}));
        if (!res.ok) {
            const messages = [];
            if (json.message) messages.push(json.message);
            if (json.errors) Object.values(json.errors).flat().forEach(m => messages.push(m));
            errBox.innerHTML = messages.map(m => `<p>${m}</p>`).join('') || `<p>${@json(__('site.common.error'))}</p>`;
            errBox.classList.remove('hidden');
            return;
        }
        const d = json.data || {};
        okBox.innerHTML = `
            <p class="font-black text-emerald-900 text-lg">${json.message || @json(__('site.services.success'))}</p>
            <p class="mt-3 text-2xl font-black tracking-wide text-ink">${d.reference_number || '—'}</p>
            <p class="mt-2 text-emerald-800">${@json(__('site.services.keep_ref'))}</p>
            ${d.reference_number ? `<p class="mt-3"><a class="font-bold text-blue underline" href="/track/status/${encodeURIComponent(d.reference_number)}">${@json(__('site.services.open_track'))}</a></p>` : ''}
        `;
        okBox.classList.remove('hidden');
        form.reset();
        okBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } catch {
        errBox.innerHTML = `<p>${@json(__('site.common.error'))}</p>`;
        errBox.classList.remove('hidden');
    } finally {
        btn.disabled = false; btn.textContent = original;
    }
});
</script>
@endpush
@endsection
