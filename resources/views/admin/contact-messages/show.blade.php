@extends('layouts.admin', ['title' => 'Contact Message', 'eyebrow' => 'Inbox'])

@section('content')
    <section class="admin-panel reveal">
        <div class="admin-panel-header">
            <div>
                <p class="admin-kicker">{{ $message->created_at?->format('M d, Y H:i') }}</p>
                <h2 class="admin-panel-title">Message from {{ $message->name }}</h2>
            </div>
            <form method="POST" action="{{ route('admin.contact-messages.destroy', $message) }}" data-ajax data-success-redirect="{{ route('admin.contact-messages.index') }}" data-confirm="Delete this message?">
                @csrf
                @method('DELETE')
                <button class="admin-button admin-button-danger" type="submit">Delete</button>
            </form>
        </div>

        <div class="admin-detail-grid">
            <div><span>Name</span><strong>{{ $message->name }}</strong></div>
            <div><span>Email</span><strong><a class="admin-link" href="mailto:{{ $message->email }}">{{ $message->email }}</a></strong></div>
            <div><span>Status</span><strong>{{ $message->read_at ? 'Read' : 'Unread' }}</strong></div>
            <div><span>Received</span><strong>{{ $message->created_at?->format('M d, Y H:i') }}</strong></div>
        </div>

        <div class="admin-note">
            <p class="admin-kicker">Message</p>
            <p>{{ $message->message }}</p>
        </div>
    </section>
@endsection
