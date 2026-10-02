{{-- Service group card (a sub-category). Params: $child, $number --}}
<a class="ds-service ds-reveal" href="{{ route('public.services.index', ['category' => $child->slug]) }}">
    <span class="ds-icon-box"><span class="ds-num-box">{{ $number }}</span></span>
    <h3>{{ $child->name }}</h3>
    <p>{{ \Illuminate\Support\Str::limit($child->description ?: 'Explore services in this group.', 96) }}</p>
    <span class="ds-card-link">
        {{ $child->active_services_count }} {{ $child->active_services_count === 1 ? 'service' : 'services' }}
        <i data-lucide="arrow-up-right"></i>
    </span>
</a>
