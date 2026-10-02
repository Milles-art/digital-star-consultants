@extends('layouts.digitalstar')

@section('title', 'Forgot password | Digital Star Consultants')

@section('content')
<section class="ds-section">
    <div class="ds-container ds-narrow">
        <div class="ds-panel ds-reveal">
            <span class="ds-kicker">Account security</span>
            <h3 style="font-size:28px">Reset your password.</h3>
            <p>Enter your account email. If the account exists, we will send a secure reset link.</p>
            @if(session('success'))
                <div class="ds-alert ds-alert-success" role="status" style="margin-top:20px">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="ds-alert ds-alert-error" role="alert" style="margin-top:20px">{{ $errors->first() }}</div>
            @endif
            <form id="forgot-form" class="ds-form" style="margin-top:24px" method="POST" action="{{ route('password.email') }}">
                @csrf
                <label class="ds-field">Email address
                    <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required placeholder="you@example.com">
                </label>
                <button class="ds-btn ds-btn-primary ds-btn-block" style="height:48px" type="submit">Send reset link <i data-lucide="arrow-right"></i></button>
            </form>
            <p class="ds-help"><a href="{{ route('login') }}">← Back to sign in</a></p>
        </div>
    </div>
</section>
@endsection
