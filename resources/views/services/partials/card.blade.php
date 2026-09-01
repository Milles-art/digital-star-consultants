@php
    $slug = strtolower($service->category?->slug ?? '');
    $iconKey = match (true) {
        str_contains($slug, 'government') => 'government',
        str_contains($slug, 'passport') || str_contains($slug, 'immigration') => 'passport',
        str_contains($slug, 'jobs') => 'jobs',
        str_contains($slug, 'education') => 'education',
        str_contains($slug, 'tra') || str_contains($slug, 'tax') => 'tax',
        str_contains($slug, 'travel') => 'travel',
        str_contains($slug, 'printing') => 'printing',
        str_contains($slug, 'branding') => 'branding',
        str_contains($slug, 'stationery') => 'stationery',
        str_contains($slug, 'business') || str_contains($slug, 'brela') => 'business',
        str_contains($slug, 'mobile') => 'mobile',
        str_contains($slug, 'website') => 'website',
        str_contains($slug, 'it') || str_contains($slug, 'tech') => 'it',
        str_contains($slug, 'support') => 'support',
        default => 'forms',
    };
@endphp
<a class="ds-service-card" href="{{ route('public.services.show', $service->slug) }}">
    <div class="ds-service-card-top">
        <span class="ds-service-icon">@include('partials.icon', ['iconKey' => $iconKey])</span>
        <span class="ds-card-arrow" aria-hidden="true">↗</span>
    </div>
    <span class="ds-service-kicker">{{ $service->category?->name ?? 'SERVICE' }}</span>
    <h3>{{ $service->name }}</h3>
    <p>{{ $service->description ?: 'Professional assistance from application to completion.' }}</p>
    <div class="ds-service-tags"><span>Guided application</span>@if($service->duration !== 'N/A')<span>{{ $service->duration }}</span>@endif</div>
    <div class="ds-service-bottom"><span>{{ $service->is_free ? 'Quote on request' : $service->formatted_price }}</span><strong>View service →</strong></div>
</a>
