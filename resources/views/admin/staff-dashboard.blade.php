@extends('layouts.admin')
@section('page_title','My work queue')
@section('content')
@if(isset($stats))
<div class="kpi-grid"><div class="kpi-card"><span>Assigned</span><strong>{{ $stats['total_assigned'] }}</strong></div><div class="kpi-card"><span>Pending</span><strong>{{ $stats['pending'] }}</strong></div><div class="kpi-card"><span>In progress</span><strong>{{ $stats['in_progress'] }}</strong></div><div class="kpi-card"><span>Completed</span><strong>{{ $stats['completed'] }}</strong></div></div>
@else
<div class="page-tools"><span class="eyebrow">STAFF QUEUE</span><a class="button button-outline" href="{{ route('staff.submissions') }}">Refresh queue</a></div>
@endif
<section class="panel"><div class="panel-head"><div><span class="eyebrow">WORK QUEUE</span><h2>Requests assigned to you</h2></div></div><div class="table-wrap"><table><thead><tr><th>Reference</th><th>Customer</th><th>Service</th><th>Status</th><th>Date</th></tr></thead><tbody>@forelse($my_submissions ?? $submissions as $s)<tr><td><a class="mono-link" href="{{ route('staff.submissions.show',$s) }}">{{ $s->reference_number }}</a></td><td>{{ $s->customer_name }}</td><td>{{ $s->service->name ?? '—' }}</td><td><span class="badge {{ $s->status_color }}">{{ $s->status_label }}</span></td><td>{{ $s->created_at->format('d M Y') }}</td></tr>@empty<tr><td colspan="5" class="empty-row">Your queue is clear.</td></tr>@endforelse</tbody></table></div></section>
@endsection
