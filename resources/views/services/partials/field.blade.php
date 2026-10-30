{{-- One dynamic service field. Params: $field --}}
@php
    $fieldId = 'field-' . $field->field_key;
    $name = 'fields[' . $field->field_key . ']';
    $old = old('fields.' . $field->field_key, $field->default_value);
    $placeholder = $field->placeholder ?: __('site.services.field_ph');
    $inputType = match ($field->field_type) {
        'number', 'date', 'email', 'tel', 'time' => $field->field_type,
        'datetime' => 'datetime-local',
        default => 'text',
    };
@endphp
<div class="ds-field {{ in_array($field->field_type, ['textarea', 'radio', 'checkbox']) ? 'ds-full' : '' }}" data-field-label="{{ $field->label }}">
    @if ($field->field_type === 'checkbox')
        <label class="ds-check">
            <input id="{{ $fieldId }}" type="checkbox" name="{{ $name }}" value="1" @checked($old)>
            <span>{{ $field->label }} @unless($field->is_required)<span class="ds-optional">({{ __('site.services.optional') }})</span>@endunless</span>
        </label>
        @if ($field->help_text)<span class="ds-field-help">{{ $field->help_text }}</span>@endif
    @else
        <label for="{{ $fieldId }}">
            {{ $field->label }}
            @if ($field->is_required)*@else<span class="ds-optional">({{ __('site.services.optional') }})</span>@endif
        </label>
        @if ($field->help_text)<span class="ds-field-help">{{ $field->help_text }}</span>@endif

        @if ($field->field_type === 'textarea')
            <textarea id="{{ $fieldId }}" name="{{ $name }}" rows="4" placeholder="{{ $placeholder }}">{{ $old }}</textarea>
        @elseif ($field->field_type === 'select')
            <select id="{{ $fieldId }}" name="{{ $name }}">
                <option value="">{{ __('site.services.select') }}</option>
                @foreach ($field->options ?? [] as $option)
                    <option value="{{ $option }}" @selected($old === $option)>{{ $option }}</option>
                @endforeach
            </select>
        @elseif ($field->field_type === 'radio')
            <div class="ds-choices" role="radiogroup" aria-label="{{ $field->label }}">
                @foreach ($field->options ?? [] as $index => $option)
                    <label class="ds-choice">
                        <input type="radio" id="{{ $fieldId }}-{{ $index }}" name="{{ $name }}" value="{{ $option }}"
                               @checked($old === $option)>
                        {{ $option }}
                    </label>
                @endforeach
            </div>
        @else
            <input id="{{ $fieldId }}" type="{{ $inputType }}" name="{{ $name }}" value="{{ $old }}" placeholder="{{ $placeholder }}">
        @endif
    @endif
</div>
