@extends('layouts.admin')

@section('title', 'Submissions | Digital Star Consultants')
@section('heading', 'Submissions')

@section('content')
<style>
    .filters{display:grid;gap:12px;grid-template-columns:1fr;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:16px;margin-bottom:20px}
    @media(min-width:768px){.filters{grid-template-columns:1.4fr 160px 160px auto}}
    .filters input,.filters select{width:100%;border:1px solid #e2e8f0;border-radius:10px;padding:10px 12px;font-size:14px;background:#f8fafc}
    .filters button{border:0;border-radius:10px;padding:10px 18px;background:#2563eb;color:#fff;font-weight:600;cursor:pointer}
    .panel{background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden}
    table{width:100%;border-collapse:collapse;font-size:14px;min-width:860px}
    th{text-align:left;padding:12px 16px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#64748b;background:#f8fafc;border-bottom:1px solid #e2e8f0}
    td{padding:14px 16px;border-bottom:1px solid #f1f5f9}
    tr:hover td{background:#f8fafc}
    .ref{color:#2563eb;font-weight:600;text-decoration:none}
    .muted{color:#64748b}
    .badge{display:inline-flex;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:600;text-transform:capitalize}
    .badge-pending{background:#fef3c7;color:#92400e}
    .badge-in_progress{background:#e0f2fe;color:#075985}
    .badge-completed{background:#d1fae5;color:#065f46}
    .badge-rejected{background:#fee2e2;color:#991b1b}
    .badge-awaiting_customer{background:#f3e8ff;color:#6b21a8}
    .badge-cancelled{background:#f1f5f9;color:#475569}
    .empty{text-align:center;padding:48px;color:#94a3b8}
    .pager{padding:16px}
</style>

<form method="GET" action="{{ route('admin.submissions.index') }}" class="filters">
    <input type="search" name="search" value="{{ request('search') }}" placeholder="Reference, customer, email, phone…">
    <select name="status">
        <option value="">All statuses</option>
        @foreach ($statuses ?? [] as $st)
            <option value="{{ $st['value'] }}" @selected(request('status') === $st['value'])>{{ $st['label'] }}</option>
        @endforeach
    </select>
    <select name="unassigned">
        <option value="">Assigned filter</option>
        <option value="1" @selected(request('unassigned') === '1')>Unassigned only</option>
    </select>
    <button type="submit">Filter</button>
</form>

<div class="panel">
    <div style="overflow-x:auto">
        <table>
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Assigned to</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($submissions as $s)
                    <tr>
                        <td><a class="ref" href="{{ route('admin.submissions.show', $s) }}">{{ $s->reference_number }}</a></td>
                        <td>
                            <div>{{ $s->customer_name }}</div>
                            <div class="muted" style="font-size:12px">{{ $s->customer_phone }}</div>
                        </td>
                        <td class="muted">{{ $s->service->name ?? 'N/A' }}</td>
                        <td class="muted">{{ $s->processedBy->name ?? 'Unassigned' }}</td>
                        <td><span class="badge badge-{{ $s->status }}">{{ str_replace('_', ' ', $s->status) }}</span></td>
                        <td class="muted">{{ $s->created_at?->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">
                            @if (request('search') || request('status') || request('unassigned'))
                                No submissions match your filters.
                            @else
                                No submissions yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if (method_exists($submissions, 'links'))
        <div class="pager">{{ $submissions->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
