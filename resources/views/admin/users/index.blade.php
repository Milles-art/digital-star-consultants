@extends('layouts.admin', ['title' => 'Users', 'eyebrow' => 'Team'])

@section('content')
    <section class="admin-stat-grid reveal">
        @foreach ([['Total', $stats['total']], ['Active', $stats['active']], ['Staff', $stats['staff']], ['Management', $stats['management']]] as [$label, $value])
            <article class="admin-stat-card">
                <span class="admin-stat-dot is-blue"></span>
                <p class="text-sm font-semibold text-muted">{{ $label }}</p>
                <p class="mt-4 text-3xl font-black text-ink">{{ number_format($value) }}</p>
            </article>
        @endforeach
    </section>

    <div class="admin-two-column">
        <section class="admin-panel reveal-delay">
            <div class="admin-panel-header">
                <div>
                    <p class="admin-kicker">Accounts</p>
                    <h2 class="admin-panel-title">Team access</h2>
                </div>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Last login</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($users as $member)
                            <tr>
                                <td class="font-bold text-ink">{{ $member->name }}</td>
                                <td>{{ $member->email }}</td>
                                <td>{{ $member->role_label }}</td>
                                <td><span class="admin-badge {{ $member->is_active ? 'is-success' : 'is-secondary' }}">{{ $member->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>{{ $member->last_login_at?->format('M d, Y H:i') ?? 'Never' }}</td>
                                <td class="text-right">
                                    <div class="admin-actions">
                                        <form method="POST" action="{{ route('admin.users.toggle-active', $member) }}" data-ajax data-success-reload>
                                            @csrf
                                            <button class="admin-button admin-button-muted" type="submit">{{ $member->is_active ? 'Deactivate' : 'Activate' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.users.destroy', $member) }}" data-ajax data-success-reload data-confirm="Delete this user?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="admin-button admin-button-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">@include('admin.partials.empty', ['message' => 'No users found.'])</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="admin-panel reveal-delay-2">
            <h2 class="admin-panel-title">Invite staff</h2>
            <form method="POST" action="{{ route('admin.users.store') }}" class="admin-form-stack" data-ajax data-success-reload>
                @csrf
                <label class="admin-label" for="name">Name</label>
                <input class="admin-field" id="name" name="name" required>
                <label class="admin-label" for="email">Email</label>
                <input class="admin-field" id="email" name="email" type="email" required>
                <label class="admin-label" for="role">Role</label>
                <select class="admin-field" id="role" name="role">
                    @foreach ($roles as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button class="admin-button admin-button-dark" type="submit">Create and email login</button>
            </form>
        </aside>
    </div>
@endsection
