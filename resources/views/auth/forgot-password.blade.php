@extends('layouts.app')

@section('content')
<section class="auth-page">
    <div class="auth-card">
        <div class="eyebrow">ACCOUNT SECURITY</div>
        <h1>Reset your password.</h1>
        <p>Enter your account email. If the account exists, we will send a secure reset link.</p>
        @if(session('success'))
            <div class="form-message success" role="status">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="form-message error" role="alert">{{ $errors->first() }}</div>
        @endif
        <form id="forgot-form" method="POST" action="{{ route('password.email') }}">
            @csrf
            <label>Email address
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>
            </label>
            <button class="button button-yellow button-wide" type="submit">Send reset link →</button>
        </form>
        <p style="margin-top:16px"><a href="{{ route('login') }}">Back to sign in</a></p>
    </div>
</section>
@endsection
