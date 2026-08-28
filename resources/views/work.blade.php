@extends('layouts.app', [
    'title' => __('site.nav.work'),
    'metaDescription' => 'Engineered systems and software portfolio by Digital Star Consultants — Bespoke web applications, operations dashboards, and digital workflows in Tanzania.'
])

@section('content')
@php
    $locale = app()->getLocale();
    $isSw = $locale === 'sw';
@endphp

{{-- ========================================================================= --}}
{{-- WORK / PORTFOLIO HERO                                                     --}}
{{-- ========================================================================= --}}
<section class="border-b border-line bg-gradient-to-b from-[#F2F6FB] via-[#F8FAFD] to-white py-14 lg:py-20">
    <div class="shell">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-line shadow-xs mb-5">
                <span class="h-2 w-2 rounded-full bg-yellow"></span>
                <span class="text-[11px] font-black uppercase tracking-[0.18em] text-navy">{{ __('site.nav.work') }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-ink tracking-tight">
                {{ $isSw ? 'Mifumo na Miradi Tuliyotengeneza' : 'Engineered Systems & Portfolio' }}
            </h1>
            <p class="mt-4 text-sm sm:text-base text-muted leading-relaxed">
                {{ $isSw ? 'Mifano halisi ya programu za wavuti, dashibodi za uendeshaji, na zana za kidijitali tulizobuni, kuziunda na kuziweka kwenye matumizi halisi ya kibiashara.' : 'A selection of bespoke web applications, operational dashboards, and digital tools we have architected, engineered, and shipped for real-world operations.' }}
            </p>
        </div>
    </div>
</section>

{{-- ========================================================================= --}}
{{-- FLAGSHIP CASE STUDIES (WITH 3D VISUALS)                                   --}}
{{-- ========================================================================= --}}
<section class="py-16 sm:py-20 bg-canvas border-b border-line">
    <div class="shell space-y-12">
        
        {{-- Case 01 --}}
        <article class="rounded-3xl border border-line bg-white p-8 sm:p-12 shadow-md hover:shadow-xl transition-all duration-300 group overflow-hidden">
            <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div class="space-y-5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky text-[11px] font-black text-blue">
                        <span>Web Platform · Custom Architecture</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-ink group-hover:text-blue transition-colors">
                        Service Request & Status Tracking Platform
                    </h2>
                    <div class="space-y-3 text-xs sm:text-sm text-muted leading-relaxed">
                        <div>
                            <span class="font-bold text-navy uppercase text-[11px] tracking-wider block mb-1">Challenge & Context:</span>
                            <p>Clients required an intuitive digital channel to submit multi-field requests, upload identification documents, and track lifecycle status in real time without account friction.</p>
                        </div>
                        <div>
                            <span class="font-bold text-navy uppercase text-[11px] tracking-wider block mb-1">Delivered Solution:</span>
                            <p>Engineered a robust Laravel platform featuring dynamic schema form generation, asynchronous file handling, automated reference generation (DSC-2026-XXXXXX), and frictionless public status lookup.</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-line flex flex-wrap gap-2">
                        @foreach (['Laravel 13', 'MySQL', 'Tailwind CSS v4', 'Vite', 'REST APIs', 'Queue Workers'] as $pill)
                            <span class="px-2.5 py-1 rounded-lg bg-surface text-[11px] font-bold text-navy border border-line">{{ $pill }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl overflow-hidden border border-line shadow-lg bg-surface">
                    <img src="{{ asset('images/case-study-platform.jpg') }}" alt="Service Request Platform Mockup" class="w-full h-auto object-cover group-hover:scale-103 transition-transform duration-500">
                </div>
            </div>
        </article>

        {{-- Case 02 --}}
        <article class="rounded-3xl border border-line bg-white p-8 sm:p-12 shadow-md hover:shadow-xl transition-all duration-300 group overflow-hidden">
            <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                <div class="rounded-2xl overflow-hidden border border-line shadow-lg bg-surface order-2 lg:order-1">
                    <img src="{{ asset('images/case-study-ops.jpg') }}" alt="Operations Dashboard UI" class="w-full h-auto object-cover group-hover:scale-103 transition-transform duration-500">
                </div>

                <div class="space-y-5 order-1 lg:order-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky text-[11px] font-black text-blue">
                        <span>Internal Operations Portal</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-black text-ink group-hover:text-blue transition-colors">
                        Enterprise Operations & Workflow Engine
                    </h2>
                    <div class="space-y-3 text-xs sm:text-sm text-muted leading-relaxed">
                        <div>
                            <span class="font-bold text-navy uppercase text-[11px] tracking-wider block mb-1">Challenge & Context:</span>
                            <p>Staff needed a centralized control center to assign incoming submissions, review multi-field payloads, manage status transitions, and communicate directly with clients.</p>
                        </div>
                        <div>
                            <span class="font-bold text-navy uppercase text-[11px] tracking-wider block mb-1">Delivered Solution:</span>
                            <p>Built a role-based management dashboard with automated assignment queues, multi-stage submission lifecycle controls, status transition logs, and analytics reporting.</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-line flex flex-wrap gap-2">
                        @foreach (['Laravel', 'Role Middleware (RBAC)', 'Chart Engine', 'Automated Logs', 'Encrypted Sessions'] as $pill)
                            <span class="px-2.5 py-1 rounded-lg bg-surface text-[11px] font-bold text-navy border border-line">{{ $pill }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </article>

    </div>
</section>

{{-- ========================================================================= --}}
{{-- GRAPHICS & BRAND IDENTITY WORK                                            --}}
{{-- ========================================================================= --}}
<section class="py-16 sm:py-20 bg-[#F8FAFD] border-b border-line">
    <div class="shell">
        <div class="max-w-xl mb-12">
            <p class="eyebrow">{{ $isSw ? 'Ubunifu na Chapa' : 'Design & Production' }}</p>
            <h2 class="text-2xl sm:text-3xl font-black text-ink tracking-tight mt-1">
                {{ $isSw ? 'Utambulisho wa Brand na Uchapishaji' : 'Brand Identity & Professional Print' }}
            </h2>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($graphicsProjects as $proj)
                <article class="rounded-3xl border border-line bg-white p-7 shadow-xs hover:border-blue/40 transition-all flex flex-col justify-between">
                    <div>
                        <span class="badge-tech">{{ $proj['tag'] }}</span>
                        <h3 class="mt-4 text-lg font-black text-ink">
                            {{ $isSw ? $proj['title_sw'] : $proj['title'] }}
                        </h3>
                        <p class="mt-2 text-xs text-muted leading-relaxed">
                            {{ $isSw ? $proj['summary_sw'] : $proj['summary'] }}
                        </p>
                    </div>

                    <div class="mt-6 pt-4 border-t border-line">
                        <span class="text-xs font-bold text-navy">{{ $proj['stack'] }}</span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ========================================================================= --}}
{{-- CONVERSION ANCHOR                                                         --}}
{{-- ========================================================================= --}}
<section class="shell py-16 sm:py-20">
    <div class="rounded-3xl bg-gradient-to-br from-navy via-navy-dark to-navy text-white p-8 sm:p-12 border border-line-dark shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <p class="text-xs font-black uppercase tracking-[0.2em] text-yellow">{{ $isSw ? 'Anza Mradi Wako' : 'Start Your Project' }}</p>
            <h2 class="mt-2 text-2xl sm:text-3xl font-black text-white">
                {{ $isSw ? 'Una wazo la mfumo au programu?' : 'Have a software or digital system to engineer?' }}
            </h2>
            <p class="mt-2 text-xs sm:text-sm text-white/75 max-w-lg">
                {{ $isSw ? 'Zungumza na wahandisi wetu Dar es Salaam kupanga mfumo wako wa kidijitali.' : 'Consult directly with our engineering team in Dar es Salaam to plan and deploy your custom solution.' }}
            </p>
        </div>
        <a href="{{ route('public.contact.show') }}" class="button-primary !py-3.5 !px-7 !text-xs font-black whitespace-nowrap shrink-0">
            <span>{{ __('site.home.cta_primary') }}</span>
            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
        </a>
    </div>
</section>
@endsection
