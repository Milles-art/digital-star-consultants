@extends('layouts.app')
@section('title', 'Digital Star Consultants — Make important work move')
@section('content')

{{-- ===== HERO: split layout with editorial headline and visual panel ===== --}}
<section class="relative overflow-hidden bg-ink text-white">
    <div class="grid-lines absolute inset-0 opacity-50"></div>
    <div class="hero-mesh absolute inset-0"></div>
    <div class="shell relative grid min-h-[640px] items-center gap-16 py-24 lg:grid-cols-[1.15fr_.85fr] lg:py-32">
        <div class="reveal">
            <div class="flex items-center gap-3">
                <span class="rounded-full bg-yellow/15 px-3 py-1.5 text-xs font-bold uppercase tracking-wider text-yellow">Trusted across 12 countries</span>
            </div>
            <h1 class="display mt-8 max-w-4xl">
                Move the work that matters
                <span class="relative inline-block text-yellow">forward<span class="absolute -bottom-2 left-1 h-1 w-2/3 -rotate-2 bg-yellow"></span></span>.
            </h1>
            <p class="mt-8 max-w-xl text-lg text-slate-300">From government requests to business systems, we turn complex next steps into clear, confident progress — for organizations and individuals alike.</p>
            <div class="mt-10 flex flex-col gap-3 sm:flex-row">
                <a class="button-primary" href="{{ route('public.services.index') }}">Find a service <span aria-hidden="true">↗</span></a>
                <a class="button-secondary !border-white/25 !text-white hover:!bg-white/10" href="{{ url('/track') }}">Track a request</a>
            </div>
            <div class="mt-12 flex items-center gap-6 text-sm text-slate-400">
                <span class="flex items-center gap-2"><span class="text-yellow">✓</span> No account needed</span>
                <span class="flex items-center gap-2"><span class="text-yellow">✓</span> Reference tracking</span>
                <span class="hidden items-center gap-2 sm:flex"><span class="text-yellow">✓</span> Multilingual support</span>
            </div>
        </div>
        <div class="reveal-delay relative hidden lg:block">
            <div class="rounded-[28px] border border-white/15 bg-white/5 p-6 shadow-2xl backdrop-blur-sm">
                <div class="flex items-center justify-between border-b border-white/10 pb-4">
                    <span class="text-xs font-semibold uppercase tracking-[.16em] text-slate-400">The clear path</span>
                    <span class="h-2 w-2 rounded-full bg-yellow shadow-[0_0_14px_#f5c84b]"></span>
                </div>
                <div class="py-7">
                    <div class="flex items-start gap-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-yellow text-sm font-bold text-ink">01</div>
                        <div><p class="font-semibold">Tell us what you need</p><p class="mt-1 text-sm text-slate-400">A few details is all it takes to begin.</p></div>
                    </div>
                    <div class="ml-4 h-10 border-l border-dashed border-white/20"></div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-white/25 text-sm font-bold text-white">02</div>
                        <div><p class="font-semibold">We make a plan</p><p class="mt-1 text-sm text-slate-400">Your request lands with the right people.</p></div>
                    </div>
                    <div class="ml-4 h-10 border-l border-dashed border-white/20"></div>
                    <div class="flex items-start gap-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-white/25 text-sm font-bold text-white">03</div>
                        <div><p class="font-semibold">Watch it move</p><p class="mt-1 text-sm text-slate-400">Track progress with a reference number.</p></div>
                    </div>
                </div>
                <div class="rounded-2xl bg-white/5 px-4 py-3 text-xs text-slate-300"><span class="mr-2 text-yellow">●</span> Designed for clarity at every step</div>
            </div>
        </div>
    </div>
</section>

{{-- ===== STATS BAND: trust strip with key metrics ===== --}}
<section class="border-b border-line bg-white py-10">
    <div class="shell">
        <div class="stat-band grid grid-cols-2 divide-x divide-line md:grid-cols-4">
            <div class="px-6 py-6 text-center">
                <p class="text-3xl font-bold text-ink">12,400+</p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-muted">Requests completed</p>
            </div>
            <div class="px-6 py-6 text-center">
                <p class="text-3xl font-bold text-ink">48<span class="text-lg text-blue">h</span></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-muted">Average response</p>
            </div>
            <div class="px-6 py-6 text-center">
                <p class="text-3xl font-bold text-ink">12</p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-muted">Countries served</p>
            </div>
            <div class="px-6 py-6 text-center">
                <p class="text-3xl font-bold text-ink">98<span class="text-lg text-blue">%</span></p>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-muted">Client satisfaction</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== INDUSTRIES: who we serve ===== --}}
