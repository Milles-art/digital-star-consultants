{{-- Project card. Params: $project (title, category, image), $index (optional, opens case study), $href (optional link) --}}
@if (!empty($href))
<a href="{{ $href }}" class="ds-project">
@else
<button type="button" class="ds-project" data-project-id="{{ $index }}" data-project-category="{{ $project['filter'] }}">
@endif
    <img src="{{ asset($project['thumbnail']) }}" @if($project['width'] > 640) srcset="{{ asset($project['thumbnail']) }} 640w, {{ asset($project['image']) }} {{ $project['width'] }}w" @endif sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw" width="{{ $project['width'] }}" height="{{ $project['height'] }}" alt="{{ $project['alt'] }}" loading="lazy" decoding="async">
    <span class="ds-project-shade" aria-hidden="true"></span>
    <span class="ds-project-arrow"><i data-lucide="{{ $project['type'] === 'video' ? 'play' : 'arrow-up-right' }}"></i></span>
    <span class="ds-project-body">
        <span>{{ $project['category'] }}</span>
        <h3>{{ $project['title'] }}</h3>
    </span>
@if (!empty($href))
</a>
@else
</button>
@endif
