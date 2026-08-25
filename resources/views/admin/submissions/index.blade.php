@extends('layouts.admin', ['title' => 'Submissions', 'eyebrow' => 'Work queue'])

@section('content')
    <section class="admin-panel reveal">
        <form method="GET" action="{{ route('admin.submissions.index') }}" class="admin-filter-grid">
            <input class="admin-field" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search reference, customer, email, phone">
            <select class="admin-field" name="status">
                <option value="">All statuses</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status['value'] }}" @selected(($filters['status'] ?? '') === $status['value'])>{{ $status['label'] }}</option>
                @endforeach
            </select>
            <select class="admin-field" name="service_id">
                <option value="">All services</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}" @selected((string) ($filters['service_id'] ?? '') === (string) $service->id)>{{ $service->name }}</option>
                @endforeach
            </select>
            <select class="admin-field" name="staff_id">
                <option value="">All owners</option>
                @foreach ($staff as $member)
                    <option value="{{ $member->id }}" @selected((string) ($filters['staff_id'] ?? '') === (string) $member->id)>{{ $member->name }}</option>
                @endforeach
            </select>
            <input class="admin-field" type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
            <input class="admin-field" type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
            <button class="admin-button admin-button-dark" type="submit">Filter</button>
            <a href="{{ route('admin.submissions.index') }}" class="admin-button admin-button-muted">Reset</a>
        </form>
    </section>

    <section class="admin-panel reveal-delay">
        <div class="admin-panel-header">
            <div>
                <p class="admin-kicker">Requests</p>
                <h2 class="admin-panel-title">{{ number_format($submissions->total()) }} submissions</h2>
            </div>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Assigned</th>
                        <th>Preferred</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $submission)
                        <tr>
                            <td><a class="admin-link" href="{{ route('admin.submissions.show', $submission) }}">{{ $submission->reference_number }}</a></td>
                            <td>
                                <span class="block font-bold text-ink">{{ $submission->customer_name }}</span>
                                <span class="text-xs text-muted">{{ $submission->customer_email }}</span>
                            </td>
                            <td>{{ $submission->service?->name ?? 'N/A' }}</td>
                            <td><span class="admin-badge is-{{ $submission->status_color }}">{{ $submission->status_label }}</span></td>
                            <td>{{ $submission->processedBy?->name ?? 'Unassigned' }}</td>
                            <td>{{ $submission->preferred_date?->format('M d, Y') ?? 'Any time' }}</td>
                            <td class="text-right"><a class="admin-button admin-button-muted" href="{{ route('admin.submissions.show', $submission) }}">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7">@include('admin.partials.empty', ['message' => 'No submissions match these filters.'])</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $submissions->links() }}</div>
    </section>
@endsection
