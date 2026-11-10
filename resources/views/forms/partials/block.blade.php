{{--
    Renders one content block from resources/lang/{locale}/forms.php.

    Supported types:
      lead | lines | checks | choices | groups | declaration |
      terms | signatures | office | pricing | note
--}}
@php $type = $block['type'] ?? 'lead'; @endphp

@switch($type)

    @case('lead')
        <p class="pf-lead">{{ $block['text'] }}</p>
        @break

    @case('note')
        <p class="pf-note">{{ $block['text'] }}</p>
        @break

    @case('lines')
        @php
            $columns = (int) ($block['columns'] ?? 1);
            $numbered = $block['numbered'] ?? false;
            $start = $block['start'] ?? 1;
        @endphp
        <ol class="pf-lines {{ $columns > 1 ? 'pf-lines-' . $columns : '' }}"
            @unless($numbered) style="list-style:none" @endunless
            @if($numbered) start="{{ $start }}" @endif>
            @foreach ($block['items'] as $index => $label)
                <li class="pf-line">
                    <span class="pf-line-label">{{ $label }}</span>
                    <span class="pf-line-rule" aria-hidden="true"></span>
                </li>
            @endforeach
        </ol>
        @break

    @case('checks')
        <ul class="pf-checks">
            @foreach ($block['items'] as $item)
                <li class="pf-check">
                    <span class="pf-box" aria-hidden="true"></span>
                    <span>{{ $item }}</span>
                </li>
            @endforeach
            @if (! empty($block['other']))
                <li class="pf-check pf-check-other">
                    <span class="pf-box" aria-hidden="true"></span>
                    <span>{{ $block['other'] }}</span>
                    <span class="pf-line-rule" aria-hidden="true"></span>
                </li>
            @endif
        </ul>
        @break

    @case('choices')
        <ul class="pf-choices">
            @foreach ($block['items'] as $item)
                <li class="pf-choice">
                    <span class="pf-box pf-box-lg" aria-hidden="true"></span>
                    <span>{{ $item }}</span>
                </li>
            @endforeach
        </ul>
        @break

    @case('groups')
        <div class="pf-groups">
            @foreach ($block['items'] as $group)
                <div class="pf-group">
                    <span class="pf-group-label">{{ $group['label'] }}</span>
                    @include('forms.partials.block', ['block' => [
                        'type' => 'lines',
                        'columns' => 1,
                        'numbered' => true,
                        'items' => $group['lines'],
                    ]])
                </div>
            @endforeach
        </div>
        @break

    @case('declaration')
        <div class="pf-declaration">
            @foreach ($block['paragraphs'] as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        </div>
        @break

    @case('terms')
        <ol class="pf-terms">
            @foreach ($block['items'] as $term)
                <li>{{ $term }}</li>
            @endforeach
        </ol>
        @break

    @case('signatures')
        <div class="pf-signatures">
            @foreach ($block['items'] as $label)
                <div class="pf-signature">
                    <span class="pf-line-rule" aria-hidden="true"></span>
                    <small>{{ $label }}</small>
                </div>
            @endforeach
        </div>
        @break

    @case('office')
        <div class="pf-office">
            <div class="pf-office-fields">
                @foreach ($block['fields'] as $label)
                    <div class="pf-line">
                        <span class="pf-line-label">{{ $label }}</span>
                        <span class="pf-line-rule" aria-hidden="true"></span>
                    </div>
                @endforeach
            </div>
            <div class="pf-stamp">{{ __('forms.common.stamp') }}</div>
        </div>
        @break

    @case('pricing')
        <table class="pf-pricing">
            <thead>
                <tr>
                    @foreach ($block['columns'] as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($block['rows'] as $row)
                    <tr>
                        <td class="pf-num">{{ $row['no'] }}</td>
                        <td class="pf-package"><span class="pf-package-badge">{{ $row['package'] }}</span></td>
                        <td>
                            <ul class="pf-price-details">
                                @foreach ($row['details'] as $detail)
                                    <li>{{ $detail }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="pf-price">{{ $row['price'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @break

@endswitch
