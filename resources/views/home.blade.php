@extends('layouts.app', ['title' => __('site.nav.home')])

@section('content')
@php
    $phones = [
        ['display' => '0783 257 716', 'wa' => '255783257716'],
        ['display' => '0754 931 751', 'wa' => '255754931751'],
    ];
@endphp

{{-- HERO — Stripe-like, not a card --}}
<section class="pub-hero border-b border-line">
    <div class="shell pub-hero-grid py-16 lg:py-24">
        <div class="rise-in">
            <p class="eyebrow">{{ __('site.home.eyebrow') }}</p>
            <h1 class="display mt-4 max-w-xl text-ink">{{ __('site.home.hero_title') }}</h1>
            <p class="mt-6 max-w-lg text-lg leading-relaxed text-muted">{{ __('site.home.hero_lead') }}</p>
            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="{{ route('public.services.index') }}" class="button-primary">{{ __('site.home.cta_services') }}</a>
                <a href="https://wa.me/{{ $phones[0]['wa'] }}" class="wa-btn" target="_blank" rel="noopener">WhatsApp {{ $phones[0]['display'] }}</a>
                <a href="{{ route('work') }}" class="button-secondary">{{ __('site.home.cta_work') }}</a>
            </div>
        </div>

        <div class="rise-in" style="transition-delay:.12s">
            <form action="{{ route('public.track.form') }}" method="GET" class="rounded-[28px] border border-line bg-white/90 p-6 shadow-[0_30px_80px_#081b3512] backdrop-blur">
                <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue">{{ __('site.nav.track') }}</p>
                <h2 class="mt-2 text-xl font-black text-ink">{{ __('site.home.track_title') }}</h2>
                <p class="mt-2 text-sm text-muted">{{ __('site.home.track_lead') }}</p>
                <label class="mt-5 mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="q">{{ __('site.home.track_label') }}</label>
                <input id="q" name="q" type="text" required placeholder="{{ __('site.home.track_placeholder') }}"
                    class="w-full rounded-2xl border border-line bg-paper px-4 py-3.5 text-sm font-semibold outline-none focus:border-blue focus:ring-4 focus:ring-blue/10">
                <button type="submit" class="button-primary mt-4 w-full">{{ __('site.home.track_btn') }}</button>
            </form>
        </div>
    </div>
</section>

{{-- 3 PILLARS — not 9 cards --}}
<section class="shell py-16 lg:py-20">
    <div class="rise-in max-w-2xl">
        <p class="eyebrow">{{ __('site.home.problems_eyebrow') }}</p>
        <h2 class="section-title mt-2 text-ink">{{ __('site.home.problems_title') }}</h2>
        <p class="mt-3 text-muted">{{ __('site.home.problems_lead') }}</p>
    </div>

    <div class="pub-pillars mt-10">
        {{-- IT first --}}
        <div class="pub-pillar pub-pillar-it rise-in">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-yellow">01 · IT</p>
            <h3 class="mt-3 text-2xl font-black">{{ __('site.problems.it.title') }}</h3>
            <p class="pub-pillar-desc mt-2 text-sm leading-relaxed">{{ __('site.problems.it.desc') }}</p>
            <div class="mt-6 flex flex-col gap-2 text-sm font-bold">
                <a href="{{ route('public.services.index', ['category' => 'it-tech-consultancy']) }}">{{ __('site.nav.services') }} →</a>
                <a href="{{ route('work') }}">{{ __('site.nav.work') }} →</a>
            </div>
        </div>

        {{-- Government --}}
        <div class="pub-pillar rise-in" style="transition-delay:.08s">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue">02 · Government</p>
            <h3 class="mt-3 text-2xl font-black text-ink">{{ __('site.problems.identity.title') }}</h3>
            <p class="mt-2 text-sm text-muted leading-relaxed">{{ __('site.problems.identity.desc') }}</p>
            <ul class="mt-5 space-y-2 text-sm font-bold text-blue">
                <li><a href="{{ route('public.services.index', ['category' => 'serikali-identification']) }}">NIDA / Serikali →</a></li>
                <li><a href="{{ route('public.services.index', ['category' => 'tra']) }}">TRA / TIN →</a></li>
                <li><a href="{{ route('public.services.index', ['category' => 'brela-business']) }}">BRELA →</a></li>
                <li><a href="{{ route('public.services.index', ['category' => 'travel']) }}">{{ __('site.problems.travel.title') }} →</a></li>
            </ul>
        </div>

        {{-- Print & more --}}
        <div class="pub-pillar rise-in" style="transition-delay:.16s">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue">03 · Studio</p>
            <h3 class="mt-3 text-2xl font-black text-ink">{{ __('site.problems.print.title') }}</h3>
            <p class="mt-2 text-sm text-muted leading-relaxed">{{ __('site.problems.print.desc') }}</p>
            <ul class="mt-5 space-y-2 text-sm font-bold text-blue">
                <li><a href="{{ route('public.services.index', ['category' => 'printing-graphics-design']) }}">{{ __('site.problems.print.title') }} →</a></li>
                <li><a href="{{ route('public.services.index', ['category' => 'stationery']) }}">{{ __('site.problems.stationery.title') }} →</a></li>
                <li><a href="{{ route('public.services.index', ['category' => 'jobs']) }}">{{ __('site.problems.jobs_edu.title') }} →</a></li>
            </ul>
        </div>
    </div>
