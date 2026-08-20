<a href="{{ route('public.services.show', $service->slug) }}" class="card card-hover group flex flex-col p-5">
    <div class="flex-1">
        @if ($service->category)
            <p class="text-xs font-semibold uppercase tracking-wider text-[color:var(--color-brand-600)]">
                {{ $service->category->name }}
            </p>
        @endif
        <h3 class="mt-1 text-lg font-bold tracking-tight group-hover:text-[color:var(--color-brand-700)]">
            {{ $service->name }}
        </h3>
        @if ($service->description)
            <p class="mt-2 line-clamp-2 text-sm text-[color:var(--color-ink-soft)]">{{ $service->description }}</p>
        @endif
    </div>

    <div class="mt-4 flex items-center justify-between border-t border-[color:var(--color-line)] pt-4">
        <span class="text-sm font-semibold">
            {{ $service->formatted_price ?? ($service->price ? 'TSh '.number_format($service->price, 0) : 'Free') }}
        </span>
        <span class="text-sm font-medium text-[color:var(--color-brand-700)] group-hover:underline">
            Start request →
        </span>
    </div>
</a>
