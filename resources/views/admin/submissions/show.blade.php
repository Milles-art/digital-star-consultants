@extends('layouts.admin')

@section('page_title', 'Request '.$submission->reference_number)

@section('content')
@php
    $statusClass = match ($submission->status) {
        \App\Models\Submission::STATUS_COMPLETED => 'success',
        \App\Models\Submission::STATUS_REJECTED => 'danger',
        \App\Models\Submission::STATUS_IN_PROGRESS => 'info',
        \App\Models\Submission::STATUS_AWAITING_CUSTOMER => 'secondary',
        default => 'warning',
    };
    $categoryPath = $submission->service?->category?->full_path ?? $submission->service?->category?->name ?? 'Service request';
    $fileValues = $submission->values->filter(fn($value) => $value->isFile());
    $detailValues = $submission->values->filter(fn($value) => !$value->isFile());
    $steps = [
        ['label' => 'Received', 'done' => true],
        ['label' => 'In progress', 'done' => in_array($submission->status, [\App\Models\Submission::STATUS_IN_PROGRESS, \App\Models\Submission::STATUS_AWAITING_CUSTOMER, \App\Models\Submission::STATUS_COMPLETED])],
        ['label' => 'Customer action', 'done' => $submission->status === \App\Models\Submission::STATUS_AWAITING_CUSTOMER],
        ['label' => 'Completed', 'done' => $submission->status === \App\Models\Submission::STATUS_COMPLETED],
    ];
@endphp

