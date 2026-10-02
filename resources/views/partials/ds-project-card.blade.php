{{-- Project card. Params: $project (title, category, image), $index (optional, opens case study), $href (optional link) --}}
@if (!empty($href))
<a href="{{ $href }}" class="ds-project">
@else
<button type="button" class="ds-project" data-project-id="{{ $index }}" data-project-category="{{ $project['filter'] }}">
@endif
    <img src="{{ $project['image'] }}" alt="{{ $project['title'] }} preview" loading="lazy">
    <span class="ds-project-shade" aria-hidden="true"></span>
    <span class="ds-project-arrow"><i data-lucide="arrow-up-right"></i></span>
    <span class="ds-project-body">
        <span>{{ $project['category'] }}</span>
        <h3>{{ $project['title'] }}</h3>
    </span>
@if (!empty($href))
</a>
@else
</button>
@endif