<section id="industries" class="bg-surface py-24 lg:py-32">
    <div class="shell">
        <div class="max-w-2xl">
            <p class="eyebrow">Who we serve</p>
            <h2 class="section-title mt-5">Built for every sector.</h2>
            <p class="mt-6 text-lg text-muted">We bring the same clarity and momentum to government offices, growing businesses, and everything in between.</p>
        </div>
        <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="industry-card"><span class="icon-plate text-2xl">🏛</span><div><h3 class="text-lg font-bold">Government</h3><p class="mt-2 text-sm text-muted">Permits, registrations, and public-sector requests handled with precision and compliance.</p></div></div>
            <div class="industry-card"><span class="icon-plate text-2xl">🏢</span><div><h3 class="text-lg font-bold">Business</h3><p class="mt-2 text-sm text-muted">Formation, filings, and operational services that keep your company moving forward.</p></div></div>
            <div class="industry-card"><span class="icon-plate text-2xl">💻</span><div><h3 class="text-lg font-bold">Digital</h3><p class="mt-2 text-sm text-muted">Portal integrations, platform support, and digital transformation guidance.</p></div></div>
            <div class="industry-card"><span class="icon-plate text-2xl">⚖</span><div><h3 class="text-lg font-bold">Legal & Compliance</h3><p class="mt-2 text-sm text-muted">Document verification, legal filings, and regulatory compliance made straightforward.</p></div></div>
            <div class="industry-card"><span class="icon-plate text-2xl">📊</span><div><h3 class="text-lg font-bold">Finance</h3><p class="mt-2 text-sm text-muted">Reporting, analytics, and financial service requests with full audit trails.</p></div></div>
            <div class="industry-card"><span class="icon-plate text-2xl">🏥</span><div><h3 class="text-lg font-bold">Healthcare</h3><p class="mt-2 text-sm text-muted">Licensing, certifications, and healthcare administration support you can trust.</p></div></div>
        </div>
    </div>
</section>

