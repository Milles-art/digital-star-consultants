<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Application {{ $submission->reference_number }} — Digital Star Consultants</title>
    <style>
        @page { margin: 16mm; }
        body { font-family: Arial, sans-serif; color:#111827; margin:0; font-size:12px; }
        .top { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:2px solid #102a56; padding-bottom:14px; margin-bottom:22px; }
        .brand { font-size:20px; font-weight:800; color:#102a56; letter-spacing:.08em; }
        .brand small { display:block; font-size:10px; letter-spacing:.2em; color:#c08b18; margin-top:4px; }
        h1 { font-size:23px; margin:0 0 6px; } h2 { font-size:15px; margin:0 0 10px; color:#102a56; }
        .meta { text-align:right; } .ref { font-size:16px; font-weight:800; } .muted { color:#4b5563; }
        .section { margin-bottom:20px; break-inside:avoid; }
        .card { border:1px solid #d1d5db; border-radius:10px; padding:14px; }
        dl { margin:0; } .row { display:grid; grid-template-columns: 35% 65%; gap:10px; padding:8px 0; border-bottom:1px solid #e5e7eb; }
        .row:last-child { border-bottom:0; } dt { font-weight:700; color:#374151; } dd { margin:0; white-space:pre-wrap; }
        .customer-note { background:#f8fafc; border-left:4px solid #c08b18; padding:12px; white-space:pre-wrap; }
        .doc { display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid #e5e7eb; } .footer { margin-top:30px; color:#4b5563; font-size:10px; }
        .print-hint { background:#fff7d6; border:1px solid #e9c95e; padding:8px 10px; margin-bottom:18px; } @media print { .print-hint { display:none; } }
    </style>
</head>
<body>
<div class="print-hint">Use your browser's Print command and choose <strong>Save as PDF</strong> to create a PDF copy.</div>
<div class="top">
    <div class="brand">DIGITAL STAR<small>CONSULTANTS</small></div>
    <div class="meta"><div class="ref">{{ $submission->reference_number }}</div><div class="muted">{{ $submission->created_at->format('d M Y, H:i') }}</div></div>
</div>

<div class="section"><h1>{{ $submission->service->name ?? 'Service Application' }}</h1><div class="muted">{{ $submission->service?->category?->full_path ?? $submission->service?->category?->name ?? 'Service request' }}</div></div>

<div class="section"><h2>Customer information</h2><div class="card"><dl>
<div class="row"><dt>Full name</dt><dd>{{ $submission->customer_name }}</dd></div>
<div class="row"><dt>Phone</dt><dd>{{ $submission->customer_phone }}</dd></div>
<div class="row"><dt>Email</dt><dd>{{ $submission->customer_email ?: 'Not provided' }}</dd></div>
<div class="row"><dt>Preferred date</dt><dd>{{ $submission->preferred_date?->format('d M Y') ?? 'Not specified' }}</dd></div>
</dl></div></div>

<div class="section"><h2>Application details</h2><div class="card"><dl>
@php $savedValues = $submission->values->keyBy('service_field_id'); @endphp
@forelse($submission->service?->allFields?->sortBy('sort_order') ?? [] as $field)
@php $value = $savedValues->get($field->id); @endphp
<div class="row"><dt>{{ $submission->values->firstWhere('service_field_id', $field->id)?->field_label_snapshot ?: $field->label }}</dt><dd>@if($value && !$value->isFile()){{ $value->getValueForDisplay() ?: 'Not provided' }}@elseif($value){{ $value->display_value }}@else<span class="muted">Not provided</span>@endif</dd></div>
@empty
<div class="row"><dt>Additional fields</dt><dd>No service-specific fields.</dd></div>
@endforelse
</dl></div></div>

@if($submission->customer_notes)<div class="section"><h2>Customer message</h2><div class="customer-note">{{ $submission->customer_notes }}</div></div>@endif

@if($submission->values->filter(fn($v) => $v->isFile())->isNotEmpty())
<div class="section"><h2>Uploaded documents</h2><div class="card">
@foreach($submission->values->filter(fn($v) => $v->isFile()) as $value)<div class="doc"><span>{{ $value->field_label_snapshot ?: ($value->field->label ?? 'Document') }}</span><strong>{{ $value->display_value }}</strong></div>@endforeach
</div></div>
@endif

<div class="footer">Digital Star Consultants · Application record · Reference {{ $submission->reference_number }}</div>
</body>
</html>