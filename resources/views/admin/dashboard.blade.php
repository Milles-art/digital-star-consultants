@extends('layouts.admin', ['title' => 'Dashboard', 'eyebrow' => 'Overview'])

@section('content')
{{-- Hero strip — different from stat grid --}}
<section class="admin-hero glass-reveal mb-6" style="border-radius:24px;padding:1.75rem 1.5rem;">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="admin-kicker">Digital Star Consultants</p>
            <h2 class="mt-1 text-2xl font-black text-white sm:text-3xl">Operations console</h2>
            <p class="mt-2 max-w-xl text-sm text-white/55">
                Track submissions, assign staff, and monitor service demand. Built for the Mbagala desk.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.submissions.index') }}" class="admin-button admin-button-dark">All submissions</a>
            <a href="{{ route('admin.reports.index') }}" class="admin-button admin-button-muted">Reports</a>
        </div>
    </div>
</section>

{{-- Stats — glass tiles --}}
<div class="admin-stat-grid mb-6">
    <div class="admin-stat-card glass-reveal">
        <p class="label">Pending</p>
        <p class="value">{{ $stats['pending'] ?? $pendingCount ?? '—' }}</p>
        <p class="hint">Awaiting action</p>
    </div>
    <div class="admin-stat-card glass-reveal">
        <p class="label">In progress</p>
        <p class="value">{{ $stats['in_progress'] ?? $inProgressCount ?? '—' }}</p>
        <p class="hint">With staff</p>
    </div>
    <div class="admin-stat-card glass-reveal">
        <p class="label">Completed</p>
        <p class="value">{{ $stats['completed'] ?? $completedCount ?? '—' }}</p>
        <p class="hint">This period</p>
    </div>
    <div class="admin-stat-card glass-reveal">
        <p class="label">Total</p>
        <p class="value">{{ $stats['total'] ?? $totalCount ?? '—' }}</p>
        <p class="hint">All submissions</p>
    </div>
</div>

<div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
    {{-- Recent submissions — table, not cards --}}
    <section class="admin-panel glass-reveal" style="border-radius:24px;padding:1.25rem;">
        <div class="admin-panel-header mb-4 flex items-center justify-between">
            <div>
                <p class="admin-kicker">Queue</p>
                <h2 class="admin-panel-title text-lg">Recent submissions</h2>
            </div>
            <a href="{{ route('admin.submissions.index') }}" class="admin-button admin-button-muted text-xs">View all</a>
        </div>

        <div class="overflow-x-auto">
            <table class="admin-glass-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($recentSubmissions ?? $submissions ?? collect()) as $submission)
                        <tr>
                            <td class="font-bold">{{ $submission->reference_number }}</td>
                            <td>{{ $submission->customer_name }}</td>
                            <td>
                                <span class="admin-badge">{{ $submission->status_label ?? $submission->status }}</span>
                            </td>
                            <td class="text-right">
                                <a class="admin-button admin-button-muted text-xs" href="{{ route('admin.submissions.show', $submission) }}">Open</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-white/40">No submissions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Side column — different layout --}}
    <div class="space-y-6">
        <section class="admin-panel glass-reveal" style="border-radius:24px;padding:1.25rem;">
            <p class="admin-kicker">Shortcuts</p>
            <h2 class="admin-panel-title mt-1 text-lg">Jump to</h2>
            <div class="mt-4 grid gap-2">
                <a href="{{ route('admin.services.index') }}" class="admin-button admin-button-muted w-full justify-between">
                    <span>Services</span><span>→</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="admin-button admin-button-muted w-full justify-between">
                    <span>Categories</span><span>→</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="admin-button admin-button-muted w-full justify-between">
                    <span>Users</span><span>→</span>
                </a>
                <a href="{{ route('admin.contact-messages.index') }}" class="admin-button admin-button-muted w-full justify-between">
                    <span>Messages</span><span>→</span>
                </a>
            </div>
        </section>

        <section class="admin-panel glass-reveal" style="border-radius:24px;padding:1.25rem;background:linear-gradient(145deg,rgba(245,200,75,.15),rgba(21,87,166,.2));">
            <p class="admin-kicker">Public site</p>
            <h2 class="mt-1 text-lg font-black text-white">Citizen front door</h2>
            <p class="mt-2 text-sm text-white/55">EN/SW · track by reference · WhatsApp</p>
            <a href="{{ route('home') }}" class="admin-button admin-button-dark mt-4 inline-flex">Open public site</a>
        </section>
    </div>
</div>
@endsection
