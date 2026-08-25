@extends('layouts.admin', ['title' => 'Reports', 'eyebrow' => 'Insights'])

@section('content')
    <section class="admin-panel reveal">
        <form class="admin-filter-grid" data-report-filters
            data-overview-url="{{ route('admin.reports.overview') }}"
            data-staff-url="{{ route('admin.reports.staff-performance') }}"
            data-services-url="{{ route('admin.reports.service-usage') }}">
            <input class="admin-field" type="date" name="start_date" value="{{ $defaultStart }}">
            <input class="admin-field" type="date" name="end_date" value="{{ $defaultEnd }}">
            <button class="admin-button admin-button-dark" type="submit">Refresh</button>
        </form>
    </section>

    <section class="admin-stat-grid reveal-delay" data-report-overview>
        @foreach (['Submissions', 'Completed', 'Pending', 'Completion rate'] as $label)
            <article class="admin-stat-card is-loading">
                <span class="admin-stat-dot is-blue"></span>
                <p class="text-sm font-semibold text-muted">{{ $label }}</p>
                <p class="mt-4 text-3xl font-black text-ink">...</p>
            </article>
        @endforeach
    </section>

    <div class="admin-grid-2 reveal-delay-2">
        <section class="admin-panel">
            <div class="admin-panel-header"><h2 class="admin-panel-title">Staff performance</h2></div>
            <div class="admin-table-wrap" data-report-staff>@include('admin.partials.empty', ['message' => 'Loading staff report...'])</div>
        </section>
        <section class="admin-panel">
            <div class="admin-panel-header"><h2 class="admin-panel-title">Service usage</h2></div>
            <div class="admin-table-wrap" data-report-services>@include('admin.partials.empty', ['message' => 'Loading service report...'])</div>
        </section>
    </div>
@endsection
