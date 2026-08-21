@extends('layouts.app')
@section('title', 'Digital Star Consultants — Make important work move')
@section('content')

{{-- ===== HERO ===== --}}
<section class="relative bg-slate-900 overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-0 right-0 w-96 h-96 bg-amber-500 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-blue-500 rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold uppercase tracking-wide mb-6">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                Trusted across 12 countries
            </div>
            <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                Move the work that <span class="text-amber-400">matters</span> forward.
            </h1>
            <p class="text-lg sm:text-xl text-slate-300 leading-relaxed mb-8 max-w-2xl">
                From government requests to business systems, we turn complex next steps into clear, confident progress — for organizations and individuals alike.
            </p>
            <div class="flex flex-wrap items-center gap-4 mb-10">
                <a href="{{ route('public.services.index') }}" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-slate-900 bg-amber-400 hover:bg-amber-300 transition-colors shadow-lg shadow-amber-400/20">Browse services</a>
                <a href="{{ route('public.submissions.track', ['reference' => 'demo-ref']) }}" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-white border border-white/20 hover:bg-white/10 transition-colors">Track a request</a>
            </div>
            <div class="flex flex-wrap items-center gap-6 text-sm text-slate-400">
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> No account needed</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Reference tracking</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Multilingual support</span>
            </div>
        </div>
    </div>
</section>

{{-- ===== STATS ===== --}}
<section class="bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-3xl font-bold text-slate-900">12,400+</div>
                <div class="text-sm text-slate-500 mt-1">Requests completed</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-slate-900">48h</div>
                <div class="text-sm text-slate-500 mt-1">Average response</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-slate-900">12</div>
                <div class="text-sm text-slate-500 mt-1">Countries served</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-slate-900">98%</div>
                <div class="text-sm text-slate-500 mt-1">Client satisfaction</div>
            </div>
        </div>
    </div>
</section>

{{-- ===== SERVICES ===== --}}
<section class="py-20 lg:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-sm font-semibold text-amber-600 uppercase tracking-wide">What we help with</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mt-3">One place for the next right move.</h2>
            <p class="text-slate-500 mt-4">Browse practical services built around real needs, not jargon. Choose a starting point and we will take it from there.</p>
        </div>

        @if(isset($categories) && count($categories))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('public.services.index', ['category' => data_get($category, 'slug')]) }}" class="group bg-white rounded-2xl p-6 border border-slate-100 hover:border-amber-200 hover:shadow-lg hover:shadow-amber-500/5 transition-all duration-300">
                        <div class="flex items-start justify-between mb-4">
                            <span class="text-3xl">{{ data_get($category, 'icon', '✨') }}</span>
                            <span class="text-xs font-mono text-slate-300 group-hover:text-amber-500 transition-colors">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 group-hover:text-amber-700 transition-colors">{{ data_get($category, 'name') }}</h3>
                        <p class="text-sm text-slate-500 mt-2 leading-relaxed">{{ data_get($category, 'description', 'Focused support with a clear outcome.') }}</p>
                        @if(data_get($category, 'services') && collect(data_get($category, 'services'))->isNotEmpty())
                            <div class="mt-4 pt-4 border-t border-slate-50">
                                <ul class="space-y-1">
                                    @foreach(collect(data_get($category, 'services'))->take(3) as $svc)
                                        <li class="text-xs text-slate-400 flex items-center gap-1.5">
                                            <span class="w-1 h-1 rounded-full bg-amber-400"></span>
                                            {{ data_get($svc, 'name') }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mt-4 flex items-center gap-1 text-sm font-medium text-amber-600 opacity-0 group-hover:opacity-100 transition-opacity">
                            Explore <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <p class="text-slate-500">Services are being curated. Check back shortly.</p>
            </div>
        @endif

        <div class="text-center mt-10">
            <a href="{{ route('public.services.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-semibold text-slate-700 bg-white border border-slate-200 hover:border-amber-300 hover:text-amber-700 transition-colors">
                View all services <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- ===== WHY US ===== --}}
<section class="py-20 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-sm font-semibold text-amber-600 uppercase tracking-wide">Why Digital Star</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mt-3">Less chasing. More done.</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Clarity</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">Every step explained in plain language. No jargon, no confusion, no dead ends.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Speed</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">Most requests receive a response within two business days. We respect your time.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Trust</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">Your data is handled with enterprise-grade security and used only for your request.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 bg-violet-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-lg font-semibold text-slate-900">Reach</h3>
                <p class="text-sm text-slate-500 mt-2 leading-relaxed">Multilingual support across 12 countries means we meet you where you are.</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== PROCESS ===== --}}
<section class="py-20 lg:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-sm font-semibold text-amber-600 uppercase tracking-wide">Simple by design</span>
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mt-3">A process you can follow.</h2>
        </div>
        @if(isset($steps) && count($steps))
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($steps as $step)
                    <div class="relative">
                        @if(!$loop->last)
                            <div class="hidden lg:block absolute top-8 left-full w-full h-0.5 bg-slate-200 -translate-y-1/2"></div>
                        @endif
                        <div class="w-16 h-16 bg-white rounded-2xl border border-slate-200 flex items-center justify-center mb-4 shadow-sm">
                            <span class="text-xl font-bold text-amber-600">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900">{{ data_get($step, 'title') }}</h3>
                        <p class="text-sm text-slate-500 mt-2 leading-relaxed">{{ data_get($step, 'description') }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-slate-500">Start with a service request and we will guide you through.</p>
            </div>
        @endif
    </div>
</section>

{{-- ===== CTA ===== --}}
<section class="py-20 lg:py-24 bg-slate-900 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-1/2 left-1/2 w-[600px] h-[600px] bg-amber-500 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-display text-3xl sm:text-4xl font-bold text-white mb-6">Ready to make your next move?</h2>
        <p class="text-lg text-slate-300 mb-8 max-w-xl mx-auto">Choose a service, tell us what you need, and we will handle the rest. No account required.</p>
        <a href="{{ route('public.services.index') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl text-base font-semibold text-slate-900 bg-amber-400 hover:bg-amber-300 transition-colors shadow-lg shadow-amber-400/20">
            Browse all services <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>
</section>

@endsection
