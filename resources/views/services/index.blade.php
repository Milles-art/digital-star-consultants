@extends('layouts.app')

@section('title', 'Services — Digital Star Consultants')
@section('meta_description', 'Browse professional services from Digital Star Consultants.')

@section('content')
    <section class="border-b border-ink-100 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <h1 class="font-display text-3xl font-bold text-ink-900 sm:text-4xl">Services</h1>
            <p class="mt-2 max-w-2xl text-ink-600">
                Select a service to start your request. You’ll receive a reference number to track progress.
            </p>

            {{-- Search + filters --}}
            <form method="GET" action="{{ route('public.services.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
                <input
                    type="search"
                    name="search"
                    value="{{ $search ?? '' }}"
                    placeholder="Search services…"
                    class="form-input sm:max-w-xs"
                >

                <select name="category" class="form-input sm:max-w-xs" onchange="this.form.submit()">
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

                <button type="submit" class="btn-primary !py-2.5">Search</button>
            </form>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @if ($services->isEmpty())
            <div class="rounded-2xl border border-dashed border-ink-200 bg-white px-6 py-16 text-center text-ink-500">
                No services found. Try a different search or category.
            </div>
        @elseif (isset($serviceGroups) && $serviceGroups->isNotEmpty())
            <div class="space-y-12">
                @foreach ($serviceGroups as $group)
                    <div>
                        <div class="mb-4">
                            <h2 class="font-display text-xl font-bold text-ink-900">{{ $group['name'] }}</h2>
                            @if (!empty($group['description']))
                                <p class="mt-1 text-sm text-ink-600">{{ $group['description'] }}</p>
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
