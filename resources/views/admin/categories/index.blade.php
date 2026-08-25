@extends('layouts.admin', ['title' => 'Service Categories', 'eyebrow' => 'Catalog'])

@section('content')
    <div class="admin-two-column">
        <section class="admin-panel reveal">
            <div class="admin-panel-header">
                <div>
                    <p class="admin-kicker">Categories</p>
                    <h2 class="admin-panel-title">{{ $categories->count() }} catalog groups</h2>
                </div>
            </div>

            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Parent</th>
                            <th>Children</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>
                                    <span class="block font-bold text-ink">{{ $category->name }}</span>
                                    <span class="text-xs text-muted">{{ $category->description }}</span>
                                </td>
                                <td>{{ $category->parent?->name ?? 'Top level' }}</td>
                                <td>{{ $category->children_count }}</td>
                                <td><span class="admin-badge {{ $category->is_active ? 'is-success' : 'is-secondary' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>{{ $category->sort_order }}</td>
                                <td class="text-right">
                                    <div class="admin-actions">
                                        <form method="POST" action="{{ route('admin.categories.toggle-active', $category) }}" data-ajax data-success-reload>
                                            @csrf
                                            <button class="admin-button admin-button-muted" type="submit">{{ $category->is_active ? 'Pause' : 'Activate' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" data-ajax data-success-reload data-confirm="Delete this category?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="admin-button admin-button-danger" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">@include('admin.partials.empty', ['message' => 'No categories yet.'])</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="admin-panel reveal-delay">
            <h2 class="admin-panel-title">New category</h2>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="admin-form-stack" data-ajax data-success-reload>
                @csrf
                <label class="admin-label" for="name">Name</label>
                <input class="admin-field" id="name" name="name" required>
                <label class="admin-label" for="description">Description</label>
                <textarea class="admin-field" id="description" name="description" rows="3"></textarea>
                <label class="admin-label" for="parent_id">Parent</label>
                <select class="admin-field" id="parent_id" name="parent_id">
                    <option value="">Top level</option>
                    @foreach ($parentCategories as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
                <label class="admin-label" for="sort_order">Sort order</label>
                <input class="admin-field" id="sort_order" name="sort_order" type="number" min="0" value="0">
                <button class="admin-button admin-button-dark" type="submit">Create category</button>
            </form>
        </aside>
    </div>
@endsection
