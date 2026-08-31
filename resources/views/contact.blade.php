@extends('layouts.app')
@section('title','Contact — Digital Star Consultants')
@section('content')
<section class="ds-page-hero compact"><div class="ds-container"><span class="ds-index">CONTACT / CONSULTATION</span><h1>Let's build something<br><em>useful.</em></h1><p>Tell us what you're trying to improve, automate or launch.</p></div></section>
<section class="ds-section"><div class="ds-container ds-form-layout"><div><span class="ds-index">DIRECT</span><h2>Start with<br>a conversation.</h2><p>Prefer WhatsApp or a call? Reach out directly. Or use the form and we'll get back to you.</p><div class="ds-contact-info"><strong>Dar es Salaam, Tanzania</strong><span>Mon–Fri · 08:00–17:00</span><a href="https://wa.me/" rel="noopener">WhatsApp ↗</a></div></div>
<form class="ds-form" method="POST" action="{{ route('public.contact.show') }}">@csrf
@if(isset($errors) && $errors->any())<div class="ds-errors">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<div class="ds-field"><label>Name *</label><input name="name" value="{{ old('name') }}" required></div>
<div class="ds-field"><label>Email *</label><input type="email" name="email" value="{{ old('email') }}" required></div>
<div class="ds-field"><label>Phone</label><input name="phone" value="{{ old('phone') }}"></div>
<div class="ds-field"><label>Message *</label><textarea name="message" required>{{ old('message') }}</textarea></div>
<button class="ds-btn ds-btn-gold ds-submit">Send Inquiry ↗</button></form></div></section>
@endsection