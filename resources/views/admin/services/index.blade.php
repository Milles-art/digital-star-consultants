@extends('layouts.admin', ['title' => 'Services', 'eyebrow' => 'Catalog'])

@section('content')
    <section class="admin-panel reveal">
        <form method="GET" action="{{ route('admin.services.index') }}" class="admin-filter-grid">
            <input class="admin-field" type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search services">
            <select class="admin-field" name="category_id">
                <option value="">All categories</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <select class="admin-field" name="is_active">
                <option value="">Any status</option>
                <option value="1" @selected(($filters['is_active'] ?? '') === '1')>Active</option>
                <option value="0" @selected(($filters['is_active'] ?? '') === '0')>Inactive</option>
            </select>
            <button class="admin-button admin-button-dark" type="submit">Filter</button>
            <a href="{{ route('admin.services.index') }}" class="admin-button admin-button-muted">Reset</a>
        </form>
    </section>

    <div class="admin-two-column">
        <section class="admin-panel reveal-delay">
            <div class="admin-panel-header">
                <div>
                    <p class="admin-kicker">Services</p>
                    <h2 class="admin-panel-title">{{ $services->count() }} configured services</h2>
                </div>
            </div>
            <div class="grid gap-4">
                @forelse ($services as $service)
                    <article class="admin-resource-card">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-black text-ink">{{ $service->name }}</h3>
                                <span class="admin-badge {{ $service->is_active ? 'is-success' : 'is-secondary' }}">{{ $service->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            <p class="mt-1 text-sm text-muted">{{ $service->category?->name ?? 'No category' }} - {{ $service->formatted_price }} - {{ $service->duration }}</p>
                            <p class="mt-3 text-sm text-muted">{{ $service->description }}</p>
                        </div>
                        <div class="admin-actions">
                            <a class="admin-button admin-button-muted" href="{{ route('admin.fields.index', $service) }}">Fields</a>
                            <form method="POST" action="{{ route('admin.services.toggle-active', $service) }}" data-ajax data-success-reload>@csrf <button class="admin-button admin-button-muted" type="submit">{{ $service->is_active ? 'Pause' : 'Activate' }}</button></form>
                            <form method="POST" action="{{ route('admin.services.destroy', $service) }}" data-ajax data-success-reload data-confirm="Delete this service?">@csrf @method('DELETE') <button class="admin-button admin-button-danger" type="submit">Delete</button></form>
                        </div>
                    </article>
                @empty
                    @include('admin.partials.empty', ['message' => 'No services yet.'])
                @endforelse
            </div>
        </section>

        <aside class="admin-panel reveal-delay-2">
            <h2 class="admin-panel-title">New service</h2>
            <form method="POST" action="{{ route('admin.services.store') }}" class="admin-form-stack" data-ajax data-success-reload>
                @csrf
                <label class="admin-label" for="service_category_id">Category</label>
                <select class="admin-field" id="service_category_id" name="service_category_id" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <label class="admin-label" for="name">Name</label>
                <input class="admin-field" id="name" name="name" required>
                <label class="admin-label" for="description">Description</label>
                <textarea class="admin-field" id="description" name="description" rows="3"></textarea>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div><label class="admin-label" for="price">Price</label><input class="admin-field" id="price" name="price" type="number" min="0" step="0.01"></div>
                    <div><label class="admin-label" for="duration_minutes">Minutes</label><input class="admin-field" id="duration_minutes" name="duration_minutes" type="number" min="0"></div>
                </div>
                <label class="admin-label" for="sort_order">Sort order</label>
                <input class="admin-field" id="sort_order" name="sort_order" type="number" min="0" value="0">
                <button class="admin-button admin-button-dark" type="submit">Create service</button>
            </form>
        </aside>
    </div>
@endsection
