@extends('layouts.app')

@section('title', 'Services | Digital Star Consultants')

@section('content')
<section class="border-b border-mist-200 bg-mist-50">
    <div class="mx-auto max-w-7xl px-5 py-12 sm:px-8 lg:py-16">
        <div class="max-w-2xl">
            <p class="text-xs font-bold uppercase tracking-[.2em] text-brand-600">Service catalogue</p>
            <h1 class="mt-3 font-display text-3xl font-bold tracking-tight text-ink-900 sm:text-4xl">Find the right help for the work ahead.</h1>
            <p class="mt-4 max-w-xl text-sm leading-6 text-ink-600">Browse practical services, check what you need, and send a request when you are ready.</p>
        </div>
        <form class="mt-8 grid gap-3 rounded-2xl border border-mist-200 bg-white p-3 shadow-sm md:grid-cols-[1fr_16rem_auto]" method="GET">
            <label class="sr-only" for="service-search">Search services</label>
            <input id="service-search" class="field" name="search" value="{{ $search }}" placeholder="Search by service name">
            <label class="sr-only" for="service-category">Filter by category</label>
            <select id="service-category" class="field" name="category"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }}</option>@foreach($category->children as $child)<option value="{{ $child->slug }}" @selected($selectedCategory === $child->slug)>{{ $child->name }}</option>@endforeach @endforeach</select>
            <button class="btn btn-primary" type="submit">Filter results</button>
        </form>
        @if($serviceGroups->isNotEmpty())
            <nav class="mt-5 flex flex-wrap gap-2" aria-label="Browse service problem areas">
                @foreach($serviceGroups as $group)
                    <a href="#{{ $group['slug'] }}" class="issue-link">{{ $group['name'] }}</a>
                @endforeach
            </nav>
        @endif
    </div>
</section>

<section class="mx-auto max-w-7xl px-5 py-12 sm:px-8 lg:py-16">
    <div class="mb-6 flex items-center justify-between gap-4"><p class="text-sm font-semibold text-ink-500">{{ $services->count() }} {{ $services->count() === 1 ? 'service' : 'services' }} available</p><a href="{{ route('public.submissions.track.form') }}" class="text-sm font-bold text-brand-600 hover:text-brand-700">Already applied? Track it &rarr;</a></div>
    <div class="grid gap-4 md:grid-cols-2">
        @forelse($serviceGroups as $groupIndex => $group)
            <details id="{{ $group['slug'] }}" class="issue-group scroll-mt-32" @if($groupIndex === 0) open @endif>
                <summary class="issue-summary"><div class="flex items-start gap-4"><span class="issue-index">{{ str_pad($groupIndex + 1, 2, '0', STR_PAD_LEFT) }}</span><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-600">Problem area</p><h2 class="mt-2 font-display text-xl font-bold tracking-tight text-ink-900">{{ $group['name'] }}</h2><p class="mt-2 max-w-lg text-sm leading-6 text-ink-500">{{ $group['description'] }}</p></div></div><div class="flex shrink-0 items-center gap-3"><span class="issue-count">{{ $group['services']->count() }} {{ $group['services']->count() === 1 ? 'service' : 'services' }}</span><span class="issue-chevron" aria-hidden="true">⌄</span></div></summary>
                <div class="issue-services">
                    @foreach($group['services'] as $service)
                        <a href="{{ route('public.services.show', $service->slug) }}" class="issue-service"><span><strong class="block text-sm text-ink-900">{{ $service->name }}</strong><small class="mt-1 block text-xs leading-5 text-ink-500">{{ $service->description ?: 'Practical support from request to completion.' }}</small></span><span class="shrink-0 text-xs font-bold text-brand-600">Open <span aria-hidden="true">&rarr;</span></span></a>
                    @endforeach
                </div>
            </details>
        @empty
            <div class="rounded-xl border border-dashed border-mist-200 bg-mist-50 p-12 text-center text-sm text-ink-500">No services matched your search. Try another category or contact our team.</div>
        @endforelse
    </div>
</section>
@endsection
