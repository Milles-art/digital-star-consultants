@extends('layouts.admin')
@section('page_title','Daily report')
@section('content')
<div class="ds-report-page">
  <section class="admin-panel ds-report-hero"><div><span class="eyebrow">REPORTS / DAILY</span><h2>Daily operations snapshot</h2><p>Requests received, completed and outstanding for the selected day.</p></div><div class="ds-report-actions"><a class="button button-outline" href="{{ route('admin.reports.index') }}">← Reports</a><a class="button button-dark" href="{{ route('admin.finance.index') }}">Finance →</a></div></section>
  <section class="admin-panel ds-report-filter"><form method="GET"><label>Date<input type="date" name="date" value="{{ $filters['date'] }}"></label><button class="button button-yellow" type="submit">Apply date</button></form></section>
  <section class="ds-kpi-grid">
    <article class="admin-panel ds-kpi"><span>Requests received</span><strong>{{ number_format($data['total_submissions']) }}</strong><small>Created on {{ \Carbon\Carbon::parse($filters['date'])->format('d M Y') }}</small></article>
    <article class="admin-panel ds-kpi"><span>Completed</span><strong>{{ number_format($data['completed_today']) }}</strong><small>Completed on this day</small></article>
    <article class="admin-panel ds-kpi"><span>In progress</span><strong>{{ number_format($data['in_progress']) }}</strong><small>Current status at report time</small></article>
    <article class="admin-panel ds-kpi"><span>Pending</span><strong>{{ number_format($data['pending']) }}</strong><small>Currently pending</small></article>
  </section>
  <section class="admin-panel ds-panel"><div class="panel-heading"><div><span class="eyebrow">STATUS MIX</span><h3>What arrived today</h3></div></div>
    <div class="ds-status-grid"><div><span>Completed</span><strong>{{ $data['completed_today'] }}</strong></div><div><span>In progress</span><strong>{{ $data['in_progress'] }}</strong></div><div><span>Pending</span><strong>{{ $data['pending'] }}</strong></div><div><span>Rejected</span><strong>{{ $data['rejected'] }}</strong></div></div>
  </section>
</div>
@push('styles')<style>
.ds-report-page{display:grid;gap:18px}.ds-report-hero{display:flex;justify-content:space-between;gap:24px;align-items:flex-end}.ds-report-hero h2{margin:6px 0 8px}.ds-report-hero p{max-width:720px;margin:0;color:#334155;font-size:15px;line-height:1.65}.ds-report-actions{display:flex;gap:10px;flex-wrap:wrap}.ds-report-filter form{display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap}.ds-report-filter label{display:grid;gap:7px;font-weight:700;color:#0f172a;font-size:14px}.ds-report-filter input{min-width:190px}.ds-kpi-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}.ds-kpi{display:grid;gap:8px}.ds-kpi span,.ds-status-grid span{font-size:14px;font-weight:700;color:#334155}.ds-kpi strong{font-size:30px;line-height:1}.ds-kpi small{font-size:13px;color:#475569}.ds-status-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.ds-status-grid>div{border:1px solid #e2e8f0;border-radius:16px;padding:18px;background:#fff;display:grid;gap:8px}.ds-status-grid strong{font-size:24px}.ds-panel{padding:20px}@media(max-width:900px){.ds-report-hero{display:grid}.ds-kpi-grid{grid-template-columns:repeat(2,1fr)}.ds-status-grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:560px){.ds-kpi-grid,.ds-status-grid{grid-template-columns:1fr}.ds-report-actions{width:100%}.ds-report-actions .button{flex:1}}
</style>@endpush
@endsection