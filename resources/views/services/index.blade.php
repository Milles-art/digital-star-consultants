@extends('layouts.app', ['title' => __('site.services.title')])

@section('content')
<section class="border-b border-line bg-gradient-to-b from-[#eaf3ff66] to-white">
    <div class="shell py-12 lg:py-16">
        <p class="eyebrow rise-in">{{ __('site.services.eyebrow') }}</p>
        <h1 class="section-title rise-in mt-2 text-ink">{{ __('site.services.title') }}</h1>
        <p class="rise-in mt-3 max-w-2xl text-muted">{{ __('site.services.lead') }}</p>

        <form method="GET" action="{{ route('public.services.index') }}" class="rise-in mt-8 flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="search">{{ __('site.services.search') }}</label>
                <input id="search" name="search" type="search" value="{{ $search ?? '' }}"
                    placeholder="{{ __('site.services.search_ph') }}"
                    class="w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-blue focus:ring-4 focus:ring-blue/10">
            </div>
            <div class="sm:w-56">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="category">{{ __('site.services.category') }}</label>
                <select id="category" name="category" class="w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm font-semibold outline-none focus:border-blue focus:ring-4 focus:ring-blue/10">
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

<section class="shell py-10 lg:py-14">
    @if ($services->isEmpty())
        <div class="rounded-3xl border border-dashed border-line bg-[#f7f9fc] px-6 py-16 text-center">
            <p class="font-semibold text-muted">{{ __('site.services.empty') }}</p>
            <a href="{{ route('public.services.index') }}" class="button-secondary mt-6 inline-flex">{{ __('site.services.clear') }}</a>
        </div>
    @else
        {{-- Desktop: row list (Stripe/Helpwave style). Mobile: stacked rows --}}
        <div class="rise-in overflow-hidden rounded-3xl border border-line bg-white">
            <div class="hidden border-b border-line bg-[#f7f9fc] px-5 py-3 text-[11px] font-bold uppercase tracking-wider text-muted md:grid md:grid-cols-[1.4fr_0.8fr_0.5fr_0.5fr_auto] md:gap-4">
                <span>Service</span>
                <span>Category</span>
                <span>{{ __('site.services.price') }}</span>
                <span>{{ __('site.services.duration') }}</span>
                <span></span>
            </div>
            @foreach ($services as $service)
                <a href="{{ route('public.services.show', $service->slug) }}" class="pub-service-row no-underline text-ink">
                    <div>
                        <p class="font-black">{{ $service->name }}</p>
                        @if ($service->description)
                            <p class="mt-0.5 line-clamp-1 text-sm text-muted md:hidden">{{ $service->description }}</p>
                        @endif
                    </div>
                    <div class="text-sm font-semibold text-muted">{{ $service->category->name ?? '—' }}</div>
                    <div class="text-sm font-black">{{ $service->formatted_price }}</div>
                    <div class="text-sm font-semibold text-muted">{{ $service->duration }}</div>
                    <div class="text-sm font-bold text-blue">{{ __('site.services.apply') }} →</div>
                </a>
            @endforeach
        </div>
    @endif
</section>
@endsection
