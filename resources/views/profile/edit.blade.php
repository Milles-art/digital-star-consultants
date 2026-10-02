@extends('layouts.digitalstar')

@section('title', 'My profile | Digital Star Consultants')

@section('content')
    @include('partials.ds-hero', [
        'kicker' => 'My profile',
        'title' => e($user->name),
        'lead' => e($user->email) . ' &middot; ' . e($user->role_label),
    ])

    <section class="ds-section">
        <div class="ds-container ds-narrow">
            <div class="ds-form-card ds-reveal">
                <div class="ds-profile-head">
                    @if (!empty($user->avatar_url))
                        <img class="ds-avatar" src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    @else
                        <span class="ds-avatar-fallback">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}</span>
                    @endif
                    <div>
                        <strong>{{ $user->name }}</strong>
                        <small>{{ $user->role_label }}</small>
                    </div>
                </div>

                @if (session('success'))
                    <div class="ds-alert ds-alert-success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="ds-alert ds-alert-error" role="alert">{{ $errors->first() }}</div>
                @endif

                <span class="ds-kicker">Profile photo</span>
                <h3 style="padding-top:8px;font-size:20px;font-weight:700">Update your photo</h3>
                <p style="padding-top:6px;color:var(--ds-muted-fg)">Use a clear, square image. JPG or PNG works best.</p>

                <form method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data" class="ds-form" style="margin-top:20px">
                    @csrf
                    <label class="ds-file">
                        <span class="ds-icon-box"><i data-lucide="image-up"></i></span>
                        <span style="min-width:0">
                            <strong>Choose image</strong>
                            <small data-file-name-for="avatar">Click to choose a photo</small>
                        </span>
                        <input id="avatar" type="file" name="avatar" accept="image/*" required>
                    </label>
                    <button class="ds-btn ds-btn-primary ds-btn-block" type="submit">Upload photo <i data-lucide="upload"></i></button>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('avatar');
    const label = document.querySelector('[data-file-name-for="avatar"]');
    input?.addEventListener('change', () => {
        if (input.files?.length) {
            label.textContent = `Selected: ${input.files[0].name}`;
            label.classList.add('ds-file-name');
        }
    });
});
</script>
@endpush
