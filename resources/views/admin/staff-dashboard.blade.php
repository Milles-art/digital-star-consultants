@extends('layouts.admin', ['title' => 'My Work Queue', 'eyebrow' => 'Staff'])

@section('content')
    <section class="admin-panel reveal">
        <div class="admin-panel-header">
            <div>
                <p class="admin-kicker">Assigned work</p>
                <h2 class="admin-panel-title">Submissions assigned to you</h2>
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
                        <th>Created</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse (($submissions ?? $my_submissions) as $submission)
                        <tr>
                            <td><a class="admin-link" href="{{ route('staff.submissions.show', $submission) }}">{{ $submission->reference_number }}</a></td>
                            <td>{{ $submission->customer_name }}</td>
                            <td>{{ $submission->service?->name ?? 'N/A' }}</td>
                            <td><span class="admin-badge is-{{ $submission->status_color }}">{{ $submission->status_label }}</span></td>
                            <td>{{ $submission->created_at?->format('M d, Y H:i') }}</td>
                            <td class="text-right"><a class="admin-button admin-button-muted" href="{{ route('staff.submissions.show', $submission) }}">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6">@include('admin.partials.empty', ['message' => 'No assigned submissions yet.'])</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @isset($submissions)
            <div class="mt-6">{{ $submissions->links() }}</div>
        @endisset
    </section>
@endsection
