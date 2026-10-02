@extends('layouts.admin')
@section('page_title', 'Application '.$submission->reference_number)
@section('content')
@php
    $statusClass = match ($submission->status) {
        \App\Models\Submission::STATUS_COMPLETED => 'success',
        \App\Models\Submission::STATUS_REJECTED => 'danger',
        \App\Models\Submission::STATUS_IN_PROGRESS => 'info',
        \App\Models\Submission::STATUS_AWAITING_CUSTOMER => 'secondary',
        default => 'warning',
    };

    $serviceFields = $submission->service?->allFields?->sortBy('sort_order')->values() ?? collect();
    $activeServiceFields = $submission->service?->fields?->sortBy('sort_order')->values() ?? collect();
    $valuesByField = $submission->values->keyBy('service_field_id');
    $recordedCount = $activeServiceFields->filter(fn ($field) => $valuesByField->has($field->id))->count();
    $providedCount = $activeServiceFields->filter(function ($field) use ($valuesByField) {
        $value = $valuesByField->get($field->id);
        return $value && ($value->isFile() ? $value->hasFile() : filled($value->value));
    })->count();
    $requiredMissingCount = $activeServiceFields->where('is_required', true)->filter(function ($field) use ($valuesByField) {
        $value = $valuesByField->get($field->id);
        return ! $value || ($value->isFile() ? ! $value->hasFile() : blank($value->value));
    })->count();
    $fileValues = $serviceFields->map(fn ($field) => $valuesByField->get($field->id))
        ->filter(fn ($value) => $value && $value->isFile() && $value->hasFile())
        ->values();

    $steps = [
        ['label' => 'Received', 'caption' => 'Request received', 'complete' => true, 'current' => $submission->status === \App\Models\Submission::STATUS_PENDING],
        ['label' => 'In Progress', 'caption' => 'Staff review & processing', 'complete' => in_array($submission->status, [\App\Models\Submission::STATUS_IN_PROGRESS, \App\Models\Submission::STATUS_AWAITING_CUSTOMER, \App\Models\Submission::STATUS_COMPLETED], true), 'current' => $submission->status === \App\Models\Submission::STATUS_IN_PROGRESS],
        ['label' => 'Customer Action', 'caption' => 'Information may be needed', 'complete' => in_array($submission->status, [\App\Models\Submission::STATUS_AWAITING_CUSTOMER, \App\Models\Submission::STATUS_COMPLETED], true), 'current' => $submission->status === \App\Models\Submission::STATUS_AWAITING_CUSTOMER],
        ['label' => 'Completed', 'caption' => 'Service completed', 'complete' => $submission->status === \App\Models\Submission::STATUS_COMPLETED, 'current' => $submission->status === \App\Models\Submission::STATUS_COMPLETED],
    ];
@endphp

