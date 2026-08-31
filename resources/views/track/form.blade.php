@extends('layouts.app')
@section('title','Track Request — Digital Star Consultants')
@section('content')
<section class="ds-page-hero compact"><div class="ds-container"><span class="ds-index">CLIENT PORTAL / TRACK</span><h1>Where is your<br><em>request now?</em></h1><p>Enter the reference number you received after submitting a service request.</p></div></section>
<section class="ds-section"><div class="ds-container ds-track"><form class="ds-track-form" method="GET" action="{{ route('public.track.form') }}"><label for="q">REFERENCE NUMBER</label><div><input id="q" name="q" value="{{ request('q', $recentReference ?? '') }}" placeholder="e.g. DS-2026-0012" required><button class="ds-btn ds-btn-gold">Track ↗</button></div></form>@if(isset($recentReference) && $recentReference)<p class="ds-muted">Showing your most recent reference: <strong>{{ $recentReference }}</strong></p>@endif</div></section>
@endsection