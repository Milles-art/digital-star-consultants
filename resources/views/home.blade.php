@extends('layouts.app')

@section('title', 'Digital Star Consultants | Technology and consulting services')
@section('meta_description', 'Apply for technology, government, business, print, and creative services from Digital Star Consultants.')

@section('content')
    <section class="relative overflow-hidden bg-ink-900 text-white">
        <div class="relative mx-auto max-w-7xl px-4 pb-20 pt-16 sm:px-6 lg:px-8 lg:pb-24 lg:pt-24">
            <div class="grid items-center gap-12 lg:grid-cols-[1.1fr_.9fr]">
                <div class="max-w-3xl">
                    <p class="text-xs font-bold uppercase tracking-[.24em] text-accent-400">Digital Star Consultants</p>
                    <h1 class="mt-5 font-display text-4xl font-extrabold leading-tight tracking-tight sm:text-6xl">Move important work forward with one capable partner.</h1>
                    <p class="mt-6 max-w-2xl text-base leading-relaxed text-slate-300 sm:text-lg">From software and government services to business support and creative production, get a clear route from request to result.</p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('public.services.index') }}" class="btn btn-primary group">Browse services <span aria-hidden="true" class="transition-transform group-hover:translate-x-1">&rarr;</span></a>
                        <a href="{{ route('public.submissions.track.form') }}" class="btn border border-white/20 text-white hover:bg-white/10">Track an application</a>
                    </div>
                    <div class="mt-10 flex flex-wrap gap-x-6 gap-y-2 text-sm text-slate-300">
                        <span><strong class="text-white">Clear requirements</strong> before you apply</span>
                        <span><strong class="text-white">Reference number</strong> for every request</span>
                    </div>
                </div>

                <div class="surface-panel relative rounded-3xl p-7 text-ink-900 sm:p-8">
                    <span class="absolute inset-x-0 top-0 h-1.5 bg-accent-400" aria-hidden="true"></span>
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-brand-600">Already applied?</p>
                    <h2 class="mt-3 font-display text-2xl font-extrabold">Check your application status.</h2>
                    <p class="mt-3 text-sm leading-relaxed text-slate-500">Enter the reference number from your confirmation message and see the latest update.</p>
                    <form action="{{ route('public.submissions.track.form') }}" method="GET" class="mt-6 space-y-3">
                        <label for="reference" class="sr-only">Reference number</label>
                        <input id="reference" name="reference" type="text" required maxlength="32" placeholder="e.g. DSC-2026-004821" class="field">
                        <button type="submit" class="btn btn-blue w-full">Check status</button>
                    </form>
                    <p class="mt-4 text-xs text-slate-500">Lost your reference? <a href="{{ route('contact') }}" class="font-semibold text-brand-600 underline-offset-4 hover:underline">Contact support</a>.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="bg-white py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-5 md:flex-row md:items-end">
                <div class="max-w-2xl">
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-brand-600">What we do</p>
                    <h2 class="mt-3 font-display text-3xl font-extrabold tracking-tight text-ink-900 sm:text-4xl">Start with the kind of help you need.</h2>
                    <p class="mt-4 leading-relaxed text-slate-600">Choose a category to see the available services, requirements, pricing, and application fields.</p>
                </div>
                <a href="{{ route('public.services.index') }}" class="inline-flex shrink-0 items-center gap-2 text-sm font-bold text-brand-600 hover:text-brand-700">View all services <span aria-hidden="true">&rarr;</span></a>
            </div>

            @if($categories->isNotEmpty())
                <div class="mt-12 grid gap-5 md:grid-cols-2">
                    @foreach($categories as $category)
                        @php
                            $hasChildren = $category->children->isNotEmpty();
                            $items = $hasChildren ? $category->children->pluck('name') : $category->services->pluck('name')->take(6);
                        @endphp
                        <article class="surface-card group relative overflow-hidden rounded-2xl p-7 sm:p-8">
                            <span class="absolute inset-y-0 left-0 w-1 bg-brand-600 transition group-hover:bg-accent-400" aria-hidden="true"></span>
                            <div class="flex items-start justify-between gap-5">
                                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-brand-600/10 text-2xl text-brand-600 ring-1 ring-brand-600/15">{{ $category->icon ?: '+' }}</div>
                                <span class="rounded-full bg-accent-100 px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-accent-800">{{ $items->count() }} {{ $hasChildren ? 'categories' : 'services' }}</span>
                            </div>
                            <h3 class="mt-6 font-display text-xl font-bold text-ink-900">{{ $category->name }}</h3>
                            <p class="mt-3 max-w-xl text-sm leading-relaxed text-slate-600">{{ $category->description ?: 'Explore practical services tailored to your needs.' }}</p>
                            @if($items->isNotEmpty())
                                <ul class="mt-6 flex flex-wrap gap-2">
                                    @foreach($items as $item)
                                        <li class="rounded-lg border border-mist-200 bg-mist-50 px-3 py-1.5 text-xs font-medium text-slate-600">{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <a href="{{ route('public.services.index', ['category' => $category->slug]) }}" class="mt-7 inline-flex items-center gap-2 text-sm font-bold text-brand-600 transition-all hover:gap-3 hover:text-brand-700">Explore {{ $category->name }} <span aria-hidden="true">&rarr;</span></a>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="mt-12 rounded-2xl border border-dashed border-mist-200 bg-mist-50 p-10 text-center text-slate-500">Services are being prepared. Please contact our team for assistance.</div>
            @endif
        </div>
    </section>

    <section class="bg-mist-100 py-20 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-xs font-bold uppercase tracking-[.2em] text-brand-600">How it works</p>
                <h2 class="mt-3 font-display text-3xl font-extrabold tracking-tight text-ink-900 sm:text-4xl">Four steps from request to result.</h2>
            </div>
            <ol class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                @foreach($steps as $step)
                    <li class="surface-card relative rounded-2xl p-6">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-accent-400 font-display text-sm font-extrabold text-ink-900 shadow-cta">{{ $step['n'] }}</span>
                        <h3 class="mt-6 font-display text-base font-bold text-ink-900">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $step['desc'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    <section class="bg-white py-20 sm:py-24">
        <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">
            <div class="relative overflow-hidden rounded-3xl bg-brand-700 p-8 text-white sm:p-10">
                <span class="absolute inset-x-0 top-0 h-1.5 bg-accent-400" aria-hidden="true"></span>
                <p class="text-sm text-white/70">A more straightforward way to get things done</p>
                <h2 class="mt-3 max-w-md font-display text-3xl font-extrabold">One request. One clear record. One team accountable for progress.</h2>
                <div class="mt-8 grid gap-3 sm:grid-cols-2">
                    @foreach(['Clear requirements', 'Transparent pricing', 'Application tracking', 'Secure document handling'] as $chip)
                        <div class="rounded-xl border border-white/15 bg-white/10 px-4 py-3 text-sm font-medium">{{ $chip }}</div>
                    @endforeach
                </div>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-[.2em] text-brand-600">Why Digital Star</p>
                <h2 class="mt-3 font-display text-3xl font-extrabold tracking-tight text-ink-900 sm:text-4xl">Less chasing. More progress.</h2>
                <p class="mt-4 leading-relaxed text-slate-600">We bring related services into one practical process so you can spend less time navigating offices, portals, and vendors.</p>
                <div class="mt-8 space-y-5">
                    @foreach([['Specialists per category', 'Dedicated teams for government, print, stationery, and IT.'], ['Clear requirements up front', 'Every service page shows what you need before you apply.'], ['Progress you can follow', 'Your reference number keeps the next update within reach.']] as [$title, $desc])
                        <div class="flex gap-4"><span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-accent-400 text-ink-900" aria-hidden="true">&#10003;</span><div><h3 class="text-sm font-bold text-ink-900">{{ $title }}</h3><p class="mt-1 text-sm text-slate-600">{{ $desc }}</p></div></div>
                    @endforeach
                </div>
                <a href="{{ route('about') }}" class="mt-8 inline-flex items-center gap-2 text-sm font-bold text-brand-600 hover:text-brand-700">More about us <span aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
    </section>

    <section class="bg-accent-400 pb-20 pt-16 sm:pb-24">
        <div class="mx-auto max-w-6xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="font-display text-3xl font-extrabold tracking-tight text-ink-900 sm:text-4xl">Ready to start your request?</h2>
            <p class="mx-auto mt-4 max-w-2xl text-ink-900/75">Tell us what you need and we will help you find the right service and next step.</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3"><a href="{{ route('public.services.index') }}" class="btn btn-blue">Start a request</a><a href="{{ route('contact') }}" class="btn bg-white text-ink-900 hover:bg-mist-100">Contact us</a></div>
        </div>
    </section>
@endsection
