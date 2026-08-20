@extends('layouts.app')

@section('title', 'Digital Star Consultants — Clear process. Reliable results.')
@section('meta_description', 'Submit and track professional service requests with Digital Star Consultants. No account required.')

@section('content')
<section class="hero-glow">
    <div class="shell section">
        <div class="max-w-3xl">
            <p class="eyebrow">Digital Star Consultants</p>
            <h1 class="mt-4 text-4xl font-extrabold tracking-tight text-[color:var(--color-ink)] sm:text-5xl lg:text-6xl">
                Clear process.<br>
                <span class="underline-accent">Reliable results.</span>
            </h1>
            <p class="mt-5 max-w-xl text-lg leading-relaxed text-[color:var(--color-ink-soft)]">
                From business and tax paperwork to digital and government services —
                submit online and track progress with a reference number.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('public.services.index') }}" class="btn btn-lg btn-primary">Browse services</a>
                <a href="{{ url('/track') }}" class="btn btn-lg btn-outline">Track a request</a>
            </div>
        </div>
    </div>
</section>

<section class="shell section !pt-0">
    <div class="mb-10 max-w-xl">
        <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">How it works</h2>
        <p class="mt-2 text-[color:var(--color-ink-soft)]">Four simple steps from request to result.</p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ($steps as $step)
            <div class="card p-6">
                <span class="step-number">{{ $step['n'] }}</span>
                <h3 class="mt-4 text-lg font-bold tracking-tight">{{ $step['title'] }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-[color:var(--color-ink-soft)]">{{ $step['desc'] }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="border-t border-[color:var(--color-line)] bg-white">
    <div class="shell section">
        <div class="mb-10 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Our services</h2>
                <p class="mt-2 text-[color:var(--color-ink-soft)]">Choose a category to get started.</p>
            </div>
            <a href="{{ route('public.services.index') }}" class="text-sm font-semibold text-[color:var(--color-brand-700)] hover:underline">
                View all services →
            </a>
        </div>

        @if ($categories->isEmpty())
            <div class="rounded-2xl border border-dashed border-[color:var(--color-line-strong)] bg-[color:var(--color-surface-muted)] px-6 py-16 text-center text-[color:var(--color-ink-faint)]">
                Services will appear here once they are published.
            </div>
        @else
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    <a href="{{ route('public.services.index', ['category' => $category->slug]) }}"
                       class="card card-hover group block p-6">
                        <div class="flex items-start justify-between gap-3">
                            <h3 class="text-lg font-bold tracking-tight group-hover:text-[color:var(--color-brand-700)]">
                                {{ $category->name }}
                            </h3>
                            <span class="badge">
                                {{ $category->services->count() + $category->children->sum(fn ($c) => $c->services->count()) }}
                            </span>
                        </div>
                        @if ($category->description)
                            <p class="mt-2 line-clamp-2 text-sm text-[color:var(--color-ink-soft)]">{{ $category->description }}</p>
                        @endif

                        @php
                            $preview = $category->services->take(3);
                            if ($preview->isEmpty()) {
                                $preview = $category->children->flatMap->services->take(3);
                            }
                        @endphp

                        @if ($preview->isNotEmpty())
                            <ul class="mt-4 space-y-1.5 border-t border-[color:var(--color-line)] pt-4">
                                @foreach ($preview as $service)
                                    <li class="truncate text-sm text-[color:var(--color-ink-faint)]">{{ $service->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="shell section">
    <div class="overflow-hidden rounded-3xl bg-[color:var(--color-brand-900)] px-6 py-10 text-white sm:px-10 lg:flex lg:items-center lg:justify-between">
        <div class="max-w-xl">
            <h2 class="text-2xl font-bold sm:text-3xl">Already submitted a request?</h2>
            <p class="mt-2 text-white/75">Use your reference number to check status anytime.</p>
        </div>
        <a href="{{ url('/track') }}" class="btn btn-accent mt-6 lg:mt-0">Track your request</a>
    </div>
</section>
@endsection
