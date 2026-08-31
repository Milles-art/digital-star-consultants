@extends('layouts.app')
@section('title', ($service->name ?? 'Service').' — Digital Star Consultants')
@section('content')
<section class="ds-page-hero compact"><div class="ds-container"><a class="ds-back" href="{{ route('public.services.index') }}">← All services</a><span class="ds-index">{{ $service->category->name ?? 'SERVICE' }}</span><h1>{{ $service->name }}</h1><p>{{ $service->description }}</p></div></section>
<section class="ds-section"><div class="ds-container ds-form-layout"><div><span class="ds-index">SERVICE INTAKE</span><h2>Tell us what<br><em>you need.</em></h2><p>Submit the details below. We'll review your request and use the generated reference to keep you updated.</p></div>
<form class="ds-form" method="POST" action="{{ route('public.submissions.store') }}" enctype="multipart/form-data">@csrf<input type="hidden" name="service_id" value="{{ $service->id }}">
@if(isset($errors) && $errors->any())<div class="ds-errors">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
@foreach(($fields ?? $service->fields ?? []) as $field)
<div class="ds-field"><label for="field_{{ $field->id }}">{{ $field->label }} @if($field->required)<sup>*</sup>@endif</label>
@switch($field->type)
@case('textarea')<textarea id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" {{ $field->required?'required':'' }}>{{ old('fields.'.$field->id) }}</textarea>@break
@case('select')<select id="field_{{ $field->id }}" name="fields[{{ $field->id }}]" {{ $field->required?'required':'' }}><option value="">Select…</option>@foreach(($field->options ?? []) as $option)<option value="{{ is_array($option)?($option['value']??''):$option }}" @selected(old('fields.'.$field->id)==(is_array($option)?($option['value']??''):$option))>{{ is_array($option)?($option['label']??$option['value']??''):$option }}</option>@endforeach</select>@break
@case('file')<input id="field_{{ $field->id }}" type="file" name="fields[{{ $field->id }}]" {{ $field->required?'required':'' }}>@break
@case('date')<input id="field_{{ $field->id }}" type="date" name="fields[{{ $field->id }}]" value="{{ old('fields.'.$field->id) }}" {{ $field->required?'required':'' }}>@break
@case('number')<input id="field_{{ $field->id }}" type="number" name="fields[{{ $field->id }}]" value="{{ old('fields.'.$field->id) }}" {{ $field->required?'required':'' }}>@break
@default<input id="field_{{ $field->id }}" type="text" name="fields[{{ $field->id }}]" value="{{ old('fields.'.$field->id) }}" {{ $field->required?'required':'' }}>
@endswitch</div>
@endforeach
<button class="ds-btn ds-btn-gold ds-submit">Submit Request ↗</button></form></div></section>
@endsection