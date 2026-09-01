@extends('layouts.admin')
@section('page_title','Settings')
@section('content')
<div class="settings-page">
    <div class="admin-page-intro settings-intro">
        <div>
            <span class="admin-kicker">SYSTEM CONFIGURATION</span>
            <h2>Keep the operation aligned.</h2>
            <p>Manage the business details, request defaults, customer-facing visibility and notification behavior used across Digital Star.</p>
        </div>
        <div class="settings-save-note"><span class="settings-pulse"></span> Changes apply immediately</div>
    </div>

    @if(session('success'))
        <div class="settings-alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="settings-alert error"><strong>Review the highlighted fields.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="settings-form">
        @csrf @method('PUT')
        <section class="settings-panel">
            <div class="settings-panel-head"><div><span class="admin-kicker">BUSINESS PROFILE</span><h3>Company details</h3><p>These are the core details the operation uses for identity and customer support.</p></div><span class="settings-section-icon">✦</span></div>
            <div class="settings-grid two">
                <label>Company name<input name="company_name" value="{{ old('company_name', $settings['company.name']->castValue()) }}" required></label>
                <label>Tagline<input name="company_tagline" value="{{ old('company_tagline', $settings['company.tagline']->castValue()) }}"></label>
                <label>Email address<input type="email" name="company_email" value="{{ old('company_email', $settings['company.email']->castValue()) }}"></label>
                <label>Phone number<input name="company_phone" value="{{ old('company_phone', $settings['company.phone']->castValue()) }}"></label>
                <label>WhatsApp number<input name="company_whatsapp" value="{{ old('company_whatsapp', $settings['company.whatsapp']->castValue()) }}"></label>
                <label>Business hours<input name="company_hours" value="{{ old('company_hours', $settings['company.hours']->castValue()) }}"></label>
                <label class="full">Business address<textarea name="company_address" rows="3">{{ old('company_address', $settings['company.address']->castValue()) }}</textarea></label>
            </div>
        </section>

        <div class="settings-columns">
            <section class="settings-panel">
                <div class="settings-panel-head"><div><span class="admin-kicker">OPERATIONS</span><h3>Request defaults</h3><p>Small controls that keep the workflow consistent.</p></div><span class="settings-section-icon">◫</span></div>
                <div class="settings-grid">
                    <label>Currency code<input name="currency" value="{{ old('currency', $settings['operations.currency']->castValue()) }}" maxlength="3" required><small>Used for future financial records and reporting.</small></label>
                    <label>Reference prefix<input name="reference_prefix" value="{{ old('reference_prefix', $settings['operations.reference_prefix']->castValue()) }}" maxlength="12" required><small>Example: DSC-000123.</small></label>
                </div>
                <label class="toggle-row"><span><strong>Customer document uploads</strong><small>Allow customers to submit supporting files during an application.</small></span><input type="checkbox" name="customer_uploads_enabled" value="1" {{ $settings['operations.customer_uploads_enabled']->castValue() ? 'checked' : '' }}></label>
            </section>

            <section class="settings-panel">
                <div class="settings-panel-head"><div><span class="admin-kicker">NOTIFICATIONS</span><h3>Operational alerts</h3><p>Choose which events should surface for the team.</p></div><span class="settings-section-icon">◔</span></div>
                <label class="toggle-row"><span><strong>New submission</strong><small>Alert the team when a customer submits a new request.</small></span><input type="checkbox" name="notify_new_submission" value="1" {{ $settings['notifications.new_submission']->castValue() ? 'checked' : '' }}></label>
                <label class="toggle-row"><span><strong>Status changes</strong><small>Record and surface workflow status changes.</small></span><input type="checkbox" name="notify_status_change" value="1" {{ $settings['notifications.status_change']->castValue() ? 'checked' : '' }}></label>
                <label class="toggle-row"><span><strong>Assignment alerts</strong><small>Surface when a request is assigned to a team member.</small></span><input type="checkbox" name="notify_assignment" value="1" {{ $settings['notifications.assignment']->castValue() ? 'checked' : '' }}></label>
            </section>
        </div>

        <section class="settings-panel">
            <div class="settings-panel-head"><div><span class="admin-kicker">PUBLIC WEBSITE</span><h3>Customer-facing visibility</h3><p>Control which business contact details are safe to show publicly.</p></div><span class="settings-section-icon">◉</span></div>
            <div class="visibility-grid">
                <label class="visibility-card"><input type="checkbox" name="show_phone" value="1" {{ $settings['public.show_phone']->castValue() ? 'checked' : '' }}><span><strong>Phone</strong><small>Show the business phone in customer-facing contact surfaces.</small></span></label>
                <label class="visibility-card"><input type="checkbox" name="show_address" value="1" {{ $settings['public.show_address']->castValue() ? 'checked' : '' }}><span><strong>Address</strong><small>Show the business address on public contact surfaces.</small></span></label>
                <label class="visibility-card"><input type="checkbox" name="show_business_hours" value="1" {{ $settings['public.show_business_hours']->castValue() ? 'checked' : '' }}><span><strong>Business hours</strong><small>Show office hours where customer support information appears.</small></span></label>
            </div>
        </section>

        <section class="settings-danger">
            <div><span class="admin-kicker">SYSTEM STATE</span><h3>Protected controls</h3><p>Security-sensitive infrastructure settings such as application key, database credentials, mail transport and production maintenance mode remain outside the web UI. Manage those at the server/environment level.</p></div>
            <span class="protected-badge">Server controlled</span>
        </section>

        <div class="settings-actions"><a class="button button-light" href="{{ route('admin.dashboard') }}">Cancel</a><button class="button button-yellow" type="submit">Save settings <span>↗</span></button></div>
    </form>
</div>
@endsection
