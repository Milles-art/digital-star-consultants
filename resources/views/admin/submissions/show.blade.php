@extends('layouts.admin')

@section('title', $submission->reference_number.' | Submissions')
@section('heading', $submission->reference_number)

@section('content')
<style>
    .back{display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;color:#2563eb;text-decoration:none;margin-bottom:16px}
    .grid{display:grid;gap:16px}
    @media(min-width:1024px){.grid{grid-template-columns:1.2fr .9fr}}
    .card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px}
    .card h2{margin:0 0 16px;font-size:15px;font-weight:700}
    .row{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:12px;font-size:14px}
    .row dt{color:#64748b;min-width:110px}
    .row dd{margin:0;font-weight:600;text-align:right}
    .badge{display:inline-flex;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize}
    .badge-pending{background:#fef3c7;color:#92400e}
    .badge-in_progress{background:#e0f2fe;color:#075985}
    .badge-completed{background:#d1fae5;color:#065f46}
    .badge-rejected{background:#fee2e2;color:#991b1b}
    .badge-awaiting_customer{background:#f3e8ff;color:#6b21a8}
    .badge-cancelled{background:#f1f5f9;color:#475569}
    label{display:block;font-size:13px;font-weight:600;margin-bottom:6px}
    select,textarea,input[type=text]{width:100%;border:1px solid #e2e8f0;border-radius:10px;padding:10px 12px;font-size:14px;background:#f8fafc}
    textarea{min-height:110px;resize:vertical}
    .btn{display:inline-flex;align-items:center;justify-content:center;border:0;border-radius:10px;padding:10px 14px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none}
    .btn-primary{background:#2563eb;color:#fff}
    .btn-sky{background:#0284c7;color:#fff}
    .btn-green{background:#059669;color:#fff}
    .btn-red{background:#dc2626;color:#fff}
    .btn-ghost{background:#fff;border:1px solid #e2e8f0;color:#0f172a}
    .actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
    .field-list{list-style:none;margin:0;padding:0}
    .field-list li{padding:12px 0;border-bottom:1px solid #f1f5f9;font-size:14px}
    .field-list li:last-child{border-bottom:0}
    .field-list .lbl{color:#64748b;font-size:12px;margin-bottom:4px}
    .muted{color:#64748b;font-size:13px}
    .stack{display:grid;gap:16px}
</style>

<a class="back" href="{{ route('admin.submissions.index') }}">← All submissions</a>

<div class="grid">
    {{-- Left: details + fields --}}
    <div class="stack">
        <div class="card">
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:8px">
                <h2 style="margin:0">Request details</h2>
                <span class="badge badge-{{ $submission->status }}">{{ str_replace('_', ' ', $submission->status) }}</span>
            </div>
            <dl>
                <div class="row"><dt>Reference</dt><dd>{{ $submission->reference_number }}</dd></div>
                <div class="row"><dt>Customer</dt><dd>{{ $submission->customer_name }}</dd></div>
                <div class="row"><dt>Phone</dt><dd>{{ $submission->customer_phone }}</dd></div>
                <div class="row"><dt>Email</dt><dd>{{ $submission->customer_email ?: '—' }}</dd></div>
                <div class="row"><dt>Service</dt><dd>{{ $submission->service->name ?? 'N/A' }}</dd></div>
                <div class="row"><dt>Preferred date</dt><dd>{{ $submission->preferred_date?->format('d M Y') ?: '—' }}</dd></div>
                <div class="row"><dt>Assigned to</dt><dd>{{ $submission->processedBy->name ?? 'Unassigned' }}</dd></div>
                <div class="row"><dt>Submitted</dt><dd>{{ $submission->created_at?->format('d M Y, H:i') }}</dd></div>
            </dl>
            @if ($submission->customer_notes)
                <p class="muted" style="margin-top:12px"><strong>Customer notes:</strong> {{ $submission->customer_notes }}</p>
            @endif
        </div>

        <div class="card">
            <h2>Submitted fields</h2>
            <ul class="field-list">
                @forelse ($submission->values as $value)
                    <li>
                        <div class="lbl">{{ $value->field->label ?? 'Field' }}</div>
                        @if ($value->is_file && $value->file_path)
                            <a class="btn btn-ghost" style="padding:6px 10px;font-size:12px"
                               href="{{ route('admin.submissions.files.download', [$submission, $value]) }}">
                                Download {{ $value->display_value }}
                            </a>
                        @else
                            <div style="font-weight:600">{{ $value->display_value ?? $value->value ?? '—' }}</div>
                        @endif
                    </li>
                @empty
                    <li class="muted">No extra fields submitted.</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Right: actions --}}
    <div class="stack">
        <div class="card">
            <h2>Assign staff</h2>
            <form method="POST" action="{{ route('admin.submissions.assign', $submission) }}">
                @csrf
                <label for="staff_id">Staff member</label>
                <select id="staff_id" name="staff_id" required>
                    <option value="">Select…</option>
                    @foreach ($staff as $member)
                        <option value="{{ $member->id }}" @selected($submission->processed_by === $member->id)>
                            {{ $member->name }} ({{ $member->role }})
                        </option>
                    @endforeach
                </select>
                <div class="actions">
                    <button class="btn btn-primary" type="submit">Assign</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Status</h2>
            <p class="muted" style="margin-top:0">Current: <strong>{{ str_replace('_', ' ', $submission->status) }}</strong></p>
            <div class="actions">
                <form method="POST" action="{{ route('admin.submissions.in-progress', $submission) }}">
                    @csrf
                    <button class="btn btn-sky" type="submit">In progress</button>
                </form>
                <form method="POST" action="{{ route('admin.submissions.complete', $submission) }}">
                    @csrf
                    <button class="btn btn-green" type="submit">Complete</button>
                </form>
            </div>
            <form method="POST" action="{{ route('admin.submissions.reject', $submission) }}" style="margin-top:14px">
                @csrf
                <label for="reason">Reject reason (optional)</label>
                <textarea id="reason" name="reason" placeholder="Why is this rejected?"></textarea>
                <div class="actions">
                    <button class="btn btn-red" type="submit">Reject</button>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Staff notes</h2>
            <form method="POST" action="{{ route('admin.submissions.update', $submission) }}">
                @csrf
                @method('PUT')
                <label for="staff_notes">Internal notes</label>
                <textarea id="staff_notes" name="staff_notes">{{ old('staff_notes', $submission->staff_notes) }}</textarea>
                <div class="actions">
                    <button class="btn btn-primary" type="submit">Save notes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
