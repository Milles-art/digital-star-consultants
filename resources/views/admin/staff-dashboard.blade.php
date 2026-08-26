@extends('layouts.admin')

@section('title', 'My dashboard | Digital Star Consultants')
@section('heading', 'My dashboard')

@section('content')
@php
    // Support both DashboardController ($my_submissions + $stats)
    // and Staff\SubmissionController index ($submissions paginator)
    $list = $my_submissions ?? collect();
    if (isset($submissions)) {
        $list = $submissions;
    }
@endphp

<style>
    .kpi-row{display:grid;gap:16px;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));margin-bottom:24px}
    .kpi{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:16px;box-shadow:0 1px 2px rgba(15,23,42,.03)}
    .kpi p{margin:0}
    .kpi-label{font-size:12px;color:#64748b;margin-bottom:6px}
    .kpi-value{font-size:26px;font-weight:700;color:#2563eb;letter-spacing:-.02em}
    .panel{background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden}
    .panel-head{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #e2e8f0}
    .panel-head h3{margin:0;font-size:15px;font-weight:700}
    .item{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:14px 16px;margin:8px 12px;border-radius:12px;background:#f8fafc;text-decoration:none;color:inherit}
    .item:hover{background:#eff6ff}
    .badge{display:inline-flex;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize}
    .badge-pending{background:#fef3c7;color:#92400e}
    .badge-in_progress{background:#e0f2fe;color:#075985}
    .badge-completed{background:#d1fae5;color:#065f46}
    .badge-rejected{background:#fee2e2;color:#991b1b}
    .empty{text-align:center;padding:40px;color:#94a3b8}
</style>

@if (!empty($stats) && is_array($stats))
    <div class="kpi-row">
        @foreach ($stats as $label => $value)
            <div class="kpi">
                <p class="kpi-label" style="text-transform:capitalize">{{ str_replace('_', ' ', $label) }}</p>
                <p class="kpi-value">{{ number_format((int) $value) }}</p>
            </div>
        @endforeach
    </div>
@endif

<div class="panel">
    <div class="panel-head">
        <h3>Assigned submissions</h3>
        <a href="{{ route('staff.submissions.index') }}" style="font-size:13px;font-weight:600;color:#2563eb;text-decoration:none">View all →</a>
    </div>

    @forelse ($list as $s)
        <a class="item" href="{{ route('staff.submissions.show', $s) }}">
            <span>
                <strong style="color:#2563eb">{{ $s->reference_number }}</strong>
                <span style="margin-left:10px;color:#64748b;font-size:13px">{{ $s->service->name ?? 'N/A' }}</span>
            </span>
            <span class="badge badge-{{ $s->status }}">{{ str_replace('_', ' ', $s->status) }}</span>
        </a>
    @empty
        <p class="empty">No assigned submissions yet.</p>
    @endforelse

    @if (isset($submissions) && method_exists($submissions, 'links'))
        <div style="padding:16px 20px">{{ $submissions->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
