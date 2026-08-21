@extends('layouts.app')
@section('title', data_get($service, 'name', 'Service request').' — Digital Star Consultants')
@section('content')

@php
    $price = data_get($service, 'formatted_price') ?? (data_get($service, 'price') ? number_format(data_get($service, 'price')) . ' TZS' : null);
    $duration = data_get($service, 'duration');
    $category = data_get($service, 'category');
    $fields = data_get($service, 'fields', collect());
@endphp

{{-- ===== SERVICE HERO ===== --}}
<section class="bg-slate-900 py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('public.services.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-amber-400 transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to services
        </a>
        <div class="flex flex-wrap items-center gap-3 mb-4">
            @if($category)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    {{ data_get($category, 'name', 'Service') }}
                </span>
            @endif
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
        </div>
    </div>
</section>

{{-- ===== FORM SECTION ===== --}}
<section class="py-12 lg:py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 lg:gap-16">

            {{-- Left: Info & Trust --}}
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 mb-3">What happens next</h2>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">1</div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">Submit your request</h4>
                                <p class="text-sm text-slate-500 mt-0.5">Fill in the details below. No account needed.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">2</div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">We review & respond</h4>
                                <p class="text-sm text-slate-500 mt-0.5">Our team reviews within 48 hours on average.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center text-sm font-bold shrink-0">3</div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800">Track your progress</h4>
                                <p class="text-sm text-slate-500 mt-0.5">Use your reference number to check status anytime.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 p-5">
                    <h3 class="text-sm font-semibold text-slate-800 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Your data is protected
                    </h3>
                    <ul class="space-y-2 text-sm text-slate-500">
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span> SSL-encrypted submission</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span> Used only for your request</li>
                        <li class="flex items-center gap-2"><span class="w-1 h-1 rounded-full bg-slate-300"></span> No marketing emails</li>
                    </ul>
                </div>
            </div>

            {{-- Right: The Form --}}
            <div class="lg:col-span-3">
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
                    {{-- Form Header --}}
                    <div class="px-6 py-5 border-b border-slate-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-bold text-slate-900">Start your request</h2>
                            <span class="text-xs text-slate-400">Step 1 of 2</span>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">Required fields are marked with <span class="text-rose-500">*</span></p>
                    </div>

                    <form id="submissionForm" method="POST" action="{{ route('public.submissions.store') }}" enctype="multipart/form-data" class="px-6 py-6 space-y-6">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ data_get($service, 'id') }}">

                        {{-- Contact Info --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div class="sm:col-span-2">
                                <label for="customer_name" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Full name <span class="text-rose-500">*</span>
                                </label>
                                <input type="text" id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                    placeholder="e.g. Sarah Johnson">
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
                                    placeholder="sarah@example.com">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="preferred_date" class="block text-sm font-medium text-slate-700 mb-1.5">
                                    Preferred date
                                </label>
                                <input type="date" id="preferred_date" name="preferred_date" value="{{ old('preferred_date') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all">
                            </div>
                        </div>

                        {{-- Dynamic Service Fields --}}
                        @if($fields && $fields->count())
                            <div class="border-t border-slate-100 pt-6">
                                <h3 class="text-sm font-semibold text-slate-800 mb-4">Service details</h3>
                                <div class="space-y-5">
                                    @foreach($fields as $field)
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
                                                    <option value="">Select an option</option>
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
                                                    <input type="file" id="field_{{ $key }}" name="fields[{{ $key }}]" @if($required) required @endif
                                                        class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-colors cursor-pointer"
                                                        onchange="document.getElementById('file-label-{{ $key }}').textContent = this.files[0]?.name || 'Choose a file...'">
                                                    <p id="file-label-{{ $key }}" class="text-xs text-slate-400 mt-1.5">PDF, JPG, PNG, DOC up to 10MB</p>
                                                </div>

                                            @else
                                                <input type="{{ $type }}" id="field_{{ $key }}" name="fields[{{ $key }}]" value="{{ $oldValue }}" @if($required) required @endif
                                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300"
                                                    placeholder="{{ $placeholder }}">
                                            @endif

                                            @if($help)
                                                <p class="text-xs text-slate-400 mt-1.5">{{ $help }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Notes --}}
                        <div class="border-t border-slate-100 pt-6">
                            <label for="customer_notes" class="block text-sm font-medium text-slate-700 mb-1.5">
                                Anything else we should know?
                            </label>
                            <textarea id="customer_notes" name="customer_notes" rows="3"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all placeholder:text-slate-300 resize-y"
                                placeholder="Add any details that will help us understand your request...">{{ old('customer_notes') }}</textarea>
                        </div>

                        {{-- Submit --}}
                        <div class="border-t border-slate-100 pt-6">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <p class="text-xs text-slate-400">By submitting, you agree that we may contact you about this request.</p>
                                <button type="submit" id="submitBtn" class="w-full sm:w-auto px-8 py-3 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-sm flex items-center justify-center gap-2">
                                    <span>Submit request</span>
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

{{-- Success Modal (hidden by default, shown via JS) --}}
<div id="successModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center fade-in">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-2">Request submitted!</h3>
        <p class="text-sm text-slate-500 mb-4">We have received your request and will be in touch soon.</p>
        <div class="bg-slate-50 rounded-xl p-4 mb-6">
            <div class="text-xs text-slate-400 uppercase tracking-wide mb-1">Your reference number</div>
            <div id="refNumber" class="text-2xl font-mono font-bold text-slate-900 tracking-wider">---</div>
        </div>
        <div class="flex flex-col gap-2">
            <a id="trackLink" href="#" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors">Track your request</a>
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
