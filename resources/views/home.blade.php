@extends('layouts.app', [
    'title' => 'Digital Star Consultants',
    'metaDescription' => 'Software engineering, business systems, and digital infrastructure for enterprises in Tanzania. Built in Dar es Salaam.'
])

@section('content')
@php
    $locale = app()->getLocale();
    $isSw = $locale === 'sw';
    $phones = [
        ['display' => '0783 257 716', 'tel' => '+255783257716', 'wa' => '255783257716'],
        ['display' => '0754 931 751', 'tel' => '+255754931751', 'wa' => '255754931751'],
    ];
@endphp

{{-- ===================================================================== --}}
{{-- HERO — FULL-BLEED DARK EDITORIAL                                       --}}
{{-- ===================================================================== --}}
<section class="relative bg-[#07172C] text-white overflow-hidden min-h-[92vh] flex items-center">

    {{-- Background texture: architectural grid --}}
    <div class="pointer-events-none absolute inset-0 opacity-[0.07]"
         style="background-image: linear-gradient(#F5C84B 1px, transparent 1px), linear-gradient(90deg, #F5C84B 1px, transparent 1px); background-size: 60px 60px;" aria-hidden="true"></div>

    {{-- Background atmospheric glow --}}
    <div class="pointer-events-none absolute top-0 right-0 w-[700px] h-[700px] rounded-full"
         style="background: radial-gradient(circle, rgba(26,86,219,0.18) 0%, transparent 70%);" aria-hidden="true"></div>
    <div class="pointer-events-none absolute bottom-0 left-0 w-[500px] h-[500px] rounded-full"
         style="background: radial-gradient(circle, rgba(245,200,75,0.07) 0%, transparent 70%);" aria-hidden="true"></div>

    <div class="shell relative z-10 py-20 lg:py-0">
        <div class="grid lg:grid-cols-[1fr_1fr] gap-8 lg:gap-0 items-center min-h-[92vh]">

            {{-- LEFT: Editorial text thesis --}}
            <div class="lg:py-24 lg:pr-16">

                {{-- Studio identifier --}}
                <div class="flex items-center gap-3 mb-8">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-yellow/15 border border-yellow/30">
                        <svg class="h-4 w-4 text-yellow" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L14.4 8.6L21 11L14.4 13.4L12 20L9.6 13.4L3 11L9.6 8.6L12 2Z"/>
                        </svg>
                    </div>
                    <span class="text-[11px] font-black tracking-[0.25em] uppercase text-white/50">
                        {{ $isSw ? 'Dar es Salaam, Tanzania' : 'Digital Star Consultants · Dar es Salaam' }}
                    </span>
                </div>

                {{-- Main headline — dramatic scale --}}
                <h1 class="font-black leading-[1.02] tracking-[-0.04em] text-white"
                    style="font-size: clamp(2.75rem, 5.5vw, 4.5rem);">
                    @if($isSw)
                        Teknolojia<br>
                        <span class="text-yellow">iliyoundwa</span><br>
                        kwa biashara yako.
                    @else
                        Technology built<br>
                        <span class="text-yellow">around the way</span><br>
                        your business works.
                    @endif
                </h1>

                {{-- Supporting paragraph --}}
                <p class="mt-7 text-base text-white/60 leading-relaxed max-w-[480px]"
                   style="font-size: clamp(0.9rem, 1.4vw, 1.0625rem);">
                    {{ $isSw
                        ? 'Tunaunda mifumo ya programu, dashibodi za uendeshaji, na miundombinu ya kidijitali kwa biashara na taasisi nchini Tanzania.'
                        : 'We engineer web platforms, internal workflow systems, and digital infrastructure for businesses and institutions across Tanzania.' }}
                </p>

                {{-- CTA row --}}
                <div class="mt-10 flex flex-wrap items-center gap-4">
                    <a href="{{ route('public.contact.show') }}"
                       class="inline-flex items-center gap-2.5 rounded-full bg-yellow text-[#07172C] font-black text-sm px-7 py-4 hover:bg-[#e5b839] transition-all duration-200 shadow-[0_8px_24px_rgba(245,200,75,0.35)] hover:shadow-[0_12px_32px_rgba(245,200,75,0.5)] hover:-translate-y-0.5">
                        {{ $isSw ? 'Anza Mradi' : 'Start a Project' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    </a>

                    <a href="{{ route('public.services.index') }}"
                       class="inline-flex items-center gap-2.5 rounded-full border border-white/20 text-white font-bold text-sm px-7 py-4 hover:border-white/50 hover:bg-white/5 transition-all duration-200">
                        {{ $isSw ? 'Ona Huduma Zote' : 'Explore Services' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 3l5 5-5 5"/></svg>
                    </a>

                    <a href="https://wa.me/{{ $phones[0]['wa'] }}"
                       class="flex items-center gap-2 text-sm text-white/50 hover:text-white/80 transition-colors pl-1"
                       target="_blank" rel="noopener">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>{{ $phones[0]['display'] }}</span>
                    </a>
                </div>

                {{-- Reference tracking strip --}}
                <div class="mt-12 pt-8 border-t border-white/10">
                    <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-white/35 mb-3">
                        {{ $isSw ? 'Fuatilia Ombi Lako' : 'Track an Active Request' }}
                    </p>
                    <form action="{{ route('public.track.form') }}" method="GET"
                          class="flex items-center gap-2 max-w-sm">
                        <input type="text" name="q"
                               placeholder="{{ $isSw ? 'DSC-2026-XXXXXX...' : 'Enter reference code...' }}"
                               class="flex-1 rounded-full border border-white/15 bg-white/8 backdrop-blur px-5 py-2.5 text-xs font-semibold text-white placeholder:text-white/30 focus:border-yellow/50 focus:outline-none focus:ring-2 focus:ring-yellow/20"
                               style="background: rgba(255,255,255,0.06);">
                        <button type="submit"
                                class="rounded-full bg-[#0B2545] border border-white/20 text-yellow text-xs font-black px-5 py-2.5 hover:bg-[#132845] transition-colors whitespace-nowrap">
                            {{ $isSw ? 'Fuatilia' : 'Check Status' }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- RIGHT: WebGL 3D Star + System Architecture Overlay --}}
            <div class="relative flex items-center justify-center lg:justify-end lg:py-12">

                {{-- Canvas container --}}
                <div class="relative w-full max-w-[560px] aspect-square">

                    {{-- WebGL Star --}}
                    <div id="hero-3d-canvas-container" class="absolute inset-0 rounded-[40px] overflow-hidden"
                         style="background: radial-gradient(circle at 50% 40%, rgba(26,86,219,0.12) 0%, transparent 70%);">
                    </div>

                    {{-- Fallback: pure CSS/SVG system diagram when WebGL unavailable --}}
                    <div id="hero-3d-fallback" class="absolute inset-0 flex items-center justify-center" style="display:none;">
                        <svg viewBox="0 0 400 400" class="w-full h-full" aria-hidden="true">
                            <circle cx="200" cy="200" r="195" fill="none" stroke="#1A56DB" stroke-width="0.5" opacity="0.3"/>
                            <circle cx="200" cy="200" r="140" fill="none" stroke="#F5C84B" stroke-width="0.5" opacity="0.4"/>
                            <polygon points="200,40 220,150 340,160 230,220 265,350 200,275 135,350 170,220 60,160 180,150" fill="#0B2545" stroke="#F5C84B" stroke-width="1.5" opacity="0.9"/>
                        </svg>
                    </div>

                    {{-- System status cards floating over visual --}}
                    <div class="absolute bottom-6 left-0 right-0 flex justify-between items-end px-6 pointer-events-none z-10">
                        <div class="rounded-2xl px-4 py-3 text-xs font-bold backdrop-blur-md border"
                             style="background: rgba(7,23,44,0.85); border-color: rgba(245,200,75,0.25);">
                            <div class="flex items-center gap-2 text-yellow mb-1">
                                <span class="h-1.5 w-1.5 rounded-full bg-yellow animate-pulse"></span>
                                <span class="text-[10px] font-black uppercase tracking-wider">Live Systems</span>
                            </div>
                            <p class="text-white/80 text-[11px]">DAR · {{ date('Y') }}</p>
                        </div>

                        <div class="rounded-2xl px-4 py-3 text-xs font-bold backdrop-blur-md border"
                             style="background: rgba(7,23,44,0.85); border-color: rgba(26,86,219,0.3);">
                            <p class="text-[10px] uppercase tracking-wider text-white/40 mb-1">Stack</p>
                            <p class="text-white font-black text-[11px]">Laravel · PHP · MySQL</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Bottom scroll indicator --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex flex-col items-center gap-1.5 opacity-30 animate-bounce" aria-hidden="true">
        <span class="text-[10px] font-bold tracking-[0.2em] uppercase text-white">Scroll</span>
        <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
        </svg>
    </div>
</section>

{{-- ===================================================================== --}}
{{-- WHAT WE BUILD — EDITORIAL CAPABILITIES (NOT CARDS)                    --}}
{{-- ===================================================================== --}}
<section class="bg-white border-b border-[#E2EAF4]">

    {{-- Section intro: full-width editorial label --}}
    <div class="shell py-20">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 pb-16 border-b border-[#E2EAF4]">
            <div>
                <p class="eyebrow">{{ $isSw ? 'Tunachoweza Kujenga' : 'What We Build' }}</p>
                <h2 class="mt-2 font-black tracking-tight text-[#07172C] leading-[1.08]"
                    style="font-size: clamp(2rem, 4vw, 3.25rem);">
                    {{ $isSw ? 'Mifumo ya kidijitali\ninayofanya kazi.' : 'Digital systems\nthat actually work.' }}
                </h2>
            </div>
            <p class="text-sm text-[#52667D] leading-relaxed max-w-md lg:text-right">
                {{ $isSw
                    ? 'Kutoka mifumo ya wavuti hadi dashibodi za uendeshaji. Kila mfumo tunaoijenga unafanya kazi katika hali halisi ya biashara.'
                    : 'From web platforms to internal workflow engines. Every system we build is engineered to operate under real business conditions.' }}
            </p>
        </div>
    </div>

    {{-- FLAGSHIP: Software Engineering — spans full width, dark --}}
    <div class="bg-[#07172C] relative overflow-hidden">
        <div class="pointer-events-none absolute right-0 top-0 w-[600px] h-[600px] opacity-10"
             style="background: radial-gradient(circle, #1A56DB 0%, transparent 70%);" aria-hidden="true"></div>

        <div class="shell py-20">
            <div class="grid lg:grid-cols-[1fr_0.85fr] gap-12 lg:gap-20 items-center">
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="text-[11px] font-black tracking-[0.25em] uppercase text-yellow">01</span>
                        <div class="h-px flex-1 bg-yellow/20"></div>
                        <span class="text-[11px] font-bold text-white/30 uppercase tracking-wider">Flagship Service</span>
                    </div>

                    <h3 class="font-black text-white leading-tight tracking-tight"
                        style="font-size: clamp(1.75rem, 3vw, 2.75rem);">
                        {{ $isSw ? 'Uhandisi wa Programu\n& Mifumo ya Biashara' : 'Software Engineering\n& Business Systems' }}
                    </h3>

                    <p class="mt-5 text-white/60 leading-relaxed" style="font-size: clamp(0.875rem, 1.2vw, 1rem);">
                        {{ $isSw
                            ? 'Tunatengeneza programu maalum za wavuti, portal za wateja, mifumo ya REST API, na dashibodi za uendeshaji wa ndani — zilizobuniwa kwa ajili ya kasi, usalama, na kuendelea kudumu.'
                            : 'Bespoke web applications, customer intake portals, REST API services, and internal operations dashboards — engineered for performance, security, and long-term maintainability.' }}
                    </p>

                    {{-- Capabilities list: clean, typographic --}}
                    <div class="mt-10 grid grid-cols-2 gap-x-8 gap-y-3">
                        @foreach([
                            $isSw ? 'Programu za Wavuti Maalum' : 'Custom Web Applications',
                            $isSw ? 'Mifumo ya Biashara' : 'Business Operations Systems',
                            $isSw ? 'REST APIs & Huduma za Backend' : 'REST APIs & Backend Services',
                            $isSw ? 'Portal za Wateja' : 'Customer Intake Portals',
                            $isSw ? 'Dashibodi za Uongozi' : 'Staff Management Dashboards',
                            $isSw ? 'Mifumo Yenye Usalama' : 'Role-Based Access Control',
                        ] as $cap)
                            <div class="flex items-center gap-2.5 text-sm text-white/70">
                                <span class="h-1 w-4 rounded-full bg-yellow shrink-0"></span>
                                {{ $cap }}
                            </div>
                        @endforeach
                    </div>

                    <a href="{{ route('public.services.index', ['category' => 'it-tech-consultancy']) }}"
                       class="inline-flex items-center gap-2 mt-10 text-sm font-black text-yellow hover:text-yellow/80 transition-colors">
                        {{ $isSw ? 'Tazama Huduma za Programu' : 'Explore Software Services' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    </a>
                </div>

                {{-- Right: SVG system architecture visual --}}
                <div class="relative hidden lg:block">
                    <div class="rounded-3xl overflow-hidden border border-white/10 aspect-square relative"
                         style="background: linear-gradient(135deg, rgba(26,86,219,0.08) 0%, rgba(11,37,69,0.5) 100%);">
                        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 480 480" aria-hidden="true">
                            {{-- Grid --}}
                            <defs>
                                <pattern id="sg" width="40" height="40" patternUnits="userSpaceOnUse">
                                    <path d="M40 0 L0 0 0 40" fill="none" stroke="rgba(255,255,255,0.04)" stroke-width="1"/>
                                </pattern>
                            </defs>
                            <rect width="480" height="480" fill="url(#sg)"/>

                            {{-- Central node --}}
                            <circle cx="240" cy="240" r="36" fill="#0B2545" stroke="#F5C84B" stroke-width="1.5"/>
                            <circle cx="240" cy="240" r="20" fill="#1A56DB" opacity="0.8"/>
                            <text x="240" y="244" text-anchor="middle" fill="#F5C84B" font-size="9" font-weight="900" font-family="sans-serif">API</text>

                            {{-- Orbit rings --}}
                            <circle cx="240" cy="240" r="80" fill="none" stroke="rgba(245,200,75,0.15)" stroke-width="1" stroke-dasharray="4 6"/>
                            <circle cx="240" cy="240" r="130" fill="none" stroke="rgba(26,86,219,0.2)" stroke-width="1" stroke-dasharray="3 8"/>
                            <circle cx="240" cy="240" r="185" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="1" stroke-dasharray="2 10"/>

                            {{-- Satellite nodes --}}
                            {{-- Node 1: Database --}}
                            <line x1="240" y1="160" x2="240" y2="204" stroke="#F5C84B" stroke-width="0.75" opacity="0.5"/>
                            <circle cx="240" cy="148" r="22" fill="#0B2545" stroke="#F5C84B" stroke-width="1.5"/>
                            <text x="240" y="152" text-anchor="middle" fill="white" font-size="8" font-family="sans-serif">DB</text>

                            {{-- Node 2: Frontend --}}
                            <line x1="320" y1="240" x2="276" y2="240" stroke="#1A56DB" stroke-width="0.75" opacity="0.5"/>
                            <circle cx="332" cy="240" r="22" fill="#0B2545" stroke="#1A56DB" stroke-width="1.5"/>
                            <text x="332" y="244" text-anchor="middle" fill="white" font-size="7" font-family="sans-serif">UI</text>

                            {{-- Node 3: Auth --}}
                            <line x1="240" y1="276" x2="240" y2="320" stroke="#F5C84B" stroke-width="0.75" opacity="0.5"/>
                            <circle cx="240" cy="332" r="22" fill="#0B2545" stroke="#F5C84B" stroke-width="1.5"/>
                            <text x="240" y="336" text-anchor="middle" fill="white" font-size="7" font-family="sans-serif">Auth</text>

                            {{-- Node 4: Queue --}}
                            <line x1="160" y1="240" x2="204" y2="240" stroke="#1A56DB" stroke-width="0.75" opacity="0.5"/>
                            <circle cx="148" cy="240" r="22" fill="#0B2545" stroke="#1A56DB" stroke-width="1.5"/>
                            <text x="148" y="244" text-anchor="middle" fill="white" font-size="7" font-family="sans-serif">Queue</text>

                            {{-- Diagonal nodes --}}
                            <line x1="269" y1="211" x2="311" y2="169" stroke="rgba(245,200,75,0.25)" stroke-width="0.75"/>
                            <circle cx="320" cy="160" r="16" fill="#07172C" stroke="rgba(245,200,75,0.5)" stroke-width="1"/>
                            <text x="320" y="164" text-anchor="middle" fill="rgba(255,255,255,0.5)" font-size="7" font-family="sans-serif">CDN</text>

                            <line x1="211" y1="211" x2="169" y2="169" stroke="rgba(26,86,219,0.25)" stroke-width="0.75"/>
                            <circle cx="160" cy="160" r="16" fill="#07172C" stroke="rgba(26,86,219,0.5)" stroke-width="1"/>
                            <text x="160" y="164" text-anchor="middle" fill="rgba(255,255,255,0.5)" font-size="6" font-family="sans-serif">Cloud</text>

                            <line x1="269" y1="269" x2="311" y2="311" stroke="rgba(245,200,75,0.25)" stroke-width="0.75"/>
                            <circle cx="320" cy="320" r="16" fill="#07172C" stroke="rgba(245,200,75,0.5)" stroke-width="1"/>
                            <text x="320" y="324" text-anchor="middle" fill="rgba(255,255,255,0.5)" font-size="6" font-family="sans-serif">CRON</text>

                            <line x1="211" y1="269" x2="169" y2="311" stroke="rgba(26,86,219,0.25)" stroke-width="0.75"/>
                            <circle cx="160" cy="320" r="16" fill="#07172C" stroke="rgba(26,86,219,0.5)" stroke-width="1"/>
                            <text x="160" y="324" text-anchor="middle" fill="rgba(255,255,255,0.5)" font-size="6" font-family="sans-serif">Logs</text>

                            {{-- Data flow pulses (small dots on connections) --}}
                            <circle cx="240" cy="180" r="3" fill="#F5C84B" opacity="0.7">
                                <animate attributeName="cy" from="204" to="160" dur="2s" repeatCount="indefinite" begin="0s"/>
                                <animate attributeName="opacity" from="0.9" to="0.1" dur="2s" repeatCount="indefinite" begin="0s"/>
                            </circle>
                            <circle cx="300" cy="240" r="3" fill="#1A56DB" opacity="0.7">
                                <animate attributeName="cx" from="276" to="310" dur="2.5s" repeatCount="indefinite" begin="0.5s"/>
                                <animate attributeName="opacity" from="0.9" to="0.1" dur="2.5s" repeatCount="indefinite" begin="0.5s"/>
                            </circle>
                        </svg>

                        {{-- Tech label overlay --}}
                        <div class="absolute bottom-5 left-5 flex items-center gap-2">
                            <span class="px-3 py-1.5 rounded-full text-[10px] font-black text-yellow border border-yellow/25 bg-black/30 backdrop-blur-sm">
                                Laravel 13
                            </span>
                            <span class="px-3 py-1.5 rounded-full text-[10px] font-black text-white/60 border border-white/10 bg-black/30 backdrop-blur-sm">
                                PHP 8.5
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary services: asymmetric 3-pane layout --}}
    <div class="shell py-16">
        <div class="grid lg:grid-cols-[1fr_1fr_1fr] gap-px bg-[#E2EAF4] border border-[#E2EAF4] rounded-3xl overflow-hidden">

            {{-- 02: Digital Architecture --}}
            <div class="bg-white p-8 lg:p-10">
                <div class="flex items-center gap-2 mb-5">
                    <span class="text-[11px] font-black tracking-[0.25em] uppercase text-[#1A56DB]">02</span>
                    <div class="h-px flex-1 bg-[#E2EAF4]"></div>
                </div>
                <h3 class="font-black text-[#07172C] text-xl leading-tight">
                    {{ $isSw ? 'Usanifu wa Mifumo ya Kidijitali' : 'Systems & Digital Architecture' }}
                </h3>
                <p class="mt-3 text-xs text-[#52667D] leading-relaxed">
                    {{ $isSw
                        ? 'Muundo thabiti wa database, uunganishaji wa huduma za cloud, na zana za kidijitali za uongozi.'
                        : 'Structured database design, cloud deployments, internal tooling, and technology advisory for growing enterprises.' }}
                </p>
                <div class="mt-6 space-y-2">
                    @foreach([
                        $isSw ? 'Ubunifu wa Database' : 'Database Design',
                        $isSw ? 'Kupeleka Cloud' : 'Cloud Deployment',
                        $isSw ? 'Ushauri wa Kiteknolojia' : 'Tech Advisory',
                    ] as $item)
                        <div class="flex items-center gap-2 text-[11px] font-semibold text-[#0B2545]">
                            <span class="h-1 w-3 rounded-full bg-[#1A56DB] shrink-0"></span>
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 03: Business Digitization --}}
            <div class="bg-white p-8 lg:p-10">
                <div class="flex items-center gap-2 mb-5">
                    <span class="text-[11px] font-black tracking-[0.25em] uppercase text-[#1A56DB]">03</span>
                    <div class="h-px flex-1 bg-[#E2EAF4]"></div>
                </div>
                <h3 class="font-black text-[#07172C] text-xl leading-tight">
                    {{ $isSw ? 'Mabadiliko ya Kidijitali ya Biashara' : 'Business Digitization' }}
                </h3>
                <p class="mt-3 text-xs text-[#52667D] leading-relaxed">
                    {{ $isSw
                        ? 'Kubadilisha makaratasi na utaratibu wa kizamani kuwa fomu za mtandaoni zenye ufuatiliaji wa uwazi na namba ya kumbukumbu.'
                        : 'Converting manual paperwork and offline processes into structured digital workflows with instant reference tracking.' }}
                </p>
                <div class="mt-6 space-y-2">
                    @foreach([
                        $isSw ? 'Fomu za Kidijitali' : 'Digital Intake Forms',
                        $isSw ? 'Ufuatiliaji wa Papo Hapo' : 'Live Status Tracking',
                        $isSw ? 'Rekodi Zilizopangwa' : 'Structured Records',
                    ] as $item)
                        <div class="flex items-center gap-2 text-[11px] font-semibold text-[#0B2545]">
                            <span class="h-1 w-3 rounded-full bg-[#1A56DB] shrink-0"></span>
                            {{ $item }}
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 04: Support Services --}}
            <div class="bg-[#F8FAFD] p-8 lg:p-10">
                <div class="flex items-center gap-2 mb-5">
                    <span class="text-[11px] font-black tracking-[0.25em] uppercase text-[#52667D]">04</span>
                    <div class="h-px flex-1 bg-[#E2EAF4]"></div>
                </div>
                <h3 class="font-black text-[#07172C] text-xl leading-tight">
                    {{ $isSw ? 'Huduma Rasmi & Uchapishaji' : 'Institutional & Print Services' }}
                </h3>
                <p class="mt-3 text-xs text-[#52667D] leading-relaxed">
                    {{ $isSw
                        ? 'Usaidizi wa mifumo ya NIDA, TRA, BRELA, na uchapishaji wa kibiashara wenye ufuatiliaji wa namba ya kumbukumbu.'
                        : 'NIDA, TRA, BRELA workflows, commercial print production, and institutional applications with reference tracking.' }}
                </p>
                <div class="mt-8">
                    <a href="{{ route('public.services.index') }}"
                       class="inline-flex items-center gap-2 text-xs font-black text-[#1A56DB] hover:underline">
                        {{ $isSw ? 'Tazama Huduma Zote' : 'Browse Full Catalog' }}
                        <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================================================================== --}}
{{-- SELECTED WORK — LARGE EDITORIAL PROJECT FEATURES                       --}}
{{-- ===================================================================== --}}
<section class="bg-[#F8FAFD] border-b border-[#E2EAF4] py-20 sm:py-24">
    <div class="shell">

        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-16">
            <div>
                <p class="eyebrow">{{ $isSw ? 'Kazi Zilizokamilika' : 'Selected Work' }}</p>
                <h2 class="mt-2 font-black text-[#07172C] tracking-tight"
                    style="font-size: clamp(1.875rem, 3.5vw, 2.75rem);">
                    {{ $isSw ? 'Mifumo iliyojengwa kwa matumizi halisi.' : 'Systems built for real use.' }}
                </h2>
            </div>
            <a href="{{ route('work') }}"
               class="shrink-0 inline-flex items-center gap-2 text-sm font-bold text-[#1A56DB] hover:underline">
                {{ $isSw ? 'Tazama Kazi Zote' : 'View Full Portfolio' }}
                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
            </a>
        </div>

        {{-- Case Study 01: Large full-width feature --}}
        <div class="group rounded-[32px] bg-[#07172C] overflow-hidden mb-8 relative">
            {{-- Decorative accent --}}
            <div class="absolute top-0 right-0 w-96 h-96 opacity-10 pointer-events-none"
                 style="background: radial-gradient(circle, #1A56DB 0%, transparent 70%);" aria-hidden="true"></div>

            <div class="grid lg:grid-cols-[1fr_0.9fr] gap-0">
                {{-- Content --}}
                <div class="p-10 sm:p-14 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <span class="px-3 py-1.5 rounded-full text-[10px] font-black text-yellow border border-yellow/25 bg-yellow/10">
                                Web Platform
                            </span>
                            <span class="text-[10px] font-bold text-white/30 uppercase tracking-wider">01 / Service Platform</span>
                        </div>

                        <h3 class="font-black text-white leading-tight tracking-tight"
                            style="font-size: clamp(1.5rem, 2.5vw, 2.25rem);">
                            Service Request &<br>Status Tracking Platform
                        </h3>

                        <p class="mt-4 text-white/55 text-sm leading-relaxed max-w-md">
                            A dynamic full-stack Laravel platform allowing clients to submit multi-field service requests, upload documents, and receive instant reference codes for real-time public status tracking — without creating an account.
                        </p>

                        <div class="mt-8 flex flex-wrap gap-2">
                            @foreach(['Laravel 13', 'PHP 8.5', 'MySQL', 'Tailwind v4', 'Dynamic Schemas', 'Queue Workers'] as $t)
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold text-white/60 border border-white/10 bg-white/5">
                                    {{ $t }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-10 pt-8 border-t border-white/10">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-wider text-white/30 mb-1">Approach</p>
                                <p class="text-xs font-semibold text-white/70">Schema-driven forms</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-wider text-white/30 mb-1">Tracking</p>
                                <p class="text-xs font-semibold text-white/70">Instant reference code</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-wider text-white/30 mb-1">Auth</p>
                                <p class="text-xs font-semibold text-white/70">No login required</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Visual: Stylized UI mockup in SVG --}}
                <div class="bg-[#0B2545] relative overflow-hidden hidden lg:flex items-center justify-center p-10">
                    <div class="absolute inset-0 opacity-20"
                         style="background: linear-gradient(135deg, #1A56DB 0%, transparent 60%);" aria-hidden="true"></div>

                    <div class="relative w-full max-w-[320px] space-y-3">
                        {{-- Mockup card --}}
                        <div class="rounded-2xl bg-[#07172C] border border-white/10 p-5">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span class="text-[10px] font-black text-white/70 uppercase tracking-wider">DSC-2026-A4F7X2</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black bg-yellow/20 text-yellow border border-yellow/25">In Progress</span>
                            </div>
                            <div class="space-y-2">
                                <div class="h-1.5 rounded-full bg-white/10 overflow-hidden">
                                    <div class="h-full w-2/3 rounded-full bg-gradient-to-r from-[#1A56DB] to-[#F5C84B]"></div>
                                </div>
                                <div class="flex justify-between text-[9px] text-white/30">
                                    <span>Submitted</span><span>In Review</span><span>Complete</span>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-[#07172C] border border-white/10 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-black text-white/50 uppercase tracking-wider">Service</span>
                                <span class="text-[10px] font-bold text-white/70">BRELA Company Search</span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-white/5 p-3">
                                    <p class="text-[9px] text-white/30 mb-0.5">Customer</p>
                                    <p class="text-[10px] font-bold text-white/70">J. Mwangi</p>
                                </div>
                                <div class="rounded-xl bg-white/5 p-3">
                                    <p class="text-[9px] text-white/30 mb-0.5">Submitted</p>
                                    <p class="text-[10px] font-bold text-white/70">{{ date('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Case Study 02: Operations Engine --}}
        <div class="group rounded-[32px] bg-white border border-[#E2EAF4] overflow-hidden">
            <div class="grid lg:grid-cols-[0.9fr_1fr] gap-0">

                {{-- Visual: Operations dashboard SVG --}}
                <div class="bg-[#F8FAFD] border-b lg:border-b-0 lg:border-r border-[#E2EAF4] relative overflow-hidden hidden lg:flex items-center justify-center p-10">
                    <div class="relative w-full max-w-[320px] space-y-3">
                        {{-- Admin panel mockup --}}
                        <div class="rounded-2xl bg-white border border-[#E2EAF4] shadow-lg p-5">
                            <div class="flex items-center gap-2 mb-5">
                                <div class="h-7 w-7 rounded-lg bg-[#0B2545] flex items-center justify-center">
                                    <svg class="h-3.5 w-3.5 text-[#F5C84B]" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L14.4 8.6L21 11L14.4 13.4L12 20L9.6 13.4L3 11L9.6 8.6L12 2Z"/></svg>
                                </div>
                                <span class="text-[11px] font-black text-[#07172C]">Operations Console</span>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mb-4">
                                @foreach([['12', 'Pending', 'bg-yellow/20 text-yellow'], ['4', 'In Review', 'bg-blue-50 text-blue-700'], ['31', 'Completed', 'bg-emerald-50 text-emerald-700'], ['2', 'Flagged', 'bg-red-50 text-red-600']] as [$n, $l, $cls])
                                    <div class="rounded-xl p-3 {{ $cls }} text-center">
                                        <p class="text-lg font-black leading-none">{{ $n }}</p>
                                        <p class="text-[9px] font-bold mt-1 opacity-80">{{ $l }}</p>
                                    </div>
                                @endforeach
                            </div>
                            <div class="space-y-2">
                                @foreach(['BRELA Name Search', 'TIN Registration', 'Web Development'] as $item)
                                    <div class="flex items-center justify-between py-1.5 border-b border-[#E2EAF4] last:border-0">
                                        <span class="text-[10px] font-semibold text-[#52667D]">{{ $item }}</span>
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <div class="p-10 sm:p-14">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="px-3 py-1.5 rounded-full text-[10px] font-black text-[#1A56DB] border border-[#1A56DB]/25 bg-[#1A56DB]/5">
                            Internal Operations Tool
                        </span>
                        <span class="text-[10px] font-bold text-[#52667D] uppercase tracking-wider">02 / Admin Engine</span>
                    </div>

                    <h3 class="font-black text-[#07172C] leading-tight tracking-tight"
                        style="font-size: clamp(1.5rem, 2.5vw, 2.25rem);">
                        Enterprise Operations &<br>Workflow Engine
                    </h3>

                    <p class="mt-4 text-[#52667D] text-sm leading-relaxed max-w-md">
                        A centralized role-based staff portal with automated submission assignment, multi-stage lifecycle tracking, status transition logs, staff notes, and analytics reporting — built to eliminate fragmented internal communication.
                    </p>

                    <div class="mt-8 grid grid-cols-2 gap-6">
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-[#52667D] mb-1">Access Control</p>
                            <p class="text-sm font-bold text-[#07172C]">Role-based RBAC middleware</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black uppercase tracking-wider text-[#52667D] mb-1">Lifecycle</p>
                            <p class="text-sm font-bold text-[#07172C]">4-stage status pipeline</p>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap gap-2">
                        @foreach(['Laravel Auth', 'RBAC', 'Chart Engine', 'Audit Logs', 'Encrypted Sessions'] as $t)
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold text-[#0B2545] border border-[#E2EAF4] bg-[#F8FAFD]">
                                {{ $t }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================================================================== --}}
{{-- HOW WE WORK — VISUALLY DIFFERENTIATED PROCESS                         --}}
{{-- ===================================================================== --}}
<section class="bg-white border-b border-[#E2EAF4] py-20 sm:py-24">
    <div class="shell">

        <div class="grid lg:grid-cols-[1fr_2fr] gap-16 items-start">
            {{-- Left sticky label --}}
            <div class="lg:sticky lg:top-28">
                <p class="eyebrow">{{ $isSw ? 'Jinsi Tunavyofanya Kazi' : 'How We Work' }}</p>
                <h2 class="mt-2 font-black text-[#07172C] tracking-tight leading-tight"
                    style="font-size: clamp(1.875rem, 3vw, 2.5rem);">
                    {{ $isSw ? 'Mchakato wa hatua 4.' : 'A four-stage engineering process.' }}
                </h2>
                <p class="mt-4 text-sm text-[#52667D] leading-relaxed">
                    {{ $isSw
                        ? 'Kila mradi unafuata mchakato uliopangwa kwa uwazi na unaotoa matokeo yanayotegemewa.'
                        : 'Every project follows a structured process designed for transparency and dependable delivery.' }}
                </p>
            </div>

            {{-- Right: Numbered process steps with connecting line --}}
            <div class="relative">
                {{-- Vertical connecting line --}}
                <div class="absolute left-[23px] top-12 bottom-12 w-px bg-[#E2EAF4]"></div>

                <div class="space-y-0">
                    @foreach([
                        [
                            'n' => '01',
                            'title' => $isSw ? 'Gundua & Bainisha' : 'Discover & Define',
                            'desc' => $isSw
                                ? 'Tunafafanua malengo ya biashara yako, mahitaji ya kiteknolojia, na mchakato wa kazi wa sasa hivi. Tunaunda ramani ya suluhisho sahihi.'
                                : 'We clarify your operational goals, extract technical requirements, and map your current business workflows. We design the right solution blueprint.',
                            'accent' => '#F5C84B',
                        ],
                        [
                            'n' => '02',
                            'title' => $isSw ? 'Sanidi & Ubuni' : 'Architect & Design',
                            'desc' => $isSw
                                ? 'Tunaunda muundo wa database, mikataba ya API, middleware salama, na violesura vya utumiaji rahisi.'
                                : 'We architect database schemas, API contracts, secure middleware layers, and intuitive user interfaces with clean information hierarchy.',
                            'accent' => '#1A56DB',
                        ],
                        [
                            'n' => '03',
                            'title' => $isSw ? 'Jenga & Thibitisha' : 'Build & Validate',
                            'desc' => $isSw
                                ? 'Tunaunda mfumo kwa Laravel, tunaandika majaribio ya kiotomatiski, na kuthibitisha matumizi halisi kabla ya kupeleka.'
                                : 'We engineer the system in Laravel, write automated tests, and rigorously validate real-world edge cases before any deployment.',
                            'accent' => '#0B2545',
                        ],
                        [
                            'n' => '04',
                            'title' => $isSw ? 'Toa & Saidia' : 'Launch & Support',
                            'desc' => $isSw
                                ? 'Kupeleka kwa uzalishaji laini, uwasilishaji kwa mteja, ufuatiliaji wa maombi, na ushauri wa kiteknolojia unaoendelea.'
                                : 'Smooth production deployment, client handover, live reference tracking activation, and ongoing technical advisory.',
                            'accent' => '#F5C84B',
                        ],
                    ] as $step)
                        <div class="relative flex gap-8 pb-12 last:pb-0">
                            {{-- Number node --}}
                            <div class="relative z-10 shrink-0">
                                <div class="h-12 w-12 rounded-full border-2 border-[#E2EAF4] bg-white flex items-center justify-center shadow-sm">
                                    <span class="text-sm font-black" style="color: {{ $step['accent'] }}">{{ $step['n'] }}</span>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="pt-2 pb-0">
                                <h3 class="font-black text-[#07172C] text-xl tracking-tight">{{ $step['title'] }}</h3>
                                <p class="mt-2 text-sm text-[#52667D] leading-relaxed max-w-lg">{{ $step['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>

{{-- ===================================================================== --}}
{{-- TECHNOLOGY STACK — Visual node layout                                  --}}
{{-- ===================================================================== --}}
<section class="bg-[#07172C] border-b border-[#132845] py-20 sm:py-24 relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0 opacity-[0.06]"
         style="background-image: linear-gradient(#F5C84B 1px, transparent 1px), linear-gradient(90deg, #F5C84B 1px, transparent 1px); background-size: 48px 48px;" aria-hidden="true"></div>

    <div class="shell relative z-10">
        <div class="grid lg:grid-cols-[1fr_1fr] gap-16 items-center">

            <div>
                <p class="eyebrow-yellow">{{ $isSw ? 'Teknolojia Tunazotumia' : 'Technology Stack' }}</p>
                <h2 class="mt-2 font-black text-white tracking-tight leading-tight"
                    style="font-size: clamp(1.875rem, 3vw, 2.75rem);">
                    {{ $isSw ? 'Imejaribiwa. Imehakikishwa.\nImepelekwa kwenye uzalishaji.' : 'Battle-tested.\nProduction-proven.' }}
                </h2>
                <p class="mt-4 text-white/55 text-sm leading-relaxed max-w-md">
                    {{ $isSw
                        ? 'Tunajenga kwa mifumo ya kisasa ya chanzo-wazi iliyothibitishwa, miundo iliyoandikwa kwa usalama, na usanifu unaoweza kukua.'
                        : 'We build exclusively with modern proven open-source frameworks, typed secure architectures, and scalable cloud-ready infrastructure.' }}
                </p>

                {{-- Tech grid: typographic node display --}}
                <div class="mt-10 grid grid-cols-2 gap-3">
                    @foreach([
                        ['name' => 'Laravel 13', 'role' => 'Backend Framework', 'accent' => '#F5C84B'],
                        ['name' => 'PHP 8.5', 'role' => 'Runtime', 'accent' => '#F5C84B'],
                        ['name' => 'MySQL', 'role' => 'Database Engine', 'accent' => '#1A56DB'],
                        ['name' => 'Tailwind CSS v4', 'role' => 'UI System', 'accent' => '#1A56DB'],
                        ['name' => 'REST APIs', 'role' => 'Service Layer', 'accent' => '#F5C84B'],
                        ['name' => 'Vite 8', 'role' => 'Asset Bundler', 'accent' => '#1A56DB'],
                        ['name' => 'Pest', 'role' => 'Testing Framework', 'accent' => '#F5C84B'],
                        ['name' => 'Cloud Hosting', 'role' => 'Infrastructure', 'accent' => '#1A56DB'],
                    ] as $tech)
                        <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3.5 hover:border-white/20 hover:bg-white/8 transition-all cursor-default">
                            <p class="text-xs font-black text-white leading-none">{{ $tech['name'] }}</p>
                            <p class="text-[10px] text-white/35 mt-1">{{ $tech['role'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: abstract infrastructure SVG --}}
            <div class="hidden lg:block">
                <svg viewBox="0 0 440 440" class="w-full max-w-[440px] mx-auto" aria-hidden="true">
                    <defs>
                        <radialGradient id="glow1" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" stop-color="#1A56DB" stop-opacity="0.4"/>
                            <stop offset="100%" stop-color="#1A56DB" stop-opacity="0"/>
                        </radialGradient>
                        <radialGradient id="glow2" cx="50%" cy="50%" r="50%">
                            <stop offset="0%" stop-color="#F5C84B" stop-opacity="0.25"/>
                            <stop offset="100%" stop-color="#F5C84B" stop-opacity="0"/>
                        </radialGradient>
                    </defs>

                    {{-- Background glow --}}
                    <circle cx="220" cy="220" r="180" fill="url(#glow1)"/>

                    {{-- Server rack visual --}}
                    <rect x="140" y="100" width="160" height="240" rx="12" fill="#0B2545" stroke="rgba(26,86,219,0.4)" stroke-width="1.5"/>

                    {{-- Server units --}}
                    @foreach([120, 155, 190, 225, 260, 295] as $y)
                        <rect x="152" y="{{ $y }}" width="136" height="24" rx="6" fill="rgba(255,255,255,0.04)" stroke="rgba(255,255,255,0.08)" stroke-width="0.75"/>
                        <circle cx="168" cy="{{ $y + 12 }}" r="3" fill="{{ $loop->index % 2 === 0 ? '#F5C84B' : '#22c55e' }}"/>
                        <rect x="178" y="{{ $y + 9 }}" width="60" height="3" rx="1.5" fill="rgba(255,255,255,0.1)"/>
                        <rect x="248" y="{{ $y + 9 }}" width="28" height="3" rx="1.5" fill="rgba(26,86,219,0.5)"/>
                    @endforeach

                    {{-- Connection lines going out --}}
                    <line x1="80" y1="160" x2="140" y2="190" stroke="rgba(245,200,75,0.3)" stroke-width="1" stroke-dasharray="4 4"/>
                    <line x1="80" y1="280" x2="140" y2="240" stroke="rgba(26,86,219,0.3)" stroke-width="1" stroke-dasharray="4 4"/>
                    <line x1="300" y1="160" x2="360" y2="130" stroke="rgba(245,200,75,0.3)" stroke-width="1" stroke-dasharray="4 4"/>
                    <line x1="300" y1="280" x2="360" y2="310" stroke="rgba(26,86,219,0.3)" stroke-width="1" stroke-dasharray="4 4"/>

                    {{-- Endpoint nodes --}}
                    <circle cx="68" cy="160" r="12" fill="#07172C" stroke="rgba(245,200,75,0.6)" stroke-width="1.5"/>
                    <text x="68" y="164" text-anchor="middle" fill="rgba(245,200,75,0.8)" font-size="7" font-family="sans-serif">API</text>

                    <circle cx="68" cy="280" r="12" fill="#07172C" stroke="rgba(26,86,219,0.6)" stroke-width="1.5"/>
                    <text x="68" y="284" text-anchor="middle" fill="rgba(255,255,255,0.6)" font-size="7" font-family="sans-serif">DB</text>

                    <circle cx="372" cy="130" r="12" fill="#07172C" stroke="rgba(245,200,75,0.6)" stroke-width="1.5"/>
                    <text x="372" y="134" text-anchor="middle" fill="rgba(245,200,75,0.8)" font-size="6" font-family="sans-serif">CDN</text>

                    <circle cx="372" cy="310" r="12" fill="#07172C" stroke="rgba(26,86,219,0.6)" stroke-width="1.5"/>
                    <text x="372" y="314" text-anchor="middle" fill="rgba(255,255,255,0.6)" font-size="6" font-family="sans-serif">SSL</text>

                    {{-- Yellow accent glow on center top --}}
                    <circle cx="220" cy="100" r="20" fill="url(#glow2)"/>
                    <rect x="200" y="86" width="40" height="14" rx="4" fill="rgba(245,200,75,0.15)" stroke="rgba(245,200,75,0.5)" stroke-width="1"/>
                    <text x="220" y="96" text-anchor="middle" fill="#F5C84B" font-size="7" font-weight="bold" font-family="sans-serif">LIVE</text>
                </svg>
            </div>
        </div>
    </div>
</section>

{{-- ===================================================================== --}}
{{-- FAQ — EDITORIAL ACCORDION                                               --}}
{{-- ===================================================================== --}}
<section class="bg-white border-b border-[#E2EAF4] py-20 sm:py-24">
    <div class="shell">
        <div class="grid lg:grid-cols-[1fr_1.5fr] gap-16 items-start">

            <div class="lg:sticky lg:top-28">
                <p class="eyebrow">FAQ</p>
                <h2 class="mt-2 font-black text-[#07172C] tracking-tight"
                    style="font-size: clamp(1.75rem, 3vw, 2.5rem);">
                    {{ $isSw ? 'Maswali yanayoulizwa mara nyingi.' : 'Frequently asked questions.' }}
                </h2>
                <p class="mt-4 text-sm text-[#52667D] leading-relaxed">
                    {{ $isSw
                        ? 'Yote unayohitaji kujua kuhusu kufanya kazi na Digital Star Consultants.'
                        : 'Everything you need to know about working with Digital Star Consultants.' }}
                </p>
                <div class="mt-8">
                    <a href="{{ route('public.contact.show') }}"
                       class="inline-flex items-center gap-2 text-sm font-black text-[#1A56DB] hover:underline">
                        {{ $isSw ? 'Uliza Swali Lingine' : 'Ask a Different Question' }}
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    </a>
                </div>
            </div>

            <div class="divide-y divide-[#E2EAF4]">
                @foreach([
                    [
                        'q' => 'How do I start a project with Digital Star?',
                        'q_sw' => 'Ninawezaje kuanza mradi na Digital Star?',
                        'a' => 'Reach out via our Contact page or WhatsApp. We discuss your goals, define requirements, propose an architecture, and execute in clear milestones.',
                        'a_sw' => 'Wasiliana nasi kupitia ukurasa wa Mawasiliano au WhatsApp. Tutajadili malengo yako, kubainisha mahitaji, kupendekeza usanifu, na kutekeleza kwa hatua zilizo wazi.',
                    ],
                    [
                        'q' => 'Can you build custom web applications and portals?',
                        'q_sw' => 'Je, mnaweza kutengeneza programu maalum za wavuti na portal?',
                        'a' => 'Yes. We engineer bespoke web applications, customer portals, staff dashboards, and API services — built for performance, security, and long-term maintainability.',
                        'a_sw' => 'Ndiyo. Tunatengeneza programu maalum za wavuti, portal za wateja, dashibodi za watumishi, na mifumo ya API — iliyoundwa kwa kasi, usalama na uimara.',
                    ],
                    [
                        'q' => 'How does service request tracking work?',
                        'q_sw' => 'Ufuatiliaji wa maombi ya huduma unafanya kazi vipi?',
                        'a' => 'Select a service, complete the intake form, and upload required documents. You instantly receive a unique reference code (e.g. DSC-2026-XXXXXX) for live status tracking — no account needed.',
                        'a_sw' => 'Chagua huduma, jaza fomu, na upakie nyaraka. Utapata namba ya kumbukumbu (mf. DSC-2026-XXXXXX) kufuatilia ombi lako moja kwa moja bila akaunti.',
                    ],
                    [
                        'q' => 'Do you assist with NIDA, TRA, BRELA, and official applications?',
                        'q_sw' => 'Je, mnasaidia na NIDA, TRA, BRELA, na maombi rasmi?',
                        'a' => 'Yes. We provide structured assistance for NIDA identity services, TRA tax registrations, BRELA business filings, and other official institutional applications.',
                        'a_sw' => 'Ndiyo. Tunatoa usaidizi kwa mifumo ya NIDA, TRA, BRELA, na maombi mengine rasmi ya taasisi.',
                    ],
                    [
                        'q' => 'Can I consult directly via WhatsApp?',
                        'q_sw' => 'Ninaweza kushauriana moja kwa moja kupitia WhatsApp?',
                        'a' => 'Yes. Our team is available on WhatsApp at 0783 257 716 and 0754 931 751 for project scoping, inquiries, and follow-ups on active submissions.',
                        'a_sw' => 'Ndiyo. Timu yetu inapatikana WhatsApp kupitia 0783 257 716 na 0754 931 751 kwa ushauri, maelezo, na ufuatiliaji wa maombi.',
                    ],
                ] as $faq)
                    <details class="group py-6">
                        <summary class="flex cursor-pointer items-start justify-between gap-6 text-base font-bold text-[#07172C] list-none focus:outline-none select-none">
                            <span>{{ $isSw ? $faq['q_sw'] : $faq['q'] }}</span>
                            <span class="mt-0.5 shrink-0 flex h-7 w-7 items-center justify-center rounded-full bg-[#F8FAFD] border border-[#E2EAF4] text-[#52667D] group-open:rotate-45 transition-transform duration-200 group-open:bg-[#0B2545] group-open:text-white group-open:border-[#0B2545]">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                                </svg>
                            </span>
                        </summary>
                        <p class="mt-4 text-sm text-[#52667D] leading-relaxed pr-12">
                            {{ $isSw ? $faq['a_sw'] : $faq['a'] }}
                        </p>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ===================================================================== --}}
{{-- FINAL CTA — PREMIUM CLOSING STATEMENT                                  --}}
{{-- ===================================================================== --}}
<section class="bg-[#07172C] py-24 sm:py-32 relative overflow-hidden">
    {{-- Architectural grid --}}
    <div class="pointer-events-none absolute inset-0 opacity-[0.05]"
         style="background-image: linear-gradient(#F5C84B 1px, transparent 1px), linear-gradient(90deg, #F5C84B 1px, transparent 1px); background-size: 60px 60px;" aria-hidden="true"></div>

    {{-- Gold glow --}}
    <div class="pointer-events-none absolute bottom-0 right-1/4 w-[500px] h-[500px] rounded-full"
         style="background: radial-gradient(circle, rgba(245,200,75,0.12) 0%, transparent 70%);" aria-hidden="true"></div>

    <div class="shell relative z-10 text-center">
        <p class="text-[11px] font-black tracking-[0.25em] uppercase text-yellow mb-6">
            {{ $isSw ? 'Anza Mazungumzo' : 'Start a Conversation' }}
        </p>

        <h2 class="font-black text-white tracking-tight leading-[1.04]"
            style="font-size: clamp(2.5rem, 6vw, 5rem);">
            {{ $isSw ? 'Una mfumo\nunaostahili kujengwa?' : 'Have a system\nworth building?' }}
        </h2>

        <p class="mt-6 text-white/50 text-base leading-relaxed max-w-xl mx-auto">
            {{ $isSw
                ? 'Zungumza moja kwa moja na wahandisi wetu Dar es Salaam. Tutapanga, kuunda, na kupeleka mfumo unaofanya kazi kwa biashara yako.'
                : 'Talk directly with our engineering team in Dar es Salaam. We will plan, design, and deploy a system that works the way your business does.' }}
        </p>

        <div class="mt-12 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route('public.contact.show') }}"
               class="inline-flex items-center gap-3 rounded-full bg-yellow text-[#07172C] font-black text-sm px-9 py-4.5 hover:bg-[#e5b839] transition-all duration-200 shadow-[0_8px_32px_rgba(245,200,75,0.4)] hover:shadow-[0_16px_48px_rgba(245,200,75,0.55)] hover:-translate-y-0.5"
               style="padding: 18px 36px;">
                {{ $isSw ? 'Anza Mradi Wako' : 'Start a Project' }}
                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
            </a>

            <a href="https://wa.me/{{ $phones[0]['wa'] }}?text={{ urlencode('Hello, I would like to discuss a software project with Digital Star Consultants.') }}"
               class="inline-flex items-center gap-3 rounded-full border border-white/20 text-white font-bold text-sm px-9 py-4.5 hover:border-white/50 hover:bg-white/5 transition-all duration-200"
               style="padding: 18px 36px;" target="_blank" rel="noopener">
                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                WhatsApp: {{ $phones[0]['display'] }}
            </a>
        </div>

        <div class="mt-16 pt-12 border-t border-white/10 flex flex-col sm:flex-row items-center justify-center gap-8 text-xs text-white/30">
            <span>Mbagala · Dar es Salaam, Tanzania</span>
            <span class="hidden sm:block h-1 w-1 rounded-full bg-white/20"></span>
            <span>{{ $phones[0]['display'] }} · {{ $phones[1]['display'] }}</span>
            <span class="hidden sm:block h-1 w-1 rounded-full bg-white/20"></span>
            <span>Monday – Saturday · 08:00 – 18:00 EAT</span>
        </div>
    </div>
</section>

@endsection
