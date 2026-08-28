@extends('layouts.app', [
    'title' => __('site.nav.services'),
    'metaDescription' => 'Explore Digital Star Consultants software engineering, systems consulting, and official digital workflow services in Dar es Salaam.'
])

@section('content')
@php
    $locale = app()->getLocale();
    $isSw = $locale === 'sw';
@endphp

{{-- ========================================================================= --}}
{{-- SERVICES DIRECTORY HERO & CONTROLS                                        --}}
{{-- ========================================================================= --}}
<section class="border-b border-line bg-gradient-to-b from-[#F2F6FB] via-[#F8FAFD] to-white py-14 lg:py-20">
    <div class="shell">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-line shadow-xs mb-5">
                <span class="h-2 w-2 rounded-full bg-yellow"></span>
                <span class="text-[11px] font-black uppercase tracking-[0.18em] text-navy">{{ __('site.nav.services') }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-ink tracking-tight">
                {{ $isSw ? 'Orodha ya Huduma na Mifumo' : 'Services & Solutions Directory' }}
            </h1>
            <p class="mt-4 text-sm sm:text-base text-muted leading-relaxed">
                {{ $isSw ? 'Kutoka uhandisi wa programu na mifumo ya kidijitali hadi maombi rasmi ya serikali na biashara yenye ufuatiliaji wa papo hapo.' : 'From custom software engineering and digital architecture to structured institutional applications with end-to-end status tracking.' }}
            </p>
        </div>

        {{-- Search & Category Filter Bar --}}
        <div class="mt-10 pt-8 border-t border-line/80 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <form action="{{ route('public.services.index') }}" method="GET" class="flex items-center gap-2 w-full md:max-w-md">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="{{ $isSw ? 'Tafuta huduma au mfumo…' : 'Search software or service…' }}"
                       class="w-full rounded-full border border-line bg-white px-5 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">
                <button type="submit" class="button-navy !py-3 !px-6 !text-xs whitespace-nowrap font-bold">
                    <span>{{ $isSw ? 'Tafuta' : 'Search' }}</span>
                </button>
            </form>

            <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 text-xs">
                <a href="{{ route('public.services.index') }}" 
                   class="px-4 py-2 rounded-full font-bold transition-all whitespace-nowrap {{ empty($selectedCategory) ? 'bg-navy text-yellow shadow-xs' : 'bg-white border border-line text-muted hover:text-navy' }}">
                    {{ $isSw ? 'Huduma Zote' : 'All Services' }}
                </a>
                @foreach ($categories as $cat)
                    <a href="{{ route('public.services.index', ['category' => $cat->slug]) }}" 
                       class="px-4 py-2 rounded-full font-bold transition-all whitespace-nowrap {{ $selectedCategory === $cat->slug ? 'bg-navy text-yellow shadow-xs' : 'bg-white border border-line text-muted hover:text-navy' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ========================================================================= --}}
{{-- SERVICES DIRECTORY GRID                                                   --}}
{{-- ========================================================================= --}}
<section class="py-16 sm:py-20 bg-canvas">
    <div class="shell">
        @if ($services->isEmpty())
            <div class="rounded-3xl border border-line bg-[#F8FAFD] p-12 text-center max-w-xl mx-auto">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky text-blue mx-auto mb-4 font-black">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h2 class="text-lg font-bold text-ink">{{ $isSw ? 'Hakuna huduma iliyopatikana' : 'No matching services found' }}</h2>
                <p class="mt-2 text-xs text-muted">{{ $isSw ? 'Jaribu kubadilisha neno la utafutaji au chagua huduma zote.' : 'Try adjusting your search terms or browsing all available categories.' }}</p>
                <div class="mt-6">
                    <a href="{{ route('public.services.index') }}" class="button-secondary !py-2 !px-4 !text-xs font-bold">
                        <span>{{ $isSw ? 'Ona Huduma Zote' : 'View All Services' }}</span>
                    </a>
                </div>
            </div>
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <article class="rounded-3xl border border-line bg-white p-7 shadow-xs hover:border-blue/40 hover:shadow-lg transition-all duration-300 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <span class="badge-tech">{{ $service->category->name ?? 'Service' }}</span>
                                @if ($service->duration)
                                    <span class="text-[10px] font-bold text-muted">{{ $service->duration }}</span>
                                @endif
                            </div>
                            <h2 class="mt-4 text-lg font-black text-ink group-hover:text-blue transition-colors">
                                <a href="{{ route('public.services.show', $service->slug) }}">
                                    {{ $service->name }}
                                </a>
                            </h2>
                            <p class="mt-2.5 text-xs text-muted leading-relaxed line-clamp-3">
                                {{ $service->description }}
                            </p>
                        </div>

                        <div class="mt-6 pt-5 border-t border-line flex items-center justify-between gap-4">
                            <div>
                                @if ($service->price > 0)
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-muted block">{{ $isSw ? 'Gharama' : 'Starting From' }}</span>
                                    <span class="text-sm font-black text-navy">{{ $service->formatted_price }}</span>
                                @else
                                    <span class="text-[11px] font-bold text-blue">{{ $isSw ? 'Ushauri / Maombi' : 'Custom Scope' }}</span>
                                @endif
                            </div>

                            <a href="{{ route('public.services.show', $service->slug) }}" class="button-navy !py-2 !px-4 !text-xs font-bold">
                                <span>{{ $isSw ? 'Fungua' : 'View Details' }}</span>
                                <svg class="h-3.5 w-3.5 text-yellow" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