{{-- ===== FEATURED SERVICES: from $categories ===== --}}
<section class="shell py-24 lg:py-32">
    <div class="grid gap-12 lg:grid-cols-[.8fr_1.2fr]">
        <div>
            <p class="eyebrow">What we help with</p>
            <h2 class="section-title mt-5 max-w-md">One place for the next right move.</h2>
            <p class="mt-6 max-w-sm text-muted">Browse practical services built around real needs, not jargon. Choose a starting point and we'll take it from there.</p>
            <a class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-blue hover:text-ink" href="{{ route('public.services.index') }}">View all services <span aria-hidden="true">↗</span></a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            @forelse($categories as $category)
                <a href="{{ route('public.services.index', ['category' => data_get($category, 'slug')]) }}" class="service-card group">
                    <div class="flex items-start justify-between gap-4">
                        <span class="icon-plate">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="mt-8">
                        <h3 class="text-xl font-bold transition-colors group-hover:text-blue">{{ data_get($category, 'name') }}</h3>
                        <p class="mt-3 text-sm text-muted">{{ data_get($category, 'description', 'Focused support with a clear outcome.') }}</p>
                        @if (data_get($category, 'services') && collect(data_get($category, 'services'))->isNotEmpty())
                            <ul class="mt-4 space-y-1 text-xs text-muted">
                                @foreach (collect(data_get($category, 'services'))->take(3) as $featuredService)
                                    <li>{{ data_get($featuredService, 'name') }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                    <div class="mt-auto flex items-center gap-2 border-t border-line pt-5">
                        <span class="text-sm font-bold text-blue transition group-hover:translate-x-1">Explore</span>
                        <span class="text-blue transition group-hover:translate-x-1" aria-hidden="true">→</span>
                    </div>
                </a>
            @empty
                <div class="rounded-[22px] border border-dashed border-line p-8 text-muted">Services are being curated. Check back shortly.</div>
            @endforelse
        </div>
    </div>
</section>

{{-- ===== WHY DIGITAL STAR: value pillars ===== --}}
<section id="why" class="bg-white py-24 lg:py-32">
    <div class="shell">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div>
                <p class="eyebrow">Why Digital Star</p>
                <h2 class="section-title mt-5 max-w-xl">Less chasing. More done.</h2>
            </div>
            <p class="max-w-xs text-sm text-muted">A calm, transparent process for high-stakes tasks and everyday requests alike.</p>
        </div>
        <div class="mt-16 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            <div class="value-pillar">
                <span class="text-4xl font-bold text-blue/20">01</span>
                <h3 class="mt-6 text-xl font-bold">Clarity</h3>
                <p class="mt-3 text-sm text-muted">Every step explained in plain language. No jargon, no confusion, no dead ends.</p>
            </div>
            <div class="value-pillar">
                <span class="text-4xl font-bold text-blue/20">02</span>
                <h3 class="mt-6 text-xl font-bold">Speed</h3>
                <p class="mt-3 text-sm text-muted">Most requests receive a response within two business days. We respect your time.</p>
            </div>
            <div class="value-pillar">
                <span class="text-4xl font-bold text-blue/20">03</span>
                <h3 class="mt-6 text-xl font-bold">Trust</h3>
                <p class="mt-3 text-sm text-muted">Your data is handled with enterprise-grade security and used only for your request.</p>
            </div>
            <div class="value-pillar">
                <span class="text-4xl font-bold text-blue/20">04</span>
                <h3 class="mt-6 text-xl font-bold">Reach</h3>
                <p class="mt-3 text-sm text-muted">Multilingual support across 12 countries means we meet you where you are.</p>
            </div>
        </div>
    </div>
</section>

{{-- ===== PROCESS TIMELINE: from $steps ===== --}}
<section class="bg-surface py-24 lg:py-32">
    <div class="shell">
        <div class="max-w-2xl">
            <p class="eyebrow">Simple by design</p>
            <h2 class="section-title mt-5">A process you can follow.</h2>
            <p class="mt-6 text-lg text-muted">From first click to finished outcome, you always know where things stand.</p>
        </div>
        <div class="mt-16">
            @forelse($steps as $step)
                @php $isFirst = $loop->first; $isLast = $loop->last; @endphp
                <div class="flex gap-6 {{ $isFirst ? '' : 'mt-[-1px]' }}">
                    <div class="flex flex-col items-center">
                        <div class="timeline-node {{ $isFirst ? 'timeline-node-active' : '' }}">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                        @if(!$isLast)<div class="w-0.5 grow bg-line" style="min-height: 48px;"></div>@endif
                    </div>
                    <div class="pb-10 pt-2">
                        <h3 class="text-xl font-bold">{{ data_get($step, 'title') }}</h3>
                        <p class="mt-3 max-w-lg text-sm text-muted">{{ data_get($step, 'description') }}</p>
                    </div>
                </div>
            @empty
                <div class="text-muted">Start with a service request and we'll guide you through.</div>
            @endforelse
        </div>
    </div>
</section>

{{-- ===== TESTIMONIAL ===== --}}
<section class="bg-white py-24 lg:py-32">
    <div class="shell">
        <div class="testimonial-card mx-auto max-w-3xl p-10 lg:p-16">
            <div class="text-5xl leading-none text-yellow">"</div>
            <blockquote class="mt-6 text-2xl font-semibold leading-snug text-ink lg:text-3xl">
                Digital Star took what would have been weeks of back-and-forth and turned it into a single afternoon. We knew exactly what to submit, when to expect a response, and where to track it.
            </blockquote>
            <div class="mt-8 flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-sky text-lg font-bold text-blue">SA</div>
                <div>
                    <p class="font-bold text-ink">Sarah Al-Mansouri</p>
                    <p class="text-sm text-muted">Operations Director, Meridian Holdings</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== FINAL CTA ===== --}}
<section class="bg-ink py-24 lg:py-32">
    <div class="hero-mesh absolute inset-0 opacity-40"></div>
    <div class="shell relative text-center">
        <h2 class="section-title mx-auto max-w-2xl text-white">Ready to make your next move?</h2>
        <p class="mx-auto mt-6 max-w-xl text-lg text-slate-300">Choose a service, tell us what you need, and we'll handle the rest. No account required.</p>
        <div class="mt-10 flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a class="button-primary" href="{{ route('public.services.index') }}">Start a request <span aria-hidden="true">↗</span></a>
            <a class="button-secondary !border-white/25 !text-white hover:!bg-white/10" href="{{ url('/track') }}">Track a request</a>
        </div>
    </div>
</section>

@endsection
