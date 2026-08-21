@extends('layouts.app')
@section('title', 'Services — Digital Star Consultants')
@section('meta_description', 'Browse practical digital services for government, business, and personal needs. Clear support with a confident outcome.')
@section('content')

{{-- Header --}}
<section class="bg-slate-900 py-16 lg:py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl">
            <span class="text-sm font-semibold text-amber-400 uppercase tracking-wide">The service directory</span>
            <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-bold text-white mt-3 leading-tight">Clear, practical support for digital, government, and business needs.</h1>
            <p class="text-slate-300 mt-4 text-lg">Choose a category or search to begin. Every service is designed around a real outcome, not jargon.</p>
            <div class="flex flex-wrap items-center gap-4 mt-6 text-sm text-slate-400">
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> No account needed to submit</span>
                <span class="flex items-center gap-2"><svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Track with a reference number</span>
            </div>
        </div>
    </div>
</section>

{{-- Filters + Search --}}
<section class="bg-white border-b border-slate-100 sticky top-16 z-30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <form method="GET" action="{{ route('public.services.index') }}" class="flex-1 flex gap-3">
                <div class="relative flex-1">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search services..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all">
                </div>
                <select name="category" onchange="this.form.submit()" class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-400 transition-all min-w-[160px]">
                    <option value="">All categories</option>
                    @if(isset($categories))
                        @foreach($categories as $cat)
                            <option value="{{ data_get($cat, 'slug') }}" {{ ($selectedCategory ?? '') == data_get($cat, 'slug') ? 'selected' : '' }}>{{ data_get($cat, 'name') }}</option>
                        @endforeach
                    @endif
                </select>
                @if(($search ?? '') || ($selectedCategory ?? ''))
                    <a href="{{ route('public.services.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-rose-600 border border-slate-200 hover:border-rose-200 transition-colors">Clear</a>
                @endif
            </form>
        </div>
    </div>
</section>

{{-- Service Groups --}}
<section class="py-12 lg:py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(isset($serviceGroups) && count($serviceGroups))
            <div class="space-y-16">
                @foreach($serviceGroups as $group)
                    <div>
                        <div class="flex items-end justify-between mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-slate-900">{{ data_get($group, 'name') }}</h2>
                                <p class="text-sm text-slate-500 mt-1">{{ data_get($group, 'description') }}</p>
                            </div>
                            <span class="text-sm text-slate-400">{{ count(data_get($group, 'services', [])) }} services</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach(data_get($group, 'services', []) as $service)
                                @include('services._card', ['service' => $service])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($services as $service)
                    @include('services._card', ['service' => $service])
                @empty
                    <div class="col-span-full text-center py-16">
                        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-700">No services found</h3>
                        <p class="text-sm text-slate-500 mt-1">Try a different search term or category.</p>
                    </div>
                @endforelse
            </div>
        @endif

        @if(method_exists($services, 'links'))
            <div class="mt-10">
                {{ $services->withQueryString()->links() }}
            </div>
        @endif
    </div>
</section>

@endsection
