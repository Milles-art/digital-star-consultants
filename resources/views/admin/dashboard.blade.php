@extends('layouts.admin', ['title' => 'Executive Dashboard', 'eyebrow' => 'Management'])

@section('content')
    @php
        $cards = [
            ['label' => 'Total submissions', 'value' => $stats['total_submissions'] ?? 0, 'tone' => 'blue'],
            ['label' => 'Pending review', 'value' => $stats['pending_submissions'] ?? 0, 'tone' => 'gold'],
            ['label' => 'In progress', 'value' => $stats['in_progress_submissions'] ?? 0, 'tone' => 'cyan'],
            ['label' => 'Completed', 'value' => $stats['completed_submissions'] ?? 0, 'tone' => 'green'],
            ['label' => 'Active services', 'value' => $stats['total_services'] ?? 0, 'tone' => 'blue'],
            ['label' => 'Today', 'value' => $stats['today_submissions'] ?? 0, 'tone' => 'gold'],
        ];
    @endphp

    <section class="admin-hero reveal">
        <div>
            <p class="admin-kicker">Today at a glance</p>
            <h2 class="mt-2 max-w-2xl text-3xl font-black text-white sm:text-4xl">Operations, requests, and team workload in one clean view.</h2>
        </div>
        <div class="admin-hero-pill">
            <span class="text-2xl font-black">{{ $stats['total_staff'] ?? 0 }}</span>
            <span class="text-xs text-white/64">staff accounts</span>
        </div>
    </section>

    <section class="admin-stat-grid reveal-delay">
        @foreach ($cards as $card)
            <article class="admin-stat-card">
                <span class="admin-stat-dot is-{{ $card['tone'] }}"></span>
                <p class="text-sm font-semibold text-muted">{{ $card['label'] }}</p>
                <p class="mt-4 text-3xl font-black text-ink">{{ number_format($card['value']) }}</p>
            </article>
        @endforeach
    </section>

    <section class="admin-panel reveal-delay-2">
        <div class="admin-panel-header">
            <div>
                <p class="admin-kicker">Queue</p>
                <h2 class="admin-panel-title">Recent submissions</h2>
            </div>
            <a href="{{ route('admin.submissions.index') }}" class="admin-button admin-button-muted">View all</a>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Owner</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recent_submissions as $submission)
                        <tr>
                            <td><a class="admin-link" href="{{ route('admin.submissions.show', $submission) }}">{{ $submission->reference_number }}</a></td>
                            <td>{{ $submission->customer_name }}</td>
                            <td>{{ $submission->service?->name ?? 'N/A' }}</td>
                            <td><span class="admin-badge is-{{ $submission->status_color }}">{{ $submission->status_label }}</span></td>
                            <td>{{ $submission->processedBy?->name ?? 'Unassigned' }}</td>
                            <td>{{ $submission->created_at?->format('M d, Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">@include('admin.partials.empty', ['message' => 'No submissions yet.'])</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
