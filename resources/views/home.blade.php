@extends('layouts.app')

@section('title', 'Digital Star Consultants — Digital & Government Services')
@section('meta_description', 'Submit and track professional service requests with Digital Star Consultants.')

@section('content')
    {{-- Hero --}}
    <section class="hero-gradient relative overflow-hidden text-white">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8 lg:py-24">
            <div class="max-w-2xl">
                <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-accent-400">
                    Digital Star Consultants
                </p>
                <h1 class="font-display text-4xl font-bold leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                    Clear process.<br>
                    <span class="text-accent-400">Reliable results.</span>
                </h1>
                <p class="mt-5 max-w-xl text-base leading-relaxed text-white/75 sm:text-lg">
                    From business and tax paperwork to digital and government services —
                    submit your request online and track progress with a reference number.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('public.services.index') }}" class="btn-accent">
                        Browse services
                    </a>
                    <a href="{{ url('/track') }}" class="btn-ghost">
                        Track a request
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="mb-8 max-w-xl">
            <h2 class="font-display text-2xl font-bold text-ink-900 sm:text-3xl">How it works</h2>
            <p class="mt-2 text-ink-600">Four simple steps from request to result.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($steps as $step)
                <div class="surface-card p-5">
                    <span class="step-badge">{{ $step['n'] }}</span>
                    <h3 class="mt-4 font-display text-lg font-semibold text-ink-900">{{ $step['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-ink-600">{{ $step['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Categories / services --}}
    <section class="border-t border-ink-100 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
            <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h2 class="font-display text-2xl font-bold text-ink-900 sm:text-3xl">Our services</h2>
                    <p class="mt-2 text-ink-600">Choose a category to get started.</p>
                </div>
                <a href="{{ route('public.services.index') }}" class="text-sm font-semibold text-brand-700 hover:text-brand-800">
                    View all services →
                </a>
            </div>

            @if ($categories->isEmpty())
                <div class="rounded-2xl border border-dashed border-ink-200 bg-ink-50 px-6 py-12 text-center text-ink-500">
                    Services will appear here once they are published.
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <a href="{{ route('public.services.index', ['category' => $category->slug]) }}"
                           class="surface-card group block p-6">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-display text-lg font-semibold text-ink-900 group-hover:text-brand-700">
                                    {{ $category->name }}
                                </h3>
                                <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand-700">
                                    {{ $category->services->count() + $category->children->sum(fn ($c) => $c->services->count()) }}
                                </span>
                            </div>
                            @if ($category->description)
                                <p class="mt-2 line-clamp-2 text-sm text-ink-600">{{ $category->description }}</p>
                            @endif

                            @php
                                $previewServices = $category->services->take(3);
                                if ($previewServices->isEmpty()) {
                                    $previewServices = $category->children->flatMap->services->take(3);
                                }
                            @endphp

                            @if ($previewServices->isNotEmpty())
                                <ul class="mt-4 space-y-1.5 border-t border-ink-100 pt-4">
                                    @foreach ($previewServices as $service)
                                        <li class="truncate text-sm text-ink-500">{{ $service->name }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- CTA --}}
    <section class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-3xl bg-brand-900 px-6 py-10 text-white sm:px-10 lg:flex lg:items-center lg:justify-between">
            <div class="max-w-xl">
                <h2 class="font-display text-2xl font-bold sm:text-3xl">Already submitted a request?</h2>
                <p class="mt-2 text-brand-100">Use your reference number to check status anytime.</p>
            </div>
            <a href="{{ url('/track') }}" class="btn-accent mt-6 lg:mt-0">
                Track your request
            </a>
        </div>
    </section>
@endsection
