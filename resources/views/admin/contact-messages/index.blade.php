@extends('layouts.admin')
@section('page_title','Contact messages')
@section('content')
<div class="ds-message-page">
<section class="admin-panel ds-message-hero"><div><span class="eyebrow">CUSTOMER COMMUNICATION</span><h2>Messages from your website</h2><p>Review customer enquiries, contact details and message history from one inbox.</p></div></section>
@if(!empty($schemaMissing))<div class="ds-alert ds-alert-warning">The contact messages table is not migrated yet. Run <code>php artisan migrate</code> and refresh.</div>@endif
<section class="admin-panel ds-message-panel">
<form class="filter-row" method="GET"><input name="search" value="{{ request('search') }}" placeholder="Search name, email, subject, phone or message…"><button class="button button-yellow" type="submit">Search</button>@if(request('search'))<a class="button button-outline" href="{{ route('admin.contact-messages.index') }}">Clear</a>@endif</form>
<div class="table-wrap"><table><thead><tr><th>Sender</th><th>Subject</th><th>Message</th><th>Status</th><th>Received</th></tr></thead><tbody>
@forelse($messages as $m)
<tr><td><strong>{{ $m->name }}</strong><small>{{ $m->email }}@if($m->phone)<br>{{ $m->phone }}@endif</small></td><td>{{ $m->subject ?: 'General enquiry' }}</td><td><a href="{{ route('admin.contact-messages.show',$m) }}">{{ \Illuminate\Support\Str::limit($m->message,100) }} →</a></td><td><span class="status-chip">{{ $m->read_at ? 'Read' : 'Unread' }}</span></td><td>{{ $m->created_at?->format('d M Y H:i') }}</td></tr>
@empty
<tr><td colspan="5"><div class="ds-empty"><strong>No contact messages found.</strong><span>New enquiries will appear here when customers use the public contact form.</span></div></td></tr>
@endforelse
</tbody></table></div>
{{ $messages->withQueryString()->links() }}
</section></div>
@push('styles')<style>
.ds-message-page{display:grid;gap:18px}.ds-message-hero h2{margin:6px 0 8px}.ds-message-hero p{margin:0;color:#334155;font-size:15px;line-height:1.65}.ds-message-panel{padding:20px}.ds-message-panel td small{display:block;margin-top:4px;color:#64748b}.ds-empty{padding:30px;display:grid;gap:7px}.ds-empty span{color:#64748b}.ds-alert{padding:14px 16px;border-radius:14px;font-size:14px}.ds-alert-warning{background:#fff7ed;border:1px solid #fed7aa;color:#7c2d12}@media(max-width:700px){.ds-message-panel{padding:14px}.filter-row{display:grid;grid-template-columns:1fr}.filter-row .button{width:100%}}
</style>@endpush
@endsection