@extends('layouts.app')
@section('title', data_get($service, 'name', 'Service request').' — Digital Star Consultants')
@section('content')

@php
    $price = data_get($service, 'formatted_price') ?? (data_get($service, 'price') ? number_format(data_get($service, 'price')) . ' TZS' : null);
    $duration = data_get($service, 'duration');
    $category = data_get($service, 'category');
    $allFields = data_get($service, 'fields', collect())->sortBy('sort_order');

    // Detect government agency
    $serviceName = strtolower(data_get($service, 'name', ''));
    $categoryName = strtolower(data_get($category, 'name', ''));
    $agency = match(true) {
        str_contains($serviceName, 'tin') || str_contains($serviceName, 'tax') || str_contains($serviceName, 'tra') || str_contains($categoryName, 'tra') || str_contains($categoryName, 'tax') => 'tra',
        str_contains($serviceName, 'brela') || str_contains($serviceName, 'business') || str_contains($serviceName, 'company') || str_contains($serviceName, 'ngo') => 'brela',
        str_contains($serviceName, 'nida') || str_contains($serviceName, 'passport') || str_contains($serviceName, 'visa') || str_contains($serviceName, 'immigration') || str_contains($serviceName, 'residence') || str_contains($categoryName, 'immigration') || str_contains($categoryName, 'travel') => 'immigration',
        str_contains($serviceName, 'rita') || str_contains($serviceName, 'birth') || str_contains($serviceName, 'death') || str_contains($serviceName, 'marriage') => 'rita',
        str_contains($serviceName, 'police') || str_contains($serviceName, 'clearance') || str_contains($serviceName, 'conduct') || str_contains($serviceName, 'loss') || str_contains($serviceName, 'driving') || str_contains($serviceName, 'vehicle') => 'police',
        default => 'generic',
    };

    $agencyConfig = match($agency) {
        'tra' => ['name' => 'Tanzania Revenue Authority (TRA)', 'color' => 'blue', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-800', 'accent' => 'text-blue-600', 'icon' => 'M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
        'brela' => ['name' => 'Business Registrations & Licensing Agency (BRELA)', 'color' => 'emerald', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'accent' => 'text-emerald-600', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        'immigration' => ['name' => 'Immigration Department — Tanzania', 'color' => 'amber', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'accent' => 'text-amber-600', 'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        'rita' => ['name' => 'Registration, Insolvency & Trusteeship Agency (RITA)', 'color' => 'violet', 'bg' => 'bg-violet-50', 'border' => 'border-violet-200', 'text' => 'text-violet-800', 'accent' => 'text-violet-600', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        'police' => ['name' => 'Tanzania Police Force', 'color' => 'rose', 'bg' => 'bg-rose-50', 'border' => 'border-rose-200', 'text' => 'text-rose-800', 'accent' => 'text-rose-600', 'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        default => ['name' => data_get($category, 'name', 'Government Service'), 'color' => 'slate', 'bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'text' => 'text-slate-800', 'accent' => 'text-slate-600', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
    };

    // Group fields intelligently
    $groupedFields = [
        'personal' => [],
        'identification' => [],
        'contact' => [],
        'application' => [],
        'documents' => [],
    ];

    foreach ($allFields as $field) {
        $key = data_get($field, 'field_key', '');
        $type = data_get($field, 'field_type', '');

        if (str_starts_with($key, 'upload_')) {
            $groupedFields['documents'][] = $field;
        } elseif (in_array($key, ['full_name', 'date_of_birth', 'gender', 'place_of_birth', 'marital_status', 'nationality', 'occupation', 'home_address', 'current_address', 'residential_address', 'permanent_address'])) {
            $groupedFields['personal'][] = $field;
        } elseif (in_array($key, ['nida_number', 'tin_number', 'passport_number', 'nida_or_passport_number', 'existing_licence_number', 'registration_number', 'document_number'])) {
            $groupedFields['identification'][] = $field;
        } elseif (in_array($key, ['email', 'phone', 'contact_name'])) {
            $groupedFields['contact'][] = $field;
        } else {
            $groupedFields['application'][] = $field;
        }
    }

    $sectionLabels = [
        'personal' => 'Personal Information',
        'identification' => 'Identification Details',
        'contact' => 'Contact Information',
        'application' => 'Application Details',
        'documents' => 'Required Documents',
    ];

    $sectionIcons = [
        'personal' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'identification' => 'M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2',
        'contact' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
        'application' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        'documents' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    ];
@endphp

{{-- ===== SERVICE HERO ===== --}}
<section class="bg-slate-900 py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('public.services.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-amber-400 transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to services
        </a>

        {{-- Agency Badge --}}
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl {{ $agencyConfig['bg'] }} border {{ $agencyConfig['border'] }} mb-5">
            <svg class="w-5 h-5 {{ $agencyConfig['accent'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $agencyConfig['icon'] }}"/></svg>
            <span class="text-sm font-semibold {{ $agencyConfig['text'] }}">{{ $agencyConfig['name'] }}</span>
        </div>

        <div class="flex flex-wrap items-center gap-3 mb-4">
            @if(data_get($service, 'is_active'))
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                    Available now
                </span>
            @endif
        </div>
        <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-white leading-tight">{{ data_get($service, 'name') }}</h1>
        <p class="text-slate-300 mt-4 text-lg max-w-3xl leading-relaxed">{{ data_get($service, 'description', data_get($service, 'short_description', 'Professional service with clear outcomes.')) }}</p>
        <div class="flex flex-wrap items-center gap-6 mt-6 text-sm">
            @if($price)
                <div class="flex items-center gap-2 text-amber-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-semibold">{{ $price }}</span>
                </div>
            @endif
            @if($duration)
                <div class="flex items-center gap-2 text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $duration }}</span>
                </div>
            @endif
            <div class="flex items-center gap-2 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ $allFields->count() }} fields to complete</span>
            </div>
        </div>
    </div>
</section>

{{-- ===== FORM SECTION ===== --}}
<section class="py-12 lg:py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-16">

            {{-- Left: Info & Trust --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- What happens next --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">What happens next</h2>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">1</div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">Fill in your details</h4>
                                <p class="text-sm text-slate-500 mt-0.5">Complete all required fields below. Fields marked with <span class="text-rose-500">*</span> are mandatory.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">2</div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">We process your request</h4>
                                <p class="text-sm text-slate-500 mt-0.5">Our team submits to {{ $agencyConfig['name'] }} and tracks progress.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">3</div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">Track with your reference</h4>
                                <p class="text-sm text-slate-500 mt-0.5">Use your reference number to check status anytime.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Documents checklist preview --}}
                @if(count($groupedFields['documents']))
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Documents you will need
                    </h3>
                    <ul class="space-y-2">
                        @foreach($groupedFields['documents'] as $doc)
                            <li class="flex items-center gap-2 text-sm text-slate-600">
                                <span class="w-5 h-5 rounded border-2 border-slate-200 flex items-center justify-center">
                                    <svg class="w-3 h-3 text-slate-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </span>
                                {{ data_get($doc, 'label') }}
                                @if(data_get($doc, 'is_required'))
                                    <span class="text-xs text-rose-500">required</span>
                                @else
                                    <span class="text-xs text-slate-400">optional</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-xs text-slate-400 mt-3">Accepted formats: PDF, JPG, PNG, DOC. Max 10MB each.</p>
                </div>
                @endif

                {{-- Data protection --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-6">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Your data is protected
                    </h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span> SSL-encrypted submission</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span> Shared only with {{ $agencyConfig['name'] }}</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span> No marketing or third-party sharing</li>
                    </ul>
                </div>
            </div>

            {{-- Right: The Form --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    {{-- Form Header --}}
                    <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-slate-900">Service Application Form</h2>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-slate-100 text-slate-500">{{ $allFields->where('is_required', true)->count() }} required</span>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">Complete all sections below. Required fields are marked with <span class="text-rose-500">*</span></p>
                    </div>

                    <form id="submissionForm" method="POST" action="{{ route('public.submissions.store') }}" enctype="multipart/form-data" class="px-6 py-6 space-y-8">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ data_get($service, 'id') }}">

                        {{-- Base Contact Info (always shown) --}}
                        <div>
                            <h3 class="text-sm font-semibold text-slate-800 mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Your Contact Details
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div class="sm:col-span-2">
                                    <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1.5">
                                        Full name <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                        placeholder="e.g. Juma Abdallah">
                                </div>
                                <div>
                                    <label for="customer_phone" class="block text-sm font-medium text-slate-700 mb-1.5">
                                        Phone number <span class="text-rose-500">*</span>
                                    </label>
                                    <input type="tel" id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                        placeholder="+255 712 345 678">
                                </div>
                                <div>
                                    <label for="customer_email" class="block text-sm font-medium text-slate-700 mb-1.5">
                                        Email address
                                    </label>
                                    <input type="email" id="customer_email" name="customer_email" value="{{ old('customer_email') }}"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                        placeholder="juma@example.com">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="preferred_date" class="block text-sm font-medium text-slate-700 mb-1.5">
                                        Preferred date for follow-up
                                    </label>
                                    <input type="date" id="preferred_date" name="preferred_date" value="{{ old('preferred_date') }}"
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all">
                                </div>
                            </div>
                        </div>

                        {{-- Dynamic Service Fields --}}
                        @foreach($groupedFields as $groupKey => $groupFields)
                            @if(count($groupFields))
                                <div class="border-t border-slate-100 pt-6">
                                    <h3 class="text-sm font-semibold text-slate-800 mb-4 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sectionIcons[$groupKey] }}"/></svg>
                                        {{ $sectionLabels[$groupKey] }}
                                    </h3>
                                    <div class="space-y-5">
                                        @foreach($groupFields as $field)
                                            @php
                                                $key = data_get($field, 'field_key');
                                                $type = data_get($field, 'field_type', 'text');
                                                $label = data_get($field, 'label');
                                                $required = data_get($field, 'is_required', false);
                                                $placeholder = data_get($field, 'placeholder');
                                                $help = data_get($field, 'help_text');
                                                $options = data_get($field, 'options', []);
                                                if (is_string($options)) {
                                                    $options = json_decode($options, true) ?: array_filter(array_map('trim', explode(',', $options)));
                                                }
                                                $oldValue = old("fields.{$key}");
                                            @endphp
                                            <div>
                                                <label for="field_{{ $key }}" class="block text-sm font-medium text-slate-700 mb-1.5">
                                                    {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
                                                </label>

                                                @if($type === 'textarea')
                                                    <textarea id="field_{{ $key }}" name="fields[{{ $key }}]" @if($required) required @endif rows="3"
                                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300 resize-y"
                                                        placeholder="{{ $placeholder }}">{{ $oldValue }}</textarea>

                                                @elseif($type === 'select')
                                                    <select id="field_{{ $key }}" name="fields[{{ $key }}]" @if($required) required @endif
                                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all">
                                                        <option value="">Select {{ strtolower($label) }}</option>
                                                        @foreach($options as $option)
                                                            @php $optValue = is_array($option) ? data_get($option, 'value', data_get($option, 'label')) : $option; @endphp
                                                            <option value="{{ $optValue }}" {{ $oldValue == $optValue ? 'selected' : '' }}>{{ is_array($option) ? data_get($option, 'label', $optValue) : $option }}</option>
                                                        @endforeach
                                                    </select>

                                                @elseif(in_array($type, ['radio', 'checkbox']))
                                                    <div class="space-y-2 mt-2">
                                                        @foreach($options as $option)
                                                            @php
                                                                $optValue = is_array($option) ? data_get($option, 'value', data_get($option, 'label')) : $option;
                                                                $optLabel = is_array($option) ? data_get($option, 'label', $optValue) : $option;
                                                                $isChecked = is_array($oldValue) ? in_array($optValue, $oldValue) : $oldValue == $optValue;
                                                            @endphp
                                                            <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 hover:border-amber-200 hover:bg-amber-50/30 cursor-pointer transition-colors">
                                                                <input type="{{ $type === 'checkbox' ? 'checkbox' : 'radio' }}" name="fields[{{ $key }}]{{ $type === 'checkbox' ? '[]' : '' }}" value="{{ $optValue }}" {{ $isChecked ? 'checked' : '' }} @if($required) required @endif
                                                                    class="w-4 h-4 text-amber-600 border-slate-300 focus:ring-amber-500">
                                                                <span class="text-sm text-slate-700">{{ $optLabel }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>

                                                @elseif($type === 'file')
                                                    <div class="relative">
                                                        <div class="flex items-center gap-3 p-3 rounded-xl border-2 border-dashed border-slate-200 hover:border-amber-300 hover:bg-amber-50/20 transition-colors cursor-pointer"
                                                             onclick="document.getElementById('field_{{ $key }}').click()">
                                                            <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                                                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p id="file-label-{{ $key }}" class="text-sm text-slate-600 truncate">Click to upload or drag and drop</p>
                                                                <p class="text-xs text-slate-400">PDF, JPG, PNG, DOC up to 10MB</p>
                                                            </div>
                                                        </div>
                                                        <input type="file" id="field_{{ $key }}" name="fields[{{ $key }}]" @if($required) required @endif
                                                            class="hidden"
                                                            onchange="document.getElementById('file-label-{{ $key }}').textContent = this.files[0]?.name || 'Click to upload or drag and drop'; this.closest('.relative').querySelector('.border-dashed').classList.add('border-amber-300', 'bg-amber-50/20');">
                                                    </div>

                                                @elseif($type === 'date')
                                                    <input type="date" id="field_{{ $key }}" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif
                                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all">

                                                @elseif($type === 'number')
                                                    <input type="number" id="field_{{ $key }}" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif
                                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                                        placeholder="{{ $placeholder }}">

                                                @elseif($type === 'email')
                                                    <input type="email" id="field_{{ $key }}" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif
                                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                                        placeholder="{{ $placeholder }}">

                                                @elseif($type === 'tel')
                                                    <input type="tel" id="field_{{ $key }}" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif
                                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                                        placeholder="{{ $placeholder ?? '+255 XXX XXX XXX' }}">

                                                @else
                                                    <input type="text" id="field_{{ $key }}" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif
                                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                                        placeholder="{{ $placeholder }}">
                                                @endif

                                                @if($help)
                                                    <p class="text-xs text-slate-400 mt-1.5 flex items-start gap-1">
                                                        <svg class="w-3.5 h-3.5 text-slate-300 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        {{ $help }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        {{-- Notes --}}
                        <div class="border-t border-slate-100 pt-6">
                            <label for="customer_notes" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Additional notes (optional)
                            </label>
                            <textarea id="customer_notes" name="customer_notes" rows="3"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300 resize-y"
                                placeholder="Any extra information that will help us process your request faster...">{{ old('customer_notes') }}</textarea>
                        </div>

                        {{-- Submit --}}
                        <div class="border-t border-slate-100 pt-6">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <p class="text-xs text-slate-400">By submitting, you confirm the information provided is accurate and agree to our terms of service.</p>
                                <button type="submit" id="submitBtn" class="w-full sm:w-auto px-8 py-3 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-sm flex items-center justify-center gap-2">
                                    <span>Start your request</span>
                                    <svg id="btnIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <svg id="btnSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Success Modal --}}
<div id="successModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center fade-in">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Application submitted!</h3>
        <p class="text-sm text-slate-500 mb-4">We have received your application for <strong>{{ data_get($service, 'name') }}</strong> and will begin processing immediately.</p>
        <div class="bg-slate-50 rounded-xl p-4 mb-6">
            <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Your reference number</div>
            <div id="refNumber" class="text-2xl font-mono font-bold text-slate-900 tracking-wider">---</div>
        </div>
        <div class="flex flex-col gap-2">
            <a id="trackLink" href="#" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors">Track your application</a>
            <a href="{{ route('public.services.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Browse more services</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('submissionForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('submitBtn');
    const icon = document.getElementById('btnIcon');
    const spinner = document.getElementById('btnSpinner');
    btn.disabled = true;
    icon.classList.add('hidden');
    spinner.classList.remove('hidden');

    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();

        if (response.ok && data.status === 'success') {
            document.getElementById('refNumber').textContent = data.data.reference_number;
            document.getElementById('trackLink').href = data.data.tracking_url;
            document.getElementById('successModal').classList.remove('hidden');
            document.getElementById('successModal').classList.add('flex');
            this.reset();
        } else {
            alert(data.message || 'Something went wrong. Please try again.');
        }
    } catch (err) {
        alert('Network error. Please check your connection and try again.');
    } finally {
        btn.disabled = false;
        icon.classList.remove('hidden');
        spinner.classList.add('hidden');
    }
});
</script>
@endpush

@endsection
