@extends('layouts.guest')

@section('title', 'Register | Digital Star Consultants')

@section('content')
<div class="logo">
    <span class="mark">DS</span>
    <strong>Digital Star</strong>
</div>
<h1>Create account</h1>
<p class="sub">Choose your email and password to join as staff.</p>

@if ($errors->any())
    <div class="err">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if (session('success'))
    <div class="ok">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf
    <label for="name">Full name</label>
    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">

    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">

    <label for="password">Password</label>
    <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password">

    <label for="password_confirmation">Confirm password</label>
    <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">

    <button class="btn" type="submit">Create account</button>
</form>

<p class="foot">
    Already have an account? <a href="{{ route('login') }}">Sign in</a>
</p>
@endsection
