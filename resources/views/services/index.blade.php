@extends('layouts.app')

@section('title', 'Services — Digital Star Consultants')
@section('meta_description', 'Browse professional services from Digital Star Consultants.')

@section('content')
<section class="border-b border-[color:var(--color-line)] bg-white">
    <div class="shell py-10 md:py-12">
        <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">Services</h1>
        <p class="mt-2 max-w-2xl text-[color:var(--color-ink-soft)]">
            Select a service to start your request. You’ll receive a reference number to track progress.
        </p>

        <form method="GET" action="{{ route('public.services.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <input
                type="search"
                name="search"
                value="{{ $search ?? '' }}"
                placeholder="Search services…"
                class="field-input sm:max-w-xs"
            >

            <select name="category" class="field-input sm:max-w-xs" onchange="this.form.submit()">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected(($selectedCategory ?? '') === $category->slug)>
                        {{ $category->name }}
                    </option>
                    @foreach ($category->children as $child)
                        <option value="{{ $child->slug }}" @selected(($selectedCategory ?? '') === $child->slug)>
                            — {{ $child->name }}
                        </option>
                    @endforeach
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</section>

<section class="shell section !pt-10">
    @if ($services->isEmpty())
        <div class="rounded-2xl border border-dashed border-[color:var(--color-line-strong)] bg-white px-6 py-16 text-center text-[color:var(--color-ink-faint)]">
            No services found. Try a different search or category.
        </div>
    @elseif (isset($serviceGroups) && $serviceGroups->isNotEmpty())
        <div class="space-y-12">
            @foreach ($serviceGroups as $group)
                <div>
                    <div class="mb-5">
                        <h2 class="text-xl font-bold tracking-tight">{{ $group['name'] }}</h2>
                        @if (!empty($group['description']))
                            <p class="mt-1 text-sm text-[color:var(--color-ink-soft)]">{{ $group['description'] }}</p>
                        @endif
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($group['services'] as $service)
                            @include('services._card', ['service' => $service])
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as $service)
                @include('services._card', ['service' => $service])
            @endforeach
        </div>
    @endif
</section>
@endsection
