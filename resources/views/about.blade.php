@extends('layouts.app', ['title' => __('site.nav.about')])

@section('content')
<section class="border-b border-line bg-gradient-to-b from-sky/50 to-paper">
    <div class="shell py-14 lg:py-20">
        <p class="eyebrow reveal">{{ __('site.about.eyebrow') }}</p>
        <h1 class="section-title reveal mt-2 max-w-3xl text-ink">{{ __('site.about.title') }}</h1>
        <p class="reveal mt-5 max-w-2xl text-lg text-muted">{{ __('site.about.lead') }}</p>
    </div>
</section>

<section class="shell py-14 lg:py-16">
    <div class="grid gap-10 lg:grid-cols-2">
        <div class="reveal">
            <h2 class="text-2xl font-black text-ink">{{ __('site.about.mission_t') }}</h2>
            <p class="mt-4 text-muted leading-relaxed">{{ __('site.about.mission_d') }}</p>
        </div>
        <div class="reveal rounded-3xl border border-line bg-white p-6 shadow-sm">
            <h2 class="text-2xl font-black text-ink">{{ __('site.about.why_t') }}</h2>
            <ul class="mt-5 space-y-3 text-sm font-semibold text-ink">
                <li class="flex gap-3"><span class="text-yellow">●</span> {{ __('site.about.why_1') }}</li>
                <li class="flex gap-3"><span class="text-yellow">●</span> {{ __('site.about.why_2') }}</li>
                <li class="flex gap-3"><span class="text-yellow">●</span> {{ __('site.about.why_3') }}</li>
                <li class="flex gap-3"><span class="text-yellow">●</span> {{ __('site.about.why_4') }}</li>
            </ul>
        </div>
    </div>

    <div class="reveal mt-12 rounded-3xl border border-line bg-ink p-8 text-white">
        <p class="text-[11px] font-bold uppercase tracking-wider text-yellow">{{ __('site.about.base_t') }}</p>
        <p class="mt-3 text-xl font-black">{{ __('site.about.base_d') }}</p>
        <div class="mt-6 flex flex-wrap gap-2">
            <a href="https://wa.me/255783257716" class="wa-btn" target="_blank" rel="noopener">WhatsApp 0783 257 716</a>
            <a href="https://wa.me/255754931751" class="wa-btn" target="_blank" rel="noopener">WhatsApp 0754 931 751</a>
            <a href="{{ route('public.contact.show') }}" class="button-secondary !border-white/30 !text-white">{{ __('site.nav.contact') }}</a>
        </div>
    </div>
</section>
@endsection
