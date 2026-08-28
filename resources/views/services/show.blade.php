@extends('layouts.app', [
    'title' => $service->name,
    'metaDescription' => Str::limit($service->description, 160)
])

@section('content')
@php
    $locale = app()->getLocale();
    $isSw = $locale === 'sw';
@endphp

{{-- ========================================================================= --}}
{{-- SERVICE DETAIL HEADER                                                     --}}
{{-- ========================================================================= --}}
<section class="border-b border-line bg-gradient-to-b from-[#F2F6FB] via-[#F8FAFD] to-white py-12 lg:py-16">
    <div class="shell">
        <div class="flex items-center gap-2 text-xs font-bold text-muted mb-5">
            <a href="{{ route('public.services.index') }}" class="hover:text-blue transition-colors">{{ __('site.nav.services') }}</a>
            <span>/</span>
            <span class="text-navy font-bold">{{ $service->category->name ?? 'General' }}</span>
        </div>

        <div class="grid gap-10 lg:grid-cols-[1.3fr_0.7fr] lg:items-start">
            <div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-ink tracking-tight">
                    {{ $service->name }}
                </h1>
                <p class="mt-4 text-sm sm:text-base text-muted leading-relaxed max-w-2xl">
                    {{ $service->description }}
                </p>
            </div>

            {{-- Summary Card --}}
            <div class="rounded-3xl border border-line bg-white p-7 shadow-xs space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase tracking-wider text-muted">{{ $isSw ? 'Gharama' : 'Pricing' }}</span>
                    <span class="text-base font-black text-navy">{{ $service->formatted_price ?? 'Contact for scope' }}</span>
                </div>
                @if ($service->duration)
                    <div class="flex items-center justify-between pt-3 border-t border-line">
                        <span class="text-xs font-black uppercase tracking-wider text-muted">{{ $isSw ? 'Muda wa Kazi' : 'Turnaround Time' }}</span>
                        <span class="text-xs font-bold text-ink">{{ $service->duration }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between pt-3 border-t border-line">
                    <span class="text-xs font-black uppercase tracking-wider text-muted">{{ $isSw ? 'Ufuatiliaji' : 'Tracking' }}</span>
                    <span class="text-xs font-bold text-blue flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-yellow"></span>
                        <span>Instant Reference Code</span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========================================================================= --}}
{{-- SERVICE REQUEST INTAKE FORM (DYNAMIC SCHEMAS)                             --}}
{{-- ========================================================================= --}}
<section class="py-16 sm:py-20 bg-canvas">
    <div class="shell max-w-3xl">
        
        {{-- Submission Feedback Banner --}}
        @if (session('success'))
            <div class="rounded-3xl bg-emerald-50 border border-emerald-200 p-7 mb-8 text-emerald-900 shadow-xs">
                <h2 class="text-base font-black">{{ $isSw ? 'Ombi Limewasilishwa Kikamilifu!' : 'Request Submitted Successfully!' }}</h2>
                <p class="mt-1.5 text-xs text-emerald-800">{{ session('success') }}</p>
            </div>
        @endif

        @if (isset($errors) && $errors->any())
            <div class="rounded-3xl bg-red-50 border border-red-200 p-7 mb-8 text-red-900 shadow-xs">
                <h2 class="text-sm font-black">{{ $isSw ? 'Tafadhali kagua makosa yafuatayo:' : 'Please review the following errors:' }}</h2>
                <ul class="mt-2 list-disc list-inside text-xs space-y-1 text-red-800">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- AJAX Success Modal / Box --}}
        <div id="ajax-success-container" class="hidden rounded-3xl bg-emerald-50 border border-emerald-300 p-8 text-center mb-8 shadow-md">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-600 text-white mx-auto mb-4 font-black">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-2xl font-black text-emerald-950">{{ $isSw ? 'Ombi Lako Limewasilishwa!' : 'Submission Successful!' }}</h2>
            <p class="mt-2 text-xs sm:text-sm text-emerald-900 leading-relaxed max-w-md mx-auto">
                {{ $isSw ? 'Namba yako ya kumbukumbu imeandaliwa. Tumia namba hii kufuatilia maendeleo ya ombi lako wakati wowote.' : 'Your unique reference code has been generated. Use this code anytime to track progress in real time without needing an account.' }}
            </p>
            <div class="mt-6 inline-flex items-center gap-3 px-6 py-3 rounded-2xl bg-white border border-emerald-300 text-sm font-black text-navy shadow-xs">
                <span class="text-muted text-xs uppercase tracking-wider font-bold">Reference:</span>
                <span id="ajax-ref-code" class="text-blue text-base font-mono select-all"></span>
            </div>
            <div class="mt-6 flex items-center justify-center gap-3">
                <a id="ajax-track-link" href="#" class="button-primary !py-3 !px-6 !text-xs font-black">
                    <span>{{ $isSw ? 'Fuatilia Ombi Sasa' : 'Track Request Status' }}</span>
                </a>
            </div>
        </div>

        {{-- Main Form Card --}}
        <div id="service-form-card" class="rounded-3xl border border-line bg-white p-8 sm:p-12 shadow-xs">
            <div class="border-b border-line pb-6 mb-8">
                <h2 class="text-xl font-black text-ink">
                    {{ $isSw ? 'Wasilisha Taarifa za Ombi' : 'Submit Service Request' }}
                </h2>
                <p class="mt-1 text-xs text-muted">
                    {{ $isSw ? 'Jaza fomu hapa chini. Utapata namba ya kumbukumbu papo hapo.' : 'Fill in your details below. You will immediately receive a reference code for tracking.' }}
                </p>
            </div>

            <form action="{{ route('public.submissions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="service_id" value="{{ $service->id }}">

                {{-- Primary Contact Details --}}
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="customer_name" class="block text-xs font-bold uppercase tracking-wider text-navy mb-2">
                            {{ $isSw ? 'Jina Kamili' : 'Full Name' }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required
                               class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-xs font-bold uppercase tracking-wider text-navy mb-2">
                            {{ $isSw ? 'Namba ya Simu' : 'Phone Number' }} <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required placeholder="07XX XXX XXX"
                               class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">
                    </div>

                    <div>
                        <label for="customer_email" class="block text-xs font-bold uppercase tracking-wider text-navy mb-2">
                            {{ $isSw ? 'Barua Pepe' : 'Email Address' }}
                        </label>
                        <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}" placeholder="name@example.com"
                               class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">
                    </div>

                    <div>
                        <label for="preferred_date" class="block text-xs font-bold uppercase tracking-wider text-navy mb-2">
                            {{ $isSw ? 'Tarehe Unayopendelea' : 'Target / Preferred Date' }}
                        </label>
                        <input type="date" id="preferred_date" name="preferred_date" value="{{ old('preferred_date') }}"
                               class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">
                    </div>
                </div>

                {{-- Dynamic Service Custom Fields --}}
                @if ($service->fields->isNotEmpty())
                    <div class="pt-8 border-t border-line space-y-6">
                        <p class="text-xs font-black uppercase tracking-wider text-navy">{{ $isSw ? 'Mahitaji Maalum ya Huduma Hii' : 'Service Requirements & Documents' }}</p>

                        @foreach ($service->fields as $field)
                            @php
                                $fieldKey = $field->field_key;
                                $oldVal = old("fields.{$fieldKey}", $field->default_value);
                            @endphp

                            <div>
                                <label for="field_{{ $fieldKey }}" class="block text-xs font-bold text-ink mb-2">
                                    {{ $field->label }}
                                    @if ($field->is_required)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>

                                @if ($field->field_type === 'textarea')
                                    <textarea id="field_{{ $fieldKey }}" name="fields[{{ $fieldKey }}]" rows="3"
                                              placeholder="{{ $field->placeholder }}"
                                              {{ $field->is_required ? 'required' : '' }}
                                              class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">{{ $oldVal }}</textarea>

                                @elseif ($field->field_type === 'select')
                                    <select id="field_{{ $fieldKey }}" name="fields[{{ $fieldKey }}]"
                                            {{ $field->is_required ? 'required' : '' }}
                                            class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">
                                        <option value="">{{ $field->placeholder ?? ($isSw ? 'Chagua chaguo...' : 'Select an option...') }}</option>
                                        @foreach ($field->options ?? [] as $opt)
                                            <option value="{{ $opt }}" {{ $oldVal === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>

                                @elseif ($field->field_type === 'file')
                                    <input type="file" id="field_{{ $fieldKey }}" name="fields[{{ $fieldKey }}]"
                                           {{ $field->is_required ? 'required' : '' }}
                                           class="w-full rounded-2xl border border-line bg-surface px-4 py-2.5 text-xs font-semibold text-ink file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-navy file:text-yellow hover:file:bg-navy-dark focus:outline-none">

                                @elseif ($field->field_type === 'checkbox')
                                    <div class="flex items-center gap-2.5 mt-1">
                                        <input type="checkbox" id="field_{{ $fieldKey }}" name="fields[{{ $fieldKey }}]" value="1" {{ $oldVal ? 'checked' : '' }}
                                               class="h-4 w-4 rounded border-line text-blue focus:ring-blue">
                                        <label for="field_{{ $fieldKey }}" class="text-xs text-muted font-medium">{{ $field->placeholder ?? $field->label }}</label>
                                    </div>

                                @else
                                    <input type="{{ $field->field_type === 'number' ? 'number' : ($field->field_type === 'date' ? 'date' : 'text') }}"
                                           id="field_{{ $fieldKey }}" name="fields[{{ $fieldKey }}]" value="{{ $oldVal }}"
                                           placeholder="{{ $field->placeholder }}"
                                           {{ $field->is_required ? 'required' : '' }}
                                           class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">
                                @endif

                                @if ($field->help_text)
                                    <p class="mt-1.5 text-[11px] text-muted">{{ $field->help_text }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Additional Notes --}}
                <div class="pt-6 border-t border-line">
                    <label for="customer_notes" class="block text-xs font-bold uppercase tracking-wider text-navy mb-2">
                        {{ $isSw ? 'Maelezo ya Ziada / Maombi Maalum' : 'Additional Notes / Project Specifications' }}
                    </label>
                    <textarea id="customer_notes" name="customer_notes" rows="3" placeholder="{{ $isSw ? 'Eleza maelezo yoyote ya ziada kuhusu ombi hili...' : 'Provide any additional context, specifications, or questions...' }}"
                              class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">{{ old('customer_notes') }}</textarea>
                </div>

                {{-- Submit Action --}}
                <div class="pt-8 border-t border-line flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                    <button type="submit" class="button-primary !py-4 !px-8 !text-xs font-black justify-center">
                        <span>{{ $isSw ? 'Wasilisha Ombi & Pata Namba' : 'Submit Request & Get Reference' }}</span>
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    </button>

                    <span class="text-[11px] text-muted flex items-center gap-1.5">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Encrypted & Reference Tracked</span>
                    </span>
                </div>

            </form>
        </div>

    </div>
</section>
@endsection
