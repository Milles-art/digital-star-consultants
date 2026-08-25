@extends('layouts.admin', ['title' => 'Submission '.$submission->reference_number, 'eyebrow' => 'Request detail'])

@section('content')
    @php($isManagement = auth()->user()?->isManagement())

    <div class="admin-two-column">
        <section class="admin-panel reveal">
            <div class="admin-panel-header">
                <div>
                    <p class="admin-kicker">{{ $submission->reference_number }}</p>
                    <h2 class="admin-panel-title">{{ $submission->customer_name }}</h2>
                </div>
                <span class="admin-badge is-{{ $submission->status_color }}">{{ $submission->status_label }}</span>
            </div>

            <div class="admin-detail-grid">
                <div><span>Email</span><strong>{{ $submission->customer_email ?? 'N/A' }}</strong></div>
                <div><span>Phone</span><strong>{{ $submission->customer_phone }}</strong></div>
                <div><span>Service</span><strong>{{ $submission->service?->name ?? 'N/A' }}</strong></div>
                <div><span>Preferred date</span><strong>{{ $submission->preferred_date?->format('M d, Y H:i') ?? 'Any time' }}</strong></div>
                <div><span>Total price</span><strong>{{ $submission->total_price ? 'TSh '.number_format((float) $submission->total_price, 2) : 'Not set' }}</strong></div>
                <div><span>Assigned to</span><strong>{{ $submission->processedBy?->name ?? 'Unassigned' }}</strong></div>
            </div>

            @if ($submission->customer_notes)
                <div class="admin-note">
                    <p class="admin-kicker">Customer notes</p>
                    <p>{{ $submission->customer_notes }}</p>
                </div>
            @endif
        </section>

        <aside class="admin-panel reveal-delay">
            <h2 class="admin-panel-title">Actions</h2>

            @if ($isManagement)
                <form method="POST" action="{{ route('admin.submissions.assign', $submission) }}" class="mt-5 space-y-3" data-ajax data-success-reload>
                    @csrf
                    <label class="admin-label" for="staff_id">Assign owner</label>
                    <select class="admin-field" id="staff_id" name="staff_id" required>
                        @foreach ($staff as $member)
                            <option value="{{ $member->id }}" @selected($submission->processed_by === $member->id)>{{ $member->name }} - {{ $member->role_label }}</option>
                        @endforeach
                    </select>
                    <button class="admin-button admin-button-dark w-full" type="submit">Assign</button>
                </form>
            @endif

            <div class="mt-6 grid gap-3">
                <form method="POST" action="{{ $isManagement ? route('admin.submissions.in-progress', $submission) : route('staff.submissions.in-progress', $submission) }}" data-ajax data-success-reload>@csrf <button class="admin-button admin-button-muted w-full" type="submit">Mark in progress</button></form>
                <form method="POST" action="{{ $isManagement ? route('admin.submissions.complete', $submission) : route('staff.submissions.complete', $submission) }}" data-ajax data-success-reload>@csrf <button class="admin-button admin-button-success w-full" type="submit">Mark completed</button></form>
                <form method="POST" action="{{ $isManagement ? route('admin.submissions.reject', $submission) : route('staff.submissions.reject', $submission) }}" class="space-y-3" data-ajax data-success-reload data-confirm="Reject this submission?">
                    @csrf
                    <textarea class="admin-field" name="reason" rows="3" placeholder="Reason, optional"></textarea>
                    <button class="admin-button admin-button-danger w-full" type="submit">Reject</button>
                </form>
            </div>
        </aside>
    </div>

    <section class="admin-panel reveal-delay-2">
        <div class="admin-panel-header">
            <div>
                <p class="admin-kicker">Submitted fields</p>
                <h2 class="admin-panel-title">Request information</h2>
            </div>
        </div>

        <div class="admin-detail-grid">
            @forelse ($submission->values as $value)
                <div>
                    <span>{{ $value->field?->label ?? $value->field_key }}</span>
                    @if ($value->field?->isFileField())
                        <strong>
                            @if ($isManagement)
                                <a class="admin-link" href="{{ route('admin.submissions.files.download', [$submission, $value]) }}">Download file</a>
                            @else
                                File attached
                            @endif
                        </strong>
                    @else
                        <strong>{{ is_array($value->value) ? implode(', ', $value->value) : ($value->value ?? 'N/A') }}</strong>
                    @endif
                </div>
            @empty
                @include('admin.partials.empty', ['message' => 'No dynamic fields were captured for this submission.'])
            @endforelse
        </div>

        <form method="POST" action="{{ $isManagement ? route('admin.submissions.update', $submission) : route('staff.submissions.notes', $submission) }}" class="mt-6" data-ajax data-success-reload>
            @csrf
            @if ($isManagement)
                @method('PUT')
            @else
                @method('PUT')
            @endif
            <label class="admin-label" for="staff_notes">Internal notes</label>
            <textarea class="admin-field" id="staff_notes" name="staff_notes" rows="5">{{ $submission->staff_notes }}</textarea>
            <button class="admin-button admin-button-dark mt-3" type="submit">Save notes</button>
        </form>
    </section>
@endsection
