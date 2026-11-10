@php $conditions = $conditions ?? []; @endphp
@if ($conditions)
    <small style="display:block;margin-top:2px;color:var(--ds-muted-fg)">
        @foreach ($conditions as $key => $options)
            {{ $service->fields->firstWhere('field_key', $key)?->label }}: {{ implode(' / ', $options) }}@unless($loop->last); @endunless
        @endforeach
    </small>
@endif
