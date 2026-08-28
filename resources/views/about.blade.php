@extends('layouts.app', [
    'title' => __('site.nav.about'),
    'metaDescription' => 'About Digital Star Consultants — Software engineering and digital systems studio based in Dar es Salaam, Tanzania.'
])

@section('content')
@php
    $locale = app()->getLocale();
    $isSw = $locale === 'sw';
@endphp

{{-- ========================================================================= --}}
{{-- ABOUT HERO                                                                --}}
{{-- ========================================================================= --}}
<section class="border-b border-line bg-gradient-to-b from-[#F2F6FB] via-[#F8FAFD] to-white py-14 lg:py-20">
    <div class="shell">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-line shadow-xs mb-5">
                <span class="h-2 w-2 rounded-full bg-yellow"></span>
                <span class="text-[11px] font-black uppercase tracking-[0.18em] text-navy">{{ __('site.nav.about') }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-ink tracking-tight">
                {{ $isSw ? 'Kuhusu Digital Star Consultants' : 'About Digital Star Consultants' }}
            </h1>
            <p class="mt-4 text-sm sm:text-base text-muted leading-relaxed">
                {{ $isSw ? 'Studio ya teknolojia na uhandisi wa programu iliyoko Dar es Salaam, inayojenga mifumo thabiti ya wavuti, dashibodi za uendeshaji, na kusaidia taratibu rasmi za kiserikali na biashara.' : 'A creative-tech and software engineering studio based in Dar es Salaam, Tanzania — delivering robust web platforms, internal operations tools, and structured institutional support.' }}
            </p>
        </div>
    </div>
</section>

{{-- ========================================================================= --}}
{{-- ENGINEERING THESIS & 4 CORE VALUES                                        --}}
{{-- ========================================================================= --}}
<section class="py-16 sm:py-20 bg-canvas border-b border-line">
    <div class="shell">
        <div class="grid gap-12 lg:grid-cols-2 lg:items-center">
            
            <div class="space-y-6 text-xs sm:text-sm text-muted leading-relaxed">
                <p class="eyebrow">{{ $isSw ? 'Dhamira Yetu' : 'Our Engineering Philosophy' }}</p>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-ink tracking-tight">
                    {{ $isSw ? 'Teknolojia inayofanya kazi kwenye mazingira halisi ya biashara.' : 'Software engineered for practical business reliability.' }}
                </h2>
                <p>
                    {{ $isSw ? 'Digital Star Consultants ilianzishwa kwa lengo la kuziba pengo kati ya teknolojia ya kisasa ya programu na changamoto halisi za kiutendaji zinazokabili biashara, taasisi, na raia nchini Tanzania.' : 'Digital Star Consultants was established to bridge the gap between high-performance software engineering and the day-to-day operational realities faced by enterprises, institutions, and citizens.' }}
                </p>
                <p>
                    {{ $isSw ? 'Kila mfumo tunaoutengeneza unajengwa kwa misingi ya usalama, kasi ya juu, muundo rahisi kwa watumiaji, na ufuatiliaji wa wazi kwa namba ya kumbukumbu.' : 'Every system we architect is built on foundations of security, high reliability, clean user experiences, and transparent workflow tracking.' }}
                </p>
                
                <div class="pt-4">
                    <a href="{{ route('public.contact.show') }}" class="button-primary !py-3 !px-6 !text-xs font-black">
                        <span>Consult with Our Engineers</span>
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    </a>
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="rounded-3xl border border-line bg-[#F8FAFD] p-7 shadow-xs">
                    <span class="text-2xl font-black text-yellow">01</span>
                    <h3 class="mt-3 text-base font-black text-ink">{{ $isSw ? 'Uhandisi Imara' : 'Robust Architecture' }}</h3>
                    <p class="mt-2 text-xs text-muted leading-relaxed">{{ $isSw ? 'Tunatumia mifumo ya kisasa kama Laravel 13, database imara na REST APIs.' : 'Modern battle-tested frameworks, typed database schemas, and secure REST APIs.' }}</p>
                </div>
                <div class="rounded-3xl border border-line bg-[#F8FAFD] p-7 shadow-xs">
                    <span class="text-2xl font-black text-yellow">02</span>
                    <h3 class="mt-3 text-base font-black text-ink">{{ $isSw ? 'Uwazi wa Ufuatiliaji' : 'Live Tracking' }}</h3>
                    <p class="mt-2 text-xs text-muted leading-relaxed">{{ $isSw ? 'Wateja wanafuatilia maombi yao bila ulazima wa kukariri akaunti au nywila.' : 'Frictionless status lookups by reference code without account barrier.' }}</p>
                </div>
                <div class="rounded-3xl border border-line bg-[#F8FAFD] p-7 shadow-xs">
                    <span class="text-2xl font-black text-yellow">03</span>
                    <h3 class="mt-3 text-base font-black text-ink">{{ $isSw ? 'Uelewa wa Ndani' : 'Tanzanian Fluency' }}</h3>
                    <p class="mt-2 text-xs text-muted leading-relaxed">{{ $isSw ? 'Uzoefu wa kina na mifumo rasmi ya Tanzania (TRA, BRELA, NIDA).' : 'Deep fluency with Tanzanian business workflows, compliance, and government portals.' }}</p>
                </div>
                <div class="rounded-3xl border border-line bg-[#F8FAFD] p-7 shadow-xs">
                    <span class="text-2xl font-black text-yellow">04</span>
                    <h3 class="mt-3 text-base font-black text-ink">{{ $isSw ? 'Msaada Endelevu' : 'Direct Advisory' }}</h3>
                    <p class="mt-2 text-xs text-muted leading-relaxed">{{ $isSw ? 'Mawasiliano ya moja kwa moja kupitia simu, ofisi na WhatsApp.' : 'Direct telephone, in-person, and WhatsApp engineering support.' }}</p>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ========================================================================= --}}
{{-- PHYSICAL PRESENCE & DAR ES SALAAM HEADQUARTERS                            --}}
{{-- ========================================================================= --}}
<section class="py-16 sm:py-20 bg-[#F8FAFD] border-b border-line">
    <div class="shell max-w-4xl">
        <div class="rounded-3xl bg-white border border-line p-8 sm:p-12 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="space-y-3">
                <p class="eyebrow">{{ $isSw ? 'Ofisi Yetu' : 'Headquarters & Location' }}</p>
                <h3 class="text-2xl font-black text-ink">Mbagala · Dar es Salaam, Tanzania</h3>
                <p class="text-xs sm:text-sm text-muted leading-relaxed max-w-md">
                    {{ $isSw ? 'Karibu na kituo cha mafuta cha Puma. Tunakaribisha wateja ofisini na tunahudumia mtandaoni kote nchini.' : 'Located near Puma Petrol Station in Mbagala. Serving local clients and remote organizations across Tanzania.' }}
                </p>
                <p class="text-xs font-bold text-navy pt-2">
                    Operating Hours: Monday – Saturday (08:00 – 18:00 EAT)
                </p>
            </div>

            <div class="flex flex-col gap-3 shrink-0">
                <a href="{{ route('public.contact.show') }}" class="button-primary !py-3 !px-6 !text-xs font-black whitespace-nowrap text-center">
                    <span>{{ __('site.nav.contact') }}</span>
                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>
                <a href="https://wa.me/255783257716" class="wa-btn !py-3 !px-6 !text-xs justify-center text-center font-bold" target="_blank" rel="noopener">
                    <span>WhatsApp Support</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
