@extends('layouts.admin', ['title' => 'Fields: '.$service->name, 'eyebrow' => 'Form builder'])

@section('content')
    <div class="admin-two-column">
        <section class="admin-panel reveal">
            <div class="admin-panel-header">
                <div>
                    <p class="admin-kicker">{{ $service->category?->name ?? 'Service' }}</p>
                    <h2 class="admin-panel-title">{{ $fields->count() }} fields</h2>
                </div>
                <a class="admin-button admin-button-muted" href="{{ route('admin.services.index') }}">Back to services</a>
            </div>
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead><tr><th>Label</th><th>Key</th><th>Type</th><th>Required</th><th>Order</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($fields as $field)
                            <tr>
                                <td><span class="font-bold text-ink">{{ $field->label }}</span><span class="block text-xs text-muted">{{ $field->help_text }}</span></td>
                                <td>{{ $field->field_key }}</td>
                                <td>{{ $field->type_label }}</td>
                                <td><span class="admin-badge {{ $field->is_required ? 'is-warning' : 'is-secondary' }}">{{ $field->is_required ? 'Required' : 'Optional' }}</span></td>
                                <td>{{ $field->sort_order }}</td>
                                <td class="text-right">
                                    <form method="POST" action="{{ route('admin.fields.destroy', $field) }}" data-ajax data-success-reload data-confirm="Delete this field?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-button admin-button-danger" type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6">@include('admin.partials.empty', ['message' => 'No fields have been added for this service.'])</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="admin-panel reveal-delay">
            <h2 class="admin-panel-title">Add field</h2>
            <form method="POST" action="{{ route('admin.fields.store', $service) }}" class="admin-form-stack" data-ajax data-success-reload>
                @csrf
                <label class="admin-label" for="label">Label</label>
                <input class="admin-field" id="label" name="label" required>
                <label class="admin-label" for="field_type">Type</label>
                <select class="admin-field" id="field_type" name="field_type" required>
                    @foreach ($fieldTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <label class="admin-label" for="placeholder">Placeholder</label>
                <input class="admin-field" id="placeholder" name="placeholder">
                <label class="admin-label" for="help_text">Help text</label>
                <textarea class="admin-field" id="help_text" name="help_text" rows="2"></textarea>
                <label class="admin-label" for="options">Options</label>
                <textarea class="admin-field" id="options" name="options_text" rows="3" data-options-list placeholder="One option per line for select, radio, or checkbox fields"></textarea>
                <div class="grid gap-3 sm:grid-cols-2">
                    <div><label class="admin-label" for="field_sort_order">Sort</label><input class="admin-field" id="field_sort_order" name="sort_order" type="number" min="0" value="0"></div>
                    <label class="admin-check"><input type="checkbox" name="is_required" value="1" checked> Required</label>
                </div>
                <button class="admin-button admin-button-dark" type="submit">Create field</button>
            </form>
        </aside>
    </div>
@endsection
