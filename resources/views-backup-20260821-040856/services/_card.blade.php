@php
    $price = data_get($service, 'formatted_price') ?? (data_get($service, 'price') ? number_format(data_get($service, 'price')) . ' TZS' : null);
    $duration = data_get($service, 'duration');
    $categoryName = data_get($service, 'category.name') ?? data_get($service, 'category_name');
@endphp
<a href="{{ route('public.services.show', data_get($service, 'slug')) }}" class="group bg-white rounded-2xl border border-slate-100 hover:border-amber-200 hover:shadow-lg hover:shadow-amber-500/5 transition-all duration-300 p-5 flex flex-col h-full">
    <div class="flex items-start justify-between mb-3">
        @if($categoryName)
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 group-hover:bg-amber-50 group-hover:text-amber-700 transition-colors">
                {{ $categoryName }}
            </span>
        @endif
        <svg class="w-5 h-5 text-slate-300 group-hover:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
    </div>
    <h3 class="text-lg font-semibold text-slate-900 group-hover:text-amber-700 transition-colors">{{ data_get($service, 'name') }}</h3>
    <p class="text-sm text-slate-500 mt-2 leading-relaxed flex-1">{{ Str::limit(data_get($service, 'description', 'Professional service with clear outcomes.'), 100) }}</p>
    <div class="flex items-center gap-4 mt-4 pt-4 border-t border-slate-50 text-xs text-slate-400">
        @if($price)
            <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ $price }}</span>
        @endif
        @if($duration)
            <span class="flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> {{ $duration }}</span>
        @endif
    </div>
</a>
