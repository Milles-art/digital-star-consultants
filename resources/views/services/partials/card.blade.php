{{--
    Service card used across the catalogue.
    Params: $service (name, slug, description, is_free, formatted_price, optional icon_key), $delay (optional)
--}}
@php
    // Pick a stable icon per service when it has no icon_key of its own
    $iconKeys = ['website', 'it', 'business', 'forms', 'branding', 'support'];
    $iconKey = $service->icon_key ?? $iconKeys[crc32($service->slug ?? $service->name) % count($iconKeys)];
@endphp
<a href="{{ route('public.services.show', $service->slug) }}" class="ds-service ds-reveal" style="--d:{{ $delay ?? 0 }}s">
    <span class="ds-icon-box">@include('partials.icon', ['iconKey' => $iconKey])</span>
    <h3>{{ $service->name }}</h3>
    <p>{{ \Illuminate\Support\Str::limit($service->description ?: 'Professional assistance from application to completion.', 96) }}</p>
    <span class="ds-card-link ds-price">
        {{ $service->is_free ? 'Quote on request' : $service->formatted_price }}
        <i data-lucide="arrow-up-right"></i>
    </span>
</a>
