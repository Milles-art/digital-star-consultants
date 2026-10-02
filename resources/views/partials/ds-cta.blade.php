{{-- Call-to-action band. Params: $kicker, $title, $copy, $buttonLabel, $url, $checklist (optional) --}}
@php
    $checklist = $checklist ?? ['Tailored solutions', 'On-time delivery', 'Affordable pricing', 'Long-term support'];
@endphp
<section class="ds-cta-section">
    <div class="ds-cta ds-reveal">
        <div class="ds-cta-glow" aria-hidden="true"></div>
        <div class="ds-cta-corner" aria-hidden="true"></div>
        <div class="ds-cta-inner">
            <div>
                <span class="ds-kicker">{{ $kicker }}</span>
                <h2>{{ $title }}</h2>
                <p>{{ $copy }}</p>
                <a href="{{ $url }}" class="ds-btn ds-btn-white">{{ $buttonLabel }} <i data-lucide="arrow-right"></i></a>
            </div>
            <ul class="ds-checklist">
                @foreach ($checklist as $item)
                    <li><i data-lucide="circle-check"></i>{{ $item }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
