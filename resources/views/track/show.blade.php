@extends('layouts.app')
@section('title','Request '.$reference.' — Digital Star Consultants')
@section('content')
<section class="ds-page-hero compact"><div class="ds-container"><span class="ds-index">TRACKING / {{ $reference }}</span><h1>Request<br><em>status.</em></h1><p>{{ $service->name ?? 'Service request' }}</p></div></section>
<section class="ds-section"><div class="ds-container ds-status-layout"><div class="ds-status-card"><span class="ds-status-dot"></span><small>CURRENT STATUS</small><strong>{{ $submission->status ?? 'Pending' }}</strong><p>Reference: {{ $reference }}</p></div><div class="ds-timeline">@foreach(($timeline ?? []) as $item)<div class="ds-timeline-item"><span></span><div><small>{{ $item->created_at ?? '' }}</small><h3>{{ $item->status ?? '' }}</h3><p>{{ $item->note ?? '' }}</p></div></div>@endforeach</div></div></section>
@endsection