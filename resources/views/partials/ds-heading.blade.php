{{-- Section heading. Params: $kicker, $title, $copy (optional), $center (optional), $action (optional HTML) --}}
<div class="ds-head ds-reveal {{ !empty($center) ? 'ds-head-center' : '' }}">
    <div class="ds-head-text">
        <span class="ds-kicker">{{ $kicker }}</span>
        <h2>{{ $title }}</h2>
        @if (!empty($copy))
            <p>{{ $copy }}</p>
        @endif
    </div>
    @if (!empty($action))
        {!! $action !!}
    @endif
</div>
