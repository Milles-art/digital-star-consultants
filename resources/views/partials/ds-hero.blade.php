{{--
    Dark hero used on every page.
    Params: $kicker, $title (HTML allowed - escape user data with e()), $lead, $image (optional), $points (optional array)
    Optional @section slots: 'hero-crumbs', 'hero-actions', 'hero-aside'
--}}
@php
    $image = $image ?? config('digitalstar.images.hero');
    $points = $points ?? [];
@endphp
<section class="ds-hero">
    <img class="ds-hero-bg" src="{{ $image }}" alt="">
    <div class="ds-hero-shade" aria-hidden="true"></div>
    <div class="ds-hero-glow" aria-hidden="true"></div>
    <div class="ds-hero-grid" aria-hidden="true"></div>

    <div class="ds-container ds-hero-inner">
        <div class="ds-hero-copy">
            @hasSection('hero-crumbs')
                <nav class="ds-crumbs ds-rise" aria-label="Breadcrumb">@yield('hero-crumbs')</nav>
            @endif
            <span class="ds-pill ds-rise"><span class="ds-dot"></span>{{ $kicker }}</span>
            <h1 class="ds-rise" style="--d:.1s">{!! $title !!}</h1>
            @if (!empty($lead))
                <p class="ds-hero-lead ds-rise" style="--d:.2s">{!! $lead !!}</p>
            @endif
            @hasSection('hero-actions')
                <div class="ds-actions ds-rise" style="--d:.3s">@yield('hero-actions')</div>
            @endif
            @if (count($points))
                <div class="ds-hero-points ds-rise" style="--d:.4s">
                    @foreach ($points as $point)
                        <span><i data-lucide="circle-check"></i>{{ $point }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        @hasSection('hero-aside')
            <div class="ds-rise" style="--d:.35s">@yield('hero-aside')</div>
        @endif
    </div>
</section>
