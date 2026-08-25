@extends('layouts.guest')

@section('title', 'Reset password | Digital Star Consultants')

@section('content')
<div class="logo">
    <span class="mark">DS</span>
    <strong>Digital Star</strong>
</div>
<h1>Set new password</h1>
<p class="sub">Choose a strong password for your account.</p>

@if ($errors->any())
    <div class="err">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email', request('email')) }}" required autocomplete="username">

    <label for="password">New password</label>
    <input id="password" type="password" name="password" required autocomplete="new-password">

    <label for="password_confirmation">Confirm password</label>
    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">

    <button class="btn" type="submit">Reset password</button>
</form>

<p class="foot">
    <a href="{{ route('login') }}">Back to login</a>
</p>
@endsection
