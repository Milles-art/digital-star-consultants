@extends('layouts.app', ['title' => __('site.services.title')])

@section('content')
<section class="border-b border-line bg-gradient-to-b from-sky/50 to-paper">
    <div class="shell py-12 lg:py-16">
        <p class="eyebrow reveal">{{ __('site.services.eyebrow') }}</p>
        <h1 class="section-title reveal mt-2 text-ink">{{ __('site.services.title') }}</h1>
        <p class="reveal mt-3 max-w-2xl text-muted">{{ __('site.services.lead') }}</p>

        <form method="GET" action="{{ route('public.services.index') }}" class="reveal mt-8 flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="search">{{ __('site.services.search') }}</label>
                <input id="search" name="search" type="search" value="{{ $search ?? '' }}"
                    placeholder="{{ __('site.services.search_ph') }}"
                    class="w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-blue focus:ring-4 focus:ring-blue/10">
            </div>
            <div class="sm:w-56">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="category">{{ __('site.services.category') }}</label>
                <select id="category" name="category"
                    class="w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-blue focus:ring-4 focus:ring-blue/10">
                    <option value="">{{ __('site.services.all_categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(($selectedCategory ?? '') === $category->slug)>{{ $category->name }}</option>
                        @foreach ($category->children as $child)
                            <option value="{{ $child->slug }}" @selected(($selectedCategory ?? '') === $child->slug)>— {{ $child->name }}</option>
                        @endforeach
                    @endforeach
                </select>
            </div>
            <button type="submit" class="button-primary !py-3">{{ __('site.services.filter') }}</button>
        </form>
    </div>
</section>

<section class="shell py-12 lg:py-16">
    @if ($services->isEmpty())
        <div class="rounded-3xl border border-dashed border-line bg-surface px-6 py-16 text-center">
            <p class="font-semibold text-muted">{{ __('site.services.empty') }}</p>
            <a href="{{ route('public.services.index') }}" class="button-secondary mt-6 inline-flex">{{ __('site.services.clear') }}</a>
        </div>
    @else
        @foreach ($serviceGroups as $group)
            <div class="mb-12 last:mb-0">
                <div class="mb-5 reveal">
                    <h2 class="text-2xl font-black text-ink">{{ $group['name'] }}</h2>
                    @if (!empty($group['description']))
                        <p class="mt-1 text-sm text-muted">{{ $group['description'] }}</p>
                    @endif
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($group['services'] as $service)
                        <a href="{{ route('public.services.show', $service->slug) }}"
                           class="reveal group flex flex-col rounded-3xl border border-line bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue/30 hover:shadow-lg">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-lg font-black text-ink group-hover:text-blue">{{ $service->name }}</h3>
                                @if ($service->category)
                                    <span class="shrink-0 rounded-full bg-sky px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-blue">{{ $service->category->name }}</span>
                                @endif
                            </div>
                            @if ($service->description)
                                <p class="mt-3 line-clamp-3 flex-1 text-sm text-muted">{{ $service->description }}</p>
                            @endif
                            <div class="mt-5 flex items-center justify-between border-t border-line pt-4 text-sm">
                                <span class="font-black text-ink">{{ $service->formatted_price }}</span>
                                <span class="font-semibold text-muted">{{ $service->duration }}</span>
                            </div>
                            <p class="mt-3 text-sm font-bold text-blue">{{ __('site.services.apply') }} →</p>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</section>
@endsection
