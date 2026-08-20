@extends('layouts.app')

@section('title', $service->name.' | Digital Star Consultants')

@section('content')
<section class="mx-auto max-w-6xl px-5 py-12 sm:px-8 lg:py-16">
    <a href="{{ route('public.services.index') }}" class="text-sm font-bold text-brand-600 hover:text-brand-700">&larr; All services</a>
    <div class="mt-8 grid items-start gap-10 lg:grid-cols-[.72fr_1.28fr]">
        <div class="lg:sticky lg:top-32">
            <p class="text-xs font-bold uppercase tracking-[.2em] text-brand-600">{{ $service->category->name ?? 'Service' }}</p>
            <h1 class="mt-4 font-display text-4xl font-bold tracking-tight text-ink-900 sm:text-5xl">{{ $service->name }}</h1>
            <p class="mt-5 max-w-xl text-base leading-7 text-ink-600">{{ $service->description ?: 'Tell us what you need and our team will help you move it forward.' }}</p>
            <div class="mt-8 grid max-w-sm grid-cols-2 gap-3"><div class="surface-card rounded-xl p-4"><p class="text-[11px] font-bold uppercase tracking-wider text-ink-500">Price</p><p class="mt-2 font-display text-lg font-bold text-ink-900">{{ $service->formatted_price === 'Free' ? 'No fee' : $service->formatted_price }}</p></div><div class="surface-card rounded-xl p-4"><p class="text-[11px] font-bold uppercase tracking-wider text-ink-500">Typical duration</p><p class="mt-2 font-display text-lg font-bold text-ink-900">{{ $service->duration }}</p></div></div>
            <div class="mt-8 rounded-xl border border-brand-100 bg-brand-50 p-5"><p class="text-sm font-bold text-brand-800">What happens next?</p><p class="mt-2 text-sm leading-6 text-brand-700">Send your details and we will review the request, then contact you with the next step.</p></div>
        </div>

        <form method="POST" action="{{ route('public.submissions.store') }}" enctype="multipart/form-data" class="surface-panel rounded-2xl p-6 sm:p-8">
            @csrf
            <input type="hidden" name="service_id" value="{{ $service->id }}">
            <div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-600">Application</p><h2 class="mt-2 font-display text-2xl font-bold text-ink-900">Start your request</h2><p class="mt-2 text-sm leading-6 text-ink-500">Complete the details below and we will confirm the next step.</p></div>
            @include('partials.alerts')
            <div class="form-section mt-7 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2"><p class="text-sm font-bold text-ink-900">Contact details</p><p class="mt-1 text-xs text-ink-500">How should we reach you about this request?</p></div>
                <label class="text-sm font-semibold text-ink-800">Full name<input class="field mt-2" name="customer_name" value="{{ old('customer_name') }}" required></label>
                <label class="text-sm font-semibold text-ink-800">Phone<input class="field mt-2" name="customer_phone" value="{{ old('customer_phone') }}" required></label>
                <label class="text-sm font-semibold text-ink-800">Email<input class="field mt-2" type="email" name="customer_email" value="{{ old('customer_email') }}"></label>
                <label class="text-sm font-semibold text-ink-800">Preferred date<input class="field mt-2" type="date" name="preferred_date" value="{{ old('preferred_date') }}"></label>
            </div>
            <div class="form-section mt-7">
                <p class="text-sm font-bold text-ink-900">Service details</p><p class="mt-1 text-xs text-ink-500">Tell us enough to route your request properly.</p>
                @foreach($service->fields->reject(fn ($field): bool => $field->isCoreContactField()) as $field)
                    <div class="mt-5"><label for="field-{{ $field->field_key }}" class="block text-sm font-semibold text-ink-800">{{ $field->label }} @if($field->is_required)<span class="text-red-500">*</span>@endif</label>
                        @if($field->field_type === 'textarea')<textarea id="field-{{ $field->field_key }}" class="field mt-2" name="fields[{{ $field->field_key }}]" rows="3" placeholder="{{ $field->placeholder }}" @required($field->is_required)>{{ old('fields.'.$field->field_key, $field->default_value) }}</textarea>
                        @elseif($field->field_type === 'select')<select id="field-{{ $field->field_key }}" class="field mt-2" name="fields[{{ $field->field_key }}]" @required($field->is_required)><option value="">Choose an option</option>@foreach($field->getOptionsArray() as $option)<option value="{{ $option }}" @selected(old('fields.'.$field->field_key, $field->default_value) === $option)>{{ $option }}</option>@endforeach</select>
                        @elseif($field->field_type === 'file')<input id="field-{{ $field->field_key }}" class="field mt-2" type="file" name="fields[{{ $field->field_key }}]" @required($field->is_required)>
                        @elseif($field->field_type === 'checkbox')<input id="field-{{ $field->field_key }}" class="mt-2 h-4 w-4" type="checkbox" name="fields[{{ $field->field_key }}]" value="1" @checked(old('fields.'.$field->field_key, $field->default_value))>
                        @else<input id="field-{{ $field->field_key }}" class="field mt-2" type="{{ in_array($field->field_type, ['text', 'email', 'number', 'tel', 'date', 'time']) ? $field->field_type : 'text' }}" name="fields[{{ $field->field_key }}]" value="{{ old('fields.'.$field->field_key, $field->default_value) }}" placeholder="{{ $field->placeholder }}" @required($field->is_required)>@endif
                        @if($field->help_text)<p class="mt-1.5 text-xs text-ink-500">{{ $field->help_text }}</p>@endif
                    </div>
                @endforeach
                <label class="mt-5 block text-sm font-semibold text-ink-800">Additional notes<textarea class="field mt-2" name="customer_notes" rows="4" placeholder="Anything else we should know?">{{ old('customer_notes') }}</textarea></label>
            </div>
            <div class="sticky bottom-0 mt-8 border-t border-mist-200 bg-white pt-5"><button class="btn btn-primary w-full" type="submit">Submit request <span aria-hidden="true">↗</span></button><p class="mt-3 text-center text-xs text-ink-500">You will receive a reference number to track progress.</p></div>
        </form>
    </div>
</section>
@endsection