<div class="submission-workspace">
    <div class="submission-toolbar">
        <div class="submission-breadcrumb">
            <a href="{{ route('admin.submissions.index') }}">Requests</a>
            <span>/</span>
            <span>{{ $submission->reference_number }}</span>
        </div>
        <div class="submission-toolbar-actions">
            <a class="button button-outline" href="{{ route('admin.submissions.index') }}">← All requests</a>
            <a class="button button-outline" href="{{ route('public.track.show', ['reference' => $submission->reference_number]) }}" target="_blank" rel="noopener">Customer view ↗</a>
        </div>
    </div>

    <section class="request-hero">
        <div>
            <div class="request-eyebrow">{{ $categoryPath }}</div>
            <div class="request-title-row">
                <h2>{{ $submission->service->name ?? 'Service request' }}</h2>
                <span class="badge {{ $statusClass }}">{{ $submission->status_label }}</span>
            </div>
            <p>Reference <strong>{{ $submission->reference_number }}</strong> · Submitted {{ $submission->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="request-hero-meta">
            <div><span>Assigned to</span><strong>{{ $submission->processedBy->name ?? 'Unassigned' }}</strong></div>
            <div><span>Payment</span><strong>{{ ucfirst($submission->payment_status ?? 'pending') }}</strong></div>
        </div>
    </section>

    <div class="request-grid">
        <main class="request-main">
            <section class="admin-card request-progress-card">
                <div class="admin-card-head">
                    <div><span class="admin-kicker">WORKFLOW</span><h3>Application progress</h3></div>
                    <span class="progress-label">{{ $submission->status_label }}</span>
                </div>
                <div class="request-progress">
                    @foreach($steps as $index => $step)
                        <div class="request-progress-step {{ $step['done'] ? 'is-done' : '' }} {{ $submission->status === \App\Models\Submission::STATUS_AWAITING_CUSTOMER && $index === 2 ? 'is-current' : '' }}">
                            <span class="progress-dot">{{ $step['done'] ? '✓' : $index + 1 }}</span>
                            <div><strong>{{ $step['label'] }}</strong><small>{{ $index === 0 ? 'Request received' : ($index === 1 ? 'Staff review and processing' : ($index === 2 ? 'Information may be needed' : 'Service completed')) }}</small></div>
                        </div>
                        @if(!$loop->last)<span class="progress-line"></span>@endif
                    @endforeach
                </div>
            </section>

            <section class="admin-card">
                <div class="admin-card-head">
                    <div><span class="admin-kicker">CUSTOMER</span><h3>Customer information</h3></div>
                    <button type="button" class="text-button" onclick="document.getElementById('edit-details').scrollIntoView({behavior:'smooth'})">Edit details</button>
                </div>
                <div class="customer-summary">
                    <div class="customer-avatar">{{ strtoupper(substr($submission->customer_name ?: 'C', 0, 1)) }}</div>
                    <div><strong>{{ $submission->customer_name ?: 'Unnamed customer' }}</strong><p>{{ $submission->customer_email ?: 'No email provided' }}</p></div>
                    <a href="tel:{{ $submission->customer_phone }}">{{ $submission->customer_phone ?: 'No phone' }}</a>
                </div>
                <div class="info-grid">
                    <div><span>Email</span><strong>{{ $submission->customer_email ?: 'Not provided' }}</strong></div>
                    <div><span>Phone</span><strong>{{ $submission->customer_phone ?: 'Not provided' }}</strong></div>
                    <div><span>Preferred date</span><strong>{{ $submission->preferred_date?->format('d M Y') ?? 'Not specified' }}</strong></div>
                    <div><span>Total price</span><strong>{{ $submission->total_price !== null ? number_format((float)$submission->total_price, 2) : 'Not set' }}</strong></div>
                </div>
            </section>

            <section class="admin-card">
                <div class="admin-card-head">
                    <div><span class="admin-kicker">APPLICATION DATA</span><h3>Submitted information</h3></div>
                    <span class="muted-count">{{ $detailValues->count() }} fields</span>
                </div>
                @if($detailValues->isNotEmpty())
                    <div class="submitted-fields">
                        @foreach($detailValues as $value)
                            <div class="submitted-field">
                                <span>{{ $value->field->label ?? $value->field_key }}</span>
                                <strong>{{ $value->getValueForDisplay() ?: 'Not provided' }}</strong>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">No dynamic service fields were submitted.</div>
                @endif
            </section>

            <section class="admin-card">
                <div class="admin-card-head">
                    <div><span class="admin-kicker">DOCUMENTS</span><h3>Uploaded files</h3></div>
                    <span class="muted-count">{{ $fileValues->count() }} files</span>
                </div>
                @if($fileValues->isNotEmpty())
                    <div class="document-grid">
                        @foreach($fileValues as $value)
                            <div class="document-card">
                                <div class="document-icon">DOC</div>
                                <div class="document-copy"><strong>{{ $value->field->label ?? 'Uploaded document' }}</strong><span>{{ $value->display_value }}{{ $value->file_size ? ' · '.$value->file_size : '' }}</span></div>
                                <a class="document-action" href="{{ route('admin.submissions.files.download', [$submission, $value]) }}">Download</a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">No documents uploaded with this request.</div>
                @endif
            </section>

            <section class="admin-card submission-activity">
                <div class="admin-card-head">
                    <div><span class="admin-kicker">ACTIVITY</span><h3>Request history</h3></div>
                    <span class="muted-count">{{ $submission->activities->count() }} events</span>
                </div>
                @if($submission->activities->isNotEmpty())
                    <div class="activity-list">
                        @foreach($submission->activities as $activity)
                            <article class="activity-item">
                                <div class="activity-rail"><span class="activity-dot"></span></div>
                                <div class="activity-content">
                                    <strong>{{ $activity->title }}</strong>
                                    @if($activity->description)<p>{{ $activity->description }}</p>@endif
                                    <time>{{ $activity->created_at?->format('d M Y, H:i') }} @if($activity->user)<span>· <span class="activity-actor">{{ $activity->user->name }}</span></span>@else<span>· Customer</span>@endif</time>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">No activity has been recorded yet.</div>
                @endif
            </section>

            <section class="admin-card" id="edit-details">
                <div class="admin-card-head"><div><span class="admin-kicker">INTERNAL</span><h3>Staff notes & details</h3></div></div>
                <form method="POST" action="{{ route('admin.submissions.update', $submission) }}" class="admin-edit-form">
                    @csrf @method('PUT')
                    <div class="form-grid-2">
                        <label>Customer name<input name="customer_name" value="{{ $submission->customer_name }}"></label>
                        <label>Phone<input name="customer_phone" value="{{ $submission->customer_phone }}"></label>
                        <label>Email<input type="email" name="customer_email" value="{{ $submission->customer_email }}"></label>
                        <label>Preferred date<input type="date" name="preferred_date" value="{{ $submission->preferred_date?->format('Y-m-d') }}"></label>
                        <label>Total price<input type="number" step="0.01" min="0" name="total_price" value="{{ $submission->total_price }}"></label>
                    </div>
                    <label>Internal staff notes<textarea name="staff_notes" rows="5" placeholder="Record internal processing notes, follow-ups or handover details...">{{ $submission->staff_notes }}</textarea></label>
                    <button class="button button-blue" type="submit">Save changes</button>
                </form>
            </section>

            @if($submission->customer_notes)
                <section class="admin-card">
                    <div class="admin-card-head"><div><span class="admin-kicker">CUSTOMER MESSAGE</span><h3>Notes submitted by customer</h3></div></div>
                    <div class="customer-note">{{ $submission->customer_notes }}</div>
                </section>
            @endif
        </main>

        <aside class="request-sidebar">
            <section class="admin-card action-card">
                <div class="admin-card-head"><div><span class="admin-kicker">ASSIGNMENT</span><h3>Owner</h3></div></div>
                <form method="POST" action="{{ route('admin.submissions.assign', $submission) }}">
                    @csrf
                    <select name="staff_id" required>
                        <option value="">Choose staff member...</option>
                        @foreach($staff as $person)
                            <option value="{{ $person->id }}" @selected($submission->processed_by === $person->id)>{{ $person->name }} · {{ $person->role_label }}</option>
                        @endforeach
                    </select>
                    <button class="button button-blue button-wide" type="submit">Assign request</button>
                </form>
            </section>

            <section class="admin-card action-card">
                <div class="admin-card-head"><div><span class="admin-kicker">NEXT ACTION</span><h3>Update status</h3></div></div>
                <div class="action-stack">
                    <form method="POST" action="{{ route('admin.submissions.in-progress', $submission) }}">@csrf<button class="workflow-action" type="submit"><span class="action-symbol blue">↗</span><span><strong>Start processing</strong><small>Move this request into active work.</small></span></button></form>
                    <form method="POST" action="{{ route('admin.submissions.awaiting-customer', $submission) }}" class="await-form">@csrf<input type="hidden" name="reason" value="Additional information is required to continue processing this request."><button class="workflow-action" type="submit"><span class="action-symbol amber">?</span><span><strong>Await customer</strong><small>Pause while information is needed.</small></span></button></form>
                    <form method="POST" action="{{ route('admin.submissions.complete', $submission) }}">@csrf<button class="workflow-action" type="submit"><span class="action-symbol green">✓</span><span><strong>Mark completed</strong><small>Close the request as completed.</small></span></button></form>
                </div>
            </section>

            <section class="admin-card action-card danger-card">
                <div class="admin-card-head"><div><span class="admin-kicker">EXCEPTION</span><h3>Reject request</h3></div></div>
                <form method="POST" action="{{ route('admin.submissions.reject', $submission) }}" class="reject-form">
                    @csrf
                    <textarea name="reason" rows="3" placeholder="Reason for rejection..."></textarea>
                    <button class="button button-danger button-wide" type="submit">Reject request</button>
                </form>
            </section>

            <section class="admin-card meta-card">
                <div class="admin-card-head"><div><span class="admin-kicker">REQUEST DETAILS</span><h3>Record</h3></div></div>
                <dl>
                    <div><dt>Reference</dt><dd>{{ $submission->reference_number }}</dd></div>
                    <div><dt>Service</dt><dd>{{ $submission->service->name ?? '—' }}</dd></div>
                    <div><dt>Created</dt><dd>{{ $submission->created_at->format('d M Y, H:i') }}</dd></div>
                    <div><dt>Updated</dt><dd>{{ $submission->updated_at->format('d M Y, H:i') }}</dd></div>
                    @if($submission->completed_at)<div><dt>Completed</dt><dd>{{ $submission->completed_at->format('d M Y, H:i') }}</dd></div>@endif
                </dl>
            </section>
        </aside>
    </div>
</div>
@endsection
