@extends('layouts.app', ['title' => __('site.nav.work')])

@section('content')
@php $isSw = app()->getLocale() === 'sw'; @endphp

<section class="border-b border-line bg-gradient-to-b from-sky/50 to-paper">
    <div class="shell py-14 lg:py-18">
        <p class="eyebrow reveal">{{ __('site.work.eyebrow') }}</p>
        <h1 class="section-title reveal mt-2 text-ink">{{ __('site.work.title') }}</h1>
        <p class="reveal mt-4 max-w-2xl text-muted">{{ __('site.work.lead') }}</p>
    </div>
</section>

<section class="shell py-14">
    <h2 class="reveal text-2xl font-black text-ink">{{ __('site.work.it_title') }}</h2>
    <div class="mt-6 grid gap-5 md:grid-cols-3">
        @foreach ($itProjects as $i => $project)
            <article class="reveal rounded-3xl border border-line bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                <span class="rounded-full bg-sky px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-blue">{{ $project['tag'] }}</span>
                <h3 class="mt-4 text-lg font-black text-ink">{{ $isSw ? $project['title_sw'] : $project['title'] }}</h3>
                <p class="mt-2 text-sm text-muted">{{ $isSw ? $project['summary_sw'] : $project['summary'] }}</p>
                <p class="mt-4 text-xs font-bold uppercase tracking-wider text-muted">{{ __('site.work.tech') }}</p>
                <p class="mt-1 text-sm font-semibold text-ink">{{ $project['stack'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="border-t border-line bg-surface">
    <div class="shell py-14">
        <h2 class="reveal text-2xl font-black text-ink">{{ __('site.work.graphics_title') }}</h2>
        <div class="mt-6 grid gap-5 md:grid-cols-3">
            @foreach ($graphicsProjects as $project)
                <article class="reveal rounded-3xl border border-line bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <span class="rounded-full bg-yellow/30 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-ink">{{ $project['tag'] }}</span>
                    <h3 class="mt-4 text-lg font-black text-ink">{{ $isSw ? $project['title_sw'] : $project['title'] }}</h3>
                    <p class="mt-2 text-sm text-muted">{{ $isSw ? $project['summary_sw'] : $project['summary'] }}</p>
                    <p class="mt-4 text-xs font-bold uppercase tracking-wider text-muted">{{ __('site.work.tech') }}</p>
                    <p class="mt-1 text-sm font-semibold text-ink">{{ $project['stack'] }}</p>
                </article>
            @endforeach
        </div>

        <div class="reveal mt-12 rounded-3xl border border-line bg-ink p-8 text-center text-white">
            <p class="text-lg font-black">{{ __('site.home.contact_title') }}</p>
            <p class="mt-2 text-sm text-white/65">{{ __('site.home.contact_lead') }}</p>
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <a href="https://wa.me/255783257716" class="wa-btn" target="_blank" rel="noopener">WhatsApp 0783 257 716</a>
                <a href="{{ route('public.contact.show') }}" class="button-secondary !border-white/30 !text-white">{{ __('site.nav.contact') }}</a>
            </div>
        </div>
    </div>
</section>
@endsection
