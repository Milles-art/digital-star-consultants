@extends('layouts.app', ['title' => __('site.nav.home')])

@section('content')
@php
    $problemCards = [
        ['key' => 'it', 'slug' => 'it-tech-consultancy', 'route' => 'category'],
        ['key' => 'identity', 'slug' => 'serikali-identification', 'route' => 'category'],
        ['key' => 'tra', 'slug' => 'tra', 'route' => 'category'],
        ['key' => 'brela', 'slug' => 'brela-business', 'route' => 'category'],
        ['key' => 'jobs_edu', 'slug' => null, 'route' => 'services'], // jobs + education combined
        ['key' => 'travel', 'slug' => 'travel', 'route' => 'category'],
        ['key' => 'print', 'slug' => 'printing-graphics-design', 'route' => 'category'],
        ['key' => 'stationery', 'slug' => 'stationery', 'route' => 'category'],
        ['key' => 'other', 'slug' => 'other-online-forms', 'route' => 'category'],
    ];
    $phones = [
        ['display' => '0783 257 716', 'wa' => '255783257716'],
        ['display' => '0754 931 751', 'wa' => '255754931751'],
    ];
@endphp

{{-- Hero --}}
<section class="relative overflow-hidden border-b border-line bg-gradient-to-b from-sky/70 to-paper">
    <div class="shell grid items-center gap-12 py-16 lg:grid-cols-[1.15fr_0.85fr] lg:py-22">
        <div class="reveal">
            <p class="eyebrow">{{ __('site.home.eyebrow') }}</p>
            <h1 class="display mt-4 text-ink">{{ __('site.home.hero_title') }}</h1>
            <p class="mt-6 max-w-xl text-lg text-muted">{{ __('site.home.hero_lead') }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('public.services.index') }}" class="button-primary">{{ __('site.home.cta_services') }}</a>
                <a href="{{ route('work') }}" class="button-secondary">{{ __('site.home.cta_work') }}</a>
            </div>
            <dl class="mt-12 grid max-w-lg grid-cols-3 gap-3">
                <div class="rounded-2xl border border-line bg-white/85 p-4 text-center shadow-sm">
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-muted">{{ __('site.home.stat_submit') }}</dt>
                    <dd class="mt-1 text-sm font-black text-ink">{{ __('site.home.stat_submit_v') }}</dd>
                </div>
                <div class="rounded-2xl border border-line bg-white/85 p-4 text-center shadow-sm">
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-muted">{{ __('site.home.stat_track') }}</dt>
                    <dd class="mt-1 text-sm font-black text-ink">{{ __('site.home.stat_track_v') }}</dd>
                </div>
                <div class="rounded-2xl border border-line bg-white/85 p-4 text-center shadow-sm">
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-muted">{{ __('site.home.stat_support') }}</dt>
                    <dd class="mt-1 text-sm font-black text-ink">{{ __('site.home.stat_support_v') }}</dd>
                </div>
            </dl>
        </div>

        <div class="reveal float-slow">
            <div class="rounded-[28px] border border-line bg-white p-6 shadow-xl shadow-ink/5">
                <p class="eyebrow">{{ __('site.nav.track') }}</p>
                <h2 class="mt-2 text-2xl font-black text-ink">{{ __('site.home.track_title') }}</h2>
                <p class="mt-2 text-sm text-muted">{{ __('site.home.track_lead') }}</p>
                <form action="{{ route('public.track.form') }}" method="GET" class="mt-6 space-y-3" id="home-track-form">
                    <label class="block text-xs font-bold uppercase tracking-wider text-muted" for="track-ref">{{ __('site.home.track_label') }}</label>
                    <input id="track-ref" name="q" type="text" required
                        placeholder="{{ __('site.home.track_placeholder') }}"
                        class="w-full rounded-2xl border border-line bg-paper px-4 py-3.5 text-sm font-semibold text-ink outline-none focus:border-blue focus:ring-4 focus:ring-blue/10">
                    <button type="submit" class="button-primary w-full">{{ __('site.home.track_btn') }}</button>
                </form>
                <p class="mt-4 text-center text-xs text-muted">
                    <a href="{{ route('public.track.form') }}" class="font-bold text-blue">{{ __('site.nav.track') }} →</a>
                </p>
            </div>
        </div>
    </div>
</section>

