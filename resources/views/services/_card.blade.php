<a href="{{ route('public.services.show', $service->slug) }}" class="surface-card group flex flex-col p-5">
    <div class="flex-1">
        @if ($service->category)
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-600">
                {{ $service->category->name }}
            </p>
        @endif
        <h3 class="mt-1 font-display text-lg font-semibold text-ink-900 group-hover:text-brand-700">
            {{ $service->name }}
        </h3>
        @if ($service->description)
            <p class="mt-2 line-clamp-2 text-sm text-ink-600">{{ $service->description }}</p>
        @endif
    </div>

    <div class="mt-4 flex items-center justify-between border-t border-ink-100 pt-4">
        <span class="text-sm font-semibold text-ink-800">
            {{ $service->formatted_price ?? ($service->price ? 'TSh '.number_format($service->price, 0) : 'Free') }}
        </span>
        <span class="text-sm font-medium text-brand-700 group-hover:underline">
            Start request →
        </span>
    </div>
</a>
