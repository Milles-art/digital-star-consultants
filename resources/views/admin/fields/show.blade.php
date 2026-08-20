@extends('layouts.admin')
@section('title', $field->label.' | Fields')
@section('heading', $field->label)
@section('content')<a href="{{ route('admin.fields.index', $field->service_id) }}" class="text-sm font-semibold text-brand-600">&larr; Fields</a><div class="mt-6 max-w-2xl rounded-xl border border-mist-200 bg-white p-6"><dl class="grid gap-5 sm:grid-cols-2 text-sm"><div><dt class="text-slate-500">Key</dt><dd class="mt-1 font-semibold">{{ $field->field_key }}</dd></div><div><dt class="text-slate-500">Type</dt><dd class="mt-1 font-semibold">{{ $field->field_type }}</dd></div><div><dt class="text-slate-500">Required</dt><dd class="mt-1 font-semibold">{{ $field->is_required ? 'Yes' : 'No' }}</dd></div><div><dt class="text-slate-500">Order</dt><dd class="mt-1 font-semibold">{{ $field->sort_order }}</dd></div><div class="sm:col-span-2"><dt class="text-slate-500">Help text</dt><dd class="mt-1">{{ $field->help_text ?: 'None' }}</dd></div></dl></div>
@endsection