</section>

{{-- HOW IT WORKS — horizontal steps, not cards --}}
<section class="border-y border-line bg-[#f7f9fc]">
    <div class="shell py-14 lg:py-16">
        <div class="rise-in mb-8 max-w-xl">
            <p class="eyebrow">{{ __('site.home.how_eyebrow') }}</p>
            <h2 class="section-title mt-2 text-ink">{{ __('site.home.how_title') }}</h2>
        </div>
        <div class="pub-steps rise-in rounded-3xl border border-line bg-white">
            @foreach ([1,2,3,4] as $n)
                <div class="pub-step">
                    <p class="pub-step-n">0{{ $n }}</p>
                    <h3 class="mt-3 text-base font-black text-ink">{{ __('site.home.step'.$n.'_t') }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ __('site.home.step'.$n.'_d') }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- WORK — asymmetric 1+2 --}}
<section class="shell py-16 lg:py-20">
    <div class="rise-in flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="eyebrow">{{ __('site.home.work_eyebrow') }}</p>
            <h2 class="section-title mt-2 text-ink">{{ __('site.home.work_title') }}</h2>
            <p class="mt-3 max-w-lg text-muted">{{ __('site.home.work_lead') }}</p>
        </div>
        <a href="{{ route('work') }}" class="button-secondary shrink-0">{{ __('site.home.work_cta') }}</a>
    </div>

    <div class="pub-feature-grid mt-10">
        <a href="{{ route('work') }}" class="pub-feature-main rise-in group">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-yellow">{{ __('site.work.type_it') }}</p>
            <h3 class="mt-4 text-2xl font-black leading-tight group-hover:text-yellow transition">Laravel · systems · web apps</h3>
            <p class="mt-3 max-w-md text-sm text-white/65">{{ __('site.about.why_1') }}</p>
        </a>
        <a href="{{ route('work') }}" class="rise-in rounded-3xl border border-line bg-white p-6 transition hover:-translate-y-1 hover:shadow-lg">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue">{{ __('site.work.type_graphics') }}</p>
            <h3 class="mt-3 text-lg font-black text-ink">Brand · print · outdoor</h3>
            <p class="mt-2 text-sm text-muted">{{ __('site.problems.print.desc') }}</p>
        </a>
        <a href="{{ route('public.contact.show') }}" class="rise-in rounded-3xl border border-line bg-sky p-6 transition hover:-translate-y-1">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue">WhatsApp</p>
            <h3 class="mt-3 text-lg font-black text-ink">{{ $phones[0]['display'] }}</h3>
            <p class="mt-2 text-sm text-muted">{{ __('site.home.contact_lead') }}</p>
        </a>
    </div>
</section>

{{-- TRUST band — different layout --}}
<section class="shell pb-20">
    <div class="pub-trust rise-in">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-yellow">{{ __('site.home.contact_eyebrow') }}</p>
            <h2 class="mt-3 text-3xl font-black">{{ __('site.home.contact_title') }}</h2>
            <p class="mt-3 max-w-md text-white/65">{{ __('site.home.contact_lead') }}</p>
            <p class="mt-4 text-sm font-semibold text-white/80">{{ __('site.footer.location') }}</p>
        </div>
        <div class="flex flex-col gap-3">
            @foreach ($phones as $p)
                <a href="https://wa.me/{{ $p['wa'] }}" class="wa-btn justify-center" target="_blank" rel="noopener">WhatsApp {{ $p['display'] }}</a>
                <a href="tel:+{{ $p['wa'] }}" class="button-secondary !border-white/25 !text-white justify-center">{{ $p['display'] }}</a>
            @endforeach
            <a href="{{ route('public.contact.show') }}" class="text-center text-sm font-bold text-yellow hover:underline">{{ __('site.nav.contact') }} →</a>
        </div>
    </div>
</section>
@endsection