{{-- Problem cards --}}
<section class="shell py-16 lg:py-20">
    <div class="reveal max-w-2xl">
        <p class="eyebrow">{{ __('site.home.problems_eyebrow') }}</p>
        <h2 class="section-title mt-2 text-ink">{{ __('site.home.problems_title') }}</h2>
        <p class="mt-3 text-muted">{{ __('site.home.problems_lead') }}</p>
    </div>

    <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($problemCards as $i => $card)
            @php
                $href = $card['slug']
                    ? route('public.services.index', ['category' => $card['slug']])
                    : route('public.services.index');
            @endphp
            <a href="{{ $href }}"
               class="reveal group rounded-3xl border border-line bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue/30 hover:shadow-lg hover:shadow-blue/5"
               style="transition-delay: {{ min($i * 40, 240) }}ms">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-sky text-sm font-black text-blue">
                    {{ str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) }}
                </span>
                <h3 class="mt-4 text-lg font-black text-ink group-hover:text-blue">{{ __('site.problems.'.$card['key'].'.title') }}</h3>
                <p class="mt-2 text-sm text-muted">{{ __('site.problems.'.$card['key'].'.desc') }}</p>
                <p class="mt-4 text-sm font-bold text-blue">{{ __('site.common.learn_more') }} →</p>
            </a>
        @endforeach
    </div>
</section>

{{-- How it works --}}
<section id="how-it-works" class="border-y border-line bg-surface">
    <div class="shell py-16 lg:py-20">
        <div class="reveal">
            <p class="eyebrow">{{ __('site.home.how_eyebrow') }}</p>
            <h2 class="section-title mt-2 text-ink">{{ __('site.home.how_title') }}</h2>
        </div>
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([1,2,3,4] as $n)
                <div class="reveal rounded-3xl border border-line bg-white p-6 shadow-sm">
                    <span class="text-3xl font-black text-yellow">0{{ $n }}</span>
                    <h3 class="mt-4 text-lg font-black text-ink">{{ __('site.home.step'.$n.'_t') }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ __('site.home.step'.$n.'_d') }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Work teaser --}}
<section class="shell py-16 lg:py-20">
    <div class="reveal flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="eyebrow">{{ __('site.home.work_eyebrow') }}</p>
            <h2 class="section-title mt-2 text-ink">{{ __('site.home.work_title') }}</h2>
            <p class="mt-3 max-w-xl text-muted">{{ __('site.home.work_lead') }}</p>
        </div>
        <a href="{{ route('work') }}" class="button-secondary shrink-0">{{ __('site.home.work_cta') }}</a>
    </div>
    <div class="mt-10 grid gap-4 md:grid-cols-3">
        <div class="reveal rounded-3xl border border-line bg-ink p-6 text-white">
            <p class="text-[11px] font-bold uppercase tracking-wider text-yellow">{{ __('site.work.type_it') }}</p>
            <h3 class="mt-3 text-xl font-black">Laravel · systems · web</h3>
            <p class="mt-2 text-sm text-white/65">{{ __('site.about.why_1') }}</p>
        </div>
        <div class="reveal rounded-3xl border border-line bg-white p-6">
            <p class="text-[11px] font-bold uppercase tracking-wider text-blue">{{ __('site.work.type_graphics') }}</p>
            <h3 class="mt-3 text-xl font-black text-ink">Brand · print · outdoor</h3>
            <p class="mt-2 text-sm text-muted">{{ __('site.problems.print.desc') }}</p>
        </div>
        <div class="reveal rounded-3xl border border-line bg-sky p-6">
            <p class="text-[11px] font-bold uppercase tracking-wider text-blue">WhatsApp</p>
            <h3 class="mt-3 text-xl font-black text-ink">{{ $phones[0]['display'] }}</h3>
            <a href="https://wa.me/{{ $phones[0]['wa'] }}" class="wa-btn mt-4" target="_blank" rel="noopener">WhatsApp</a>
        </div>
    </div>
</section>

{{-- Contact strip --}}
<section id="contact" class="border-t border-line bg-surface">
    <div class="shell py-16 lg:py-20">
        <div class="reveal grid gap-8 lg:grid-cols-[1fr_1fr] lg:items-center">
            <div>
                <p class="eyebrow">{{ __('site.home.contact_eyebrow') }}</p>
                <h2 class="section-title mt-2 text-ink">{{ __('site.home.contact_title') }}</h2>
                <p class="mt-3 text-muted">{{ __('site.home.contact_lead') }}</p>
                <p class="mt-4 text-sm font-semibold text-ink">{{ __('site.footer.location') }}</p>
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($phones as $p)
                        <a href="https://wa.me/{{ $p['wa'] }}" class="wa-btn" target="_blank" rel="noopener">WhatsApp {{ $p['display'] }}</a>
                        <a href="tel:+{{ $p['wa'] }}" class="button-secondary">{{ $p['display'] }}</a>
                    @endforeach
                </div>
            </div>
            <div class="rounded-3xl border border-line bg-white p-6 shadow-sm">
                <a href="{{ route('public.contact.show') }}" class="button-primary w-full justify-center">{{ __('site.nav.contact') }} →</a>
                <a href="{{ route('public.services.index') }}" class="button-secondary mt-3 w-full justify-center">{{ __('site.nav.browse_services') }}</a>
            </div>
        </div>
    </div>
</section>
@endsection
