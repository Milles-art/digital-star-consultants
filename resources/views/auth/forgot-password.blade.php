@extends('layouts.app')
@section('title', 'Forgot password | Digital Star Consultants')
@section('content')
<section class="mx-auto max-w-md px-4 py-16 sm:px-6"><div class="text-center"><p class="text-sm font-bold uppercase tracking-[.2em] text-brand-600">Account recovery</p><h1 class="mt-3 font-display text-3xl font-extrabold">Reset your password.</h1><p class="mt-4 text-sm text-slate-600">We will send a reset link to the email on your account.</p></div>@include('partials.alerts')<form method="POST" action="{{ route('password.email') }}" class="surface-panel mt-8 rounded-2xl p-6">@csrf<label class="block text-sm font-semibold">Email<input class="field mt-2" type="email" name="email" value="{{ old('email') }}" required></label><button class="btn btn-blue mt-6 w-full" type="submit">Send reset link</button></form></section>
@endsection
