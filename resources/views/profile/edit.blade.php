@extends('layouts.admin')

@section('title', 'Profile | Digital Star Consultants')
@section('heading', 'My profile')

@section('content')
<style>
    .profile-card{max-width:520px;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:24px}
    .profile-row{display:flex;align-items:center;gap:16px;margin-bottom:20px}
    .profile-avatar{width:72px;height:72px;border-radius:999px;object-fit:cover;background:linear-gradient(135deg,#3b82f6,#1e3a8a);color:#fff;display:grid;place-items:center;font-weight:700;font-size:22px;border:3px solid #e2e8f0}
    .profile-avatar img{width:100%;height:100%;border-radius:999px;object-fit:cover}
    label{display:block;font-size:13px;font-weight:600;margin-bottom:6px}
    input[type=file]{width:100%;font-size:14px;margin-bottom:12px}
    .btn{display:inline-flex;border:0;border-radius:10px;padding:10px 16px;background:#2563eb;color:#fff;font-weight:600;cursor:pointer;font-size:14px}
    .btn-ghost{background:#fff;color:#0f172a;border:1px solid #e2e8f0}
    .meta{color:#64748b;font-size:14px;margin:0}
    .actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:8px}
</style>

<div class="profile-card">
    <div class="profile-row">
        <div class="profile-avatar">
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
            @else
                {{ $user->initials() }}
            @endif
        </div>
        <div>
            <strong style="display:block;font-size:18px">{{ $user->name }}</strong>
            <p class="meta">{{ $user->email }}</p>
            <p class="meta">{{ $user->role_label }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
        @csrf
        <label for="avatar">Upload profile photo</label>
        <input id="avatar" type="file" name="avatar" accept="image/jpeg,image/png,image/webp" required>
        @error('avatar')
            <p style="color:#b91c1c;font-size:13px;margin:0 0 8px">{{ $message }}</p>
        @enderror
        <div class="actions">
            <button class="btn" type="submit">Save photo</button>
        </div>
    </form>

    @if ($user->avatar_path)
        <form method="POST" action="{{ route('profile.avatar.remove') }}" style="margin-top:12px">
            @csrf
            @method('DELETE')
            <button class="btn btn-ghost" type="submit">Remove photo</button>
        </form>
    @endif
</div>
@endsection
