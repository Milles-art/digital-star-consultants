@extends('layouts.admin')

@section('page_title', 'Notifications')

@section('content')
<div class="admin-page notifications-page">
    <div class="page-intro-row">
        <div>
            <span class="admin-kicker">TEAM INBOX</span>
            <h2>Notifications</h2>
            <p>Important operational updates, new service requests and activity that may need your attention.</p>
        </div>
        @if($unreadCount)
            <form method="POST" action="{{ route('admin.notifications.read-all') }}">@csrf<button class="button button-light" type="submit">Mark all as read</button></form>
        @endif
    </div>

    <section class="admin-card notification-summary">
        <div><strong>{{ $unreadCount }}</strong><span>Unread</span></div>
        <div><strong>{{ method_exists($notifications, 'total') ? $notifications->total() : 0 }}</strong><span>Total notifications</span></div>
    </section>

    <section class="notification-list">
        @forelse($notifications as $notification)
            @php($data = $notification->data)
            <article class="admin-card notification-item {{ $notification->read_at ? 'is-read' : 'is-unread' }}">
                <div class="notification-icon">{{ $notification->read_at ? '✓' : '!' }}</div>
                <div class="notification-copy">
                    <div class="notification-topline"><strong>{{ $data['title'] ?? 'Notification' }}</strong><time>{{ $notification->created_at?->diffForHumans() }}</time></div>
                    <p>{{ $data['message'] ?? 'You have a new notification.' }}</p>
                    @if(!empty($data['reference']))<span class="notification-ref">{{ $data['reference'] }}</span>@endif
                </div>
                @if(empty($notification->read_at))
                    <form method="POST" action="{{ route('admin.notifications.read', $notification->id) }}">@csrf<button class="text-button" type="submit">Open →</button></form>
                @elseif(!empty($data['url']))
                    <a class="text-button" href="{{ $data['url'] }}">View →</a>
                @endif
            </article>
        @empty
            <div class="admin-card empty-state">No notifications yet. New service activity will appear here.</div>
        @endforelse
    </section>

    {{ $notifications->links() }}
</div>
@endsection
