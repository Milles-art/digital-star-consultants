@extends('layouts.guest')

@section('title', 'Forgot password | Digital Star Consultants')

@section('content')
<div class="logo">
    <span class="mark">DS</span>
    <strong>Digital Star</strong>
</div>
<h1>Forgot password</h1>
<p class="sub">Enter your email and we’ll send a reset link if the account exists.</p>

@if ($errors->any())
    <div class="err">
        @foreach ($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

@if (session('success') || session('status'))
    <div class="ok">{{ session('success') ?? session('status') }}</div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf
    <label for="email">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">

    <button class="btn" type="submit">Send reset link</button>
</form>

<p class="foot">
    <a href="{{ route('login') }}">Back to login</a>
</p>
@endsection
