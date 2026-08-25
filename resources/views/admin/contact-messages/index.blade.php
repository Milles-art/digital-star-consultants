@extends('layouts.admin', ['title' => 'Contact Messages', 'eyebrow' => 'Inbox'])

@section('content')
    <section class="admin-panel reveal">
        <form method="GET" action="{{ route('admin.contact-messages.index') }}" class="admin-filter-grid">
            <input class="admin-field" type="search" name="search" value="{{ request('search') }}" placeholder="Search name, email, or message">
            <button class="admin-button admin-button-dark" type="submit">Search</button>
            <a class="admin-button admin-button-muted" href="{{ route('admin.contact-messages.index') }}">Reset</a>
        </form>
    </section>

    <section class="admin-panel reveal-delay">
        <div class="admin-panel-header">
            <div>
                <p class="admin-kicker">Messages</p>
                <h2 class="admin-panel-title">{{ number_format($messages->total()) }} conversations</h2>
            </div>
        </div>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead><tr><th>Sender</th><th>Preview</th><th>Status</th><th>Received</th><th></th></tr></thead>
                <tbody>
                    @forelse ($messages as $message)
                        <tr>
                            <td><span class="block font-bold text-ink">{{ $message->name }}</span><span class="text-xs text-muted">{{ $message->email }}</span></td>
                            <td>{{ \Illuminate\Support\Str::limit($message->message, 90) }}</td>
                            <td><span class="admin-badge {{ $message->read_at ? 'is-secondary' : 'is-warning' }}">{{ $message->read_at ? 'Read' : 'Unread' }}</span></td>
                            <td>{{ $message->created_at?->format('M d, Y H:i') }}</td>
                            <td class="text-right"><a class="admin-button admin-button-muted" href="{{ route('admin.contact-messages.show', $message) }}">Open</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5">@include('admin.partials.empty', ['message' => 'No contact messages found.'])</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $messages->links() }}</div>
    </section>
@endsection