<div class="dsc-request-page">
    <div class="dsc-request-topbar">
        <div class="dsc-request-crumbs">
            <a href="{{ route('admin.submissions.index') }}">Requests</a><span>/</span><strong>{{ $submission->reference_number }}</strong>
        </div>
        <div class="dsc-request-actions">
            <a class="dsc-request-secondary" href="{{ route('public.track.show', ['reference' => $submission->reference_number]) }}" target="_blank" rel="noopener">Customer view <span>↗</span></a>
            <a class="dsc-request-secondary" href="{{ route('admin.submissions.print', $submission) }}" target="_blank" rel="noopener">Print / Save PDF <span>↗</span></a>
        </div>
    </div>

    <section class="dsc-request-hero">
        <div class="dsc-request-hero-main">
            <span class="dsc-request-overline">{{ $submission->service?->category?->full_path ?? $submission->service?->category?->name ?? 'Service request' }}</span>
            <div class="dsc-request-title-line">
                <h2>{{ $submission->service->name ?? 'Service request' }}</h2>
                <span class="badge {{ $statusClass }}">{{ $submission->status_label }}</span>
            </div>
            <p>Reference <strong>{{ $submission->reference_number }}</strong><span>•</span> Submitted {{ $submission->created_at?->format('d M Y, H:i') }}</p>
        </div>
        <div class="dsc-request-hero-stats">
            <div><span>REQUEST OWNER</span><strong>{{ $submission->processedBy->name ?? 'Unassigned' }}</strong></div>
            <div><span>PAYMENT</span><strong>{{ ucfirst($submission->payment_status ?? 'Pending') }}</strong></div>
            <div><span>TOTAL</span><strong>{{ $submission->total_price !== null ? number_format((float)$submission->total_price, 2).' TZS' : 'Not set' }}</strong></div>
        </div>
    </section>

    <section class="dsc-workflow-card">
        <div class="dsc-card-title-row">
            <div><span class="dsc-card-kicker">WORKFLOW</span><h3>Application progress</h3></div>
            <span class="dsc-card-state">{{ $submission->status_label }}</span>
        </div>
        <div class="dsc-workflow">
            @foreach($steps as $step)
                <div class="dsc-workflow-step {{ !empty($step['complete']) ? 'is-complete' : '' }} {{ !empty($step['current']) ? 'is-current' : '' }}">
                    <span class="dsc-workflow-dot">{{ !empty($step['complete']) && empty($step['current']) ? '✓' : $loop->iteration }}</span>
                    <div><strong>{{ $step['label'] }}</strong><small>{{ $step['caption'] }}</small></div>
                </div>
                @if(!$loop->last)<div class="dsc-workflow-line"></div>@endif
            @endforeach
        </div>
    </section>

    <div class="dsc-request-layout">
        <main class="dsc-request-main">
            <section class="dsc-panel dsc-customer-panel">
                <div class="dsc-card-title-row">
                    <div><span class="dsc-card-kicker">CUSTOMER SUBMISSION</span><h3>Information received from customer</h3></div>
                    <div class="dsc-answer-summary"><strong>{{ $providedCount }}</strong><span>of {{ $activeServiceFields->count() }} current fields provided</span><em>{{ $recordedCount }}/{{ $activeServiceFields->count() }} records</em></div>
                </div>

                <div class="dsc-customer-grid">
                    <div class="dsc-info-card">
                        <div class="dsc-info-card-head"><span class="dsc-info-icon">◎</span><div><strong>Customer details</strong><small>Information entered at checkout</small></div></div>
                        <div class="dsc-info-rows">
                            <div><span>Full name</span><strong>{{ $submission->customer_name ?: 'Not provided' }}</strong></div>
                            <div><span>Phone</span><strong>{{ $submission->customer_phone ?: 'Not provided' }}</strong></div>
                            <div><span>Email</span><strong>{{ $submission->customer_email ?: 'Not provided' }}</strong></div>
                            <div><span>Preferred date</span><strong>{{ $submission->preferred_date?->format('d M Y') ?? 'Not specified' }}</strong></div>
                            <div><span>Reference</span><strong>{{ $submission->reference_number }}</strong></div>
                        </div>
                    </div>

                    <div class="dsc-info-card">
                        <div class="dsc-info-card-head"><span class="dsc-info-icon is-gold">▣</span><div><strong>Request summary</strong><small>Operational information</small></div></div>
                        <div class="dsc-info-rows">
                            <div><span>Service</span><strong>{{ $submission->service->name ?? '—' }}</strong></div>
                            <div><span>Category</span><strong>{{ $submission->service?->category?->name ?? '—' }}</strong></div>
                            <div><span>Assigned to</span><strong>{{ $submission->processedBy->name ?? 'Unassigned' }}</strong></div>
                            <div><span>Payment</span><strong>{{ ucfirst($submission->payment_status ?? 'Pending') }}</strong></div>
                            <div><span>Total price</span><strong>{{ $submission->total_price !== null ? number_format((float)$submission->total_price, 2).' TZS' : 'Not set' }}</strong></div>
                        </div>
                    </div>
                </div>

                <div class="dsc-submitted-head">
                    <div><span class="dsc-card-kicker">SERVICE INFORMATION</span><h4>Exactly what the customer submitted</h4><p class="dsc-section-note">Every configured field is shown below. A missing value means the customer did not provide it in this request.</p></div>
                    <div class="dsc-missing-pill {{ $requiredMissingCount ? 'has-missing' : 'is-good' }}">{{ $requiredMissingCount }} required missing</div>
                </div>

                @if($recordedCount < $activeServiceFields->count())
                    <div class="dsc-data-alert">
                        <div><strong>Some field records are missing</strong>{{ $activeServiceFields->count() - $recordedCount }} current field definition(s) do not have a stored answer row for this request. The page still shows the complete service schema so staff can see exactly what information the service requires.</div>
                    </div>
                @endif

                @if($serviceFields->isNotEmpty())
                    <div class="dsc-answer-list">
                        @foreach($serviceFields as $field)
                            @php
                                $value = $valuesByField->get($field->id);
                                $hasValue = $value && ($value->isFile() ? $value->hasFile() : filled($value->value));
                                $display = $value ? $value->getValueForDisplay() : null;
                            @endphp
                            <div class="dsc-answer-row {{ $hasValue ? 'has-value' : 'is-missing' }}">
                                <div class="dsc-answer-label"><strong>{{ $value?->field_label_snapshot ?: $field->label }}</strong>@if($field->is_required)<span>Required</span>@endif @if(!$field->is_active)<em>Archived field</em>@endif</div>
                                <div class="dsc-answer-value">
                                    @if($hasValue)
                                        @if($value->isFile())
                                            <a class="dsc-inline-file" href="{{ route('admin.submissions.files.download', [$submission, $value]) }}"><span>{{ strtoupper(pathinfo($display, PATHINFO_EXTENSION) ?: 'FILE') }}</span>{{ $display }} <b>↗</b></a>
                                        @else
                                            {{ $display ?: 'Blank' }}
                                        @endif
                                    @else
                                        <span class="dsc-not-provided">Not provided</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="dsc-empty-box">This service has no configured service-specific fields.</div>
                @endif

                <div class="dsc-customer-message">
                    <div><span class="dsc-info-icon is-message">✦</span><div><strong>Customer message</strong><small>Additional notes submitted with this request</small></div></div>
                    <p>{{ $submission->customer_notes ?: 'No additional message was submitted by the customer.' }}</p>
                </div>
            </section>

            <section class="dsc-panel">
                <div class="dsc-card-title-row">
                    <div><span class="dsc-card-kicker">DOCUMENTS</span><h3>Uploaded documents</h3></div>
                    <span class="dsc-card-state">{{ $fileValues->count() }} files</span>
                </div>
                @if($fileValues->isNotEmpty())
                    <div class="dsc-document-list">
                        @foreach($fileValues as $value)
                            <div class="dsc-document-row">
                                <div class="dsc-document-icon">{{ strtoupper(pathinfo($value->display_value, PATHINFO_EXTENSION) ?: 'FILE') }}</div>
                                <div class="dsc-document-copy"><strong>{{ $value->field->label ?? 'Uploaded document' }}</strong><span>{{ $value->display_value }}{{ $value->file_size ? ' · '.$value->file_size : '' }}</span></div>
                                <a href="{{ route('admin.submissions.files.download', [$submission, $value]) }}">Download <span>↓</span></a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="dsc-empty-box">No documents were uploaded with this request.</div>
                @endif
            </section>

            <section class="dsc-panel">
                <div class="dsc-card-title-row"><div><span class="dsc-card-kicker">ACTIVITY</span><h3>Request history</h3></div><span class="dsc-card-state">{{ $submission->activities->count() }} events</span></div>
                @if($submission->activities->isNotEmpty())
                    <div class="dsc-activity-list">
                        @foreach($submission->activities as $activity)
                            <article class="dsc-activity-row">
                                <div class="dsc-activity-marker"></div>
                                <div><strong>{{ $activity->title }}</strong>@if($activity->description)<p>{{ $activity->description }}</p>@endif<small>{{ $activity->created_at?->format('d M Y, H:i') }} <span>·</span> {{ $activity->user->name ?? 'Customer' }}</small></div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="dsc-empty-box">No activity has been recorded yet.</div>
                @endif
            </section>

            <section class="dsc-panel dsc-internal-panel" id="edit-details">
                <div class="dsc-card-title-row"><div><span class="dsc-card-kicker">INTERNAL</span><h3>Staff workspace</h3></div><span class="dsc-card-state">Private</span></div>
                <form method="POST" action="{{ route('admin.submissions.update', $submission) }}" class="dsc-internal-form">
                    @csrf @method('PUT')
                    <div class="dsc-field-grid">
                        <label><span>Customer name</span><input name="customer_name" value="{{ $submission->customer_name }}"></label>
                        <label><span>Phone</span><input name="customer_phone" value="{{ $submission->customer_phone }}"></label>
                        <label><span>Email</span><input type="email" name="customer_email" value="{{ $submission->customer_email }}"></label>
                        <label><span>Preferred date</span><input type="date" name="preferred_date" value="{{ $submission->preferred_date?->format('Y-m-d') }}"></label>
                        <label><span>Total price (TZS)</span><input type="number" step="0.01" min="0" name="total_price" value="{{ $submission->total_price }}"></label>
                        <label class="full"><span>Internal staff notes</span><textarea name="staff_notes" rows="5" placeholder="Add private processing notes, follow-ups or handover details...">{{ $submission->staff_notes }}</textarea></label>
                    </div>
                    <button class="dsc-primary-action" type="submit">Save changes</button>
                </form>
            </section>
        </main>

        <aside class="dsc-request-sidebar">
            <section class="dsc-side-card">
                <span class="dsc-side-kicker">ASSIGNMENT</span><h3>Request owner</h3>
                <form method="POST" action="{{ route('admin.submissions.assign', $submission) }}" class="dsc-side-form">
                    @csrf
                    <select name="staff_id" required><option value="">Choose staff member…</option>@foreach($staff as $person)<option value="{{ $person->id }}" @selected($submission->processed_by === $person->id)>{{ $person->name }} · {{ $person->role_label }}</option>@endforeach</select>
                    <button class="dsc-primary-action" type="submit">Assign request</button>
                </form>
            </section>

            <section class="dsc-side-card">
                <span class="dsc-side-kicker">NEXT ACTION</span><h3>Update status</h3>
                <div class="dsc-side-actions">
                    <form method="POST" action="{{ route('admin.submissions.in-progress', $submission) }}">@csrf<button type="submit"><i class="is-blue">↗</i><span><strong>Start processing</strong><small>Move into active work.</small></span></button></form>
                    <form method="POST" action="{{ route('admin.submissions.awaiting-customer', $submission) }}">@csrf<input type="hidden" name="reason" value="Additional information is required to continue processing this request."><button type="submit"><i class="is-gold">?</i><span><strong>Await customer</strong><small>Pause until information arrives.</small></span></button></form>
                    <form method="POST" action="{{ route('admin.submissions.complete', $submission) }}">@csrf<button type="submit"><i class="is-green">✓</i><span><strong>Mark completed</strong><small>Close the request.</small></span></button></form>
                </div>
            </section>

            <section class="dsc-side-card is-danger">
                <span class="dsc-side-kicker">EXCEPTION</span><h3>Reject request</h3>
                <form method="POST" action="{{ route('admin.submissions.reject', $submission) }}" class="dsc-side-form">@csrf<textarea name="reason" rows="4" placeholder="Reason for rejection…"></textarea><button class="dsc-danger-action" type="submit">Reject request</button></form>
            </section>

            <section class="dsc-side-card">
                <span class="dsc-side-kicker">REQUEST DETAILS</span><h3>Record</h3>
                <dl class="dsc-record-list">
                    <div><dt>Reference</dt><dd>{{ $submission->reference_number }}</dd></div>
                    <div><dt>Service</dt><dd>{{ $submission->service->name ?? '—' }}</dd></div>
                    <div><dt>Created</dt><dd>{{ $submission->created_at?->format('d M Y, H:i') }}</dd></div>
                    <div><dt>Updated</dt><dd>{{ $submission->updated_at?->format('d M Y, H:i') }}</dd></div>
                    @if($submission->completed_at)<div><dt>Completed</dt><dd>{{ $submission->completed_at->format('d M Y, H:i') }}</dd></div>@endif
                </dl>
            </section>
        </aside>
    </div>
</div>
@endsection
