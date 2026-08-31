@extends('layouts.app')
@section('title','Services — Digital Star Consultants')
@section('content')
<section class="ds-page-hero"><div class="ds-container"><span class="ds-index">SERVICES / 01</span><h1>Technology services<br><em>with a purpose.</em></h1><p>Choose a capability, tell us what you need, and we'll shape the right delivery path.</p></div></section>
<section class="ds-section"><div class="ds-container">
<form class="ds-search" method="GET" action="{{ route('public.services.index') }}"><input name="search" value="{{ $search ?? request('search') }}" placeholder="Search services…"><button>Search ↗</button></form>
@if(isset($categories))
<div class="ds-pills"><a class="{{ empty($selectedCategory) ? 'active':'' }}" href="{{ route('public.services.index') }}">All</a>@foreach($categories as $category)<a class="{{ isset($selectedCategory) && $selectedCategory->id === $category->id ? 'active':'' }}" href="{{ route('public.services.index',['category'=>$category->slug]) }}">{{ $category->name }}</a>@endforeach</div>
@endif
<div class="ds-service-grid">
@forelse(($services ?? collect()) as $service)
<a class="ds-service-card" href="{{ route('public.services.show',$service->slug) }}"><span>{{ $service->category->name ?? 'SERVICE' }}</span><h2>{{ $service->name }}</h2><p>{{ $service->description }}</p><div><b>{{ $service->duration ?? 'Custom scope' }}</b><strong>{{ $service->price ? number_format($service->price) : 'Quote' }}</strong></div></a>
@empty
<div class="ds-empty">No services matched your search.</div>
@endforelse
</div></div></section>
@endsection