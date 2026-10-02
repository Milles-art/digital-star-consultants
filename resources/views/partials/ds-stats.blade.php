{{-- Floating stats bar. Params: $stats (optional: value, label, icon), $compact (optional, smaller numbers for long values like prices) --}}
@php
    $stats = $stats ?? [
        ['value' => '100+', 'label' => 'Projects completed', 'icon' => 'trophy'],
        ['value' => '50+', 'label' => 'Happy clients', 'icon' => 'smile'],
        ['value' => '4+', 'label' => 'Years of experience', 'icon' => 'clock'],
        ['value' => '24/7', 'label' => 'Support', 'icon' => 'headphones'],
    ];
@endphp
<section class="ds-stats-wrap" aria-label="Highlights">
    <div class="ds-stats ds-reveal {{ !empty($compact) ? 'ds-stats-compact' : '' }}">
        @foreach ($stats as $stat)
            <div class="ds-stat">
                <span class="ds-icon-box"><i data-lucide="{{ $stat['icon'] }}"></i></span>
                <div style="min-width:0">
                    <strong>{{ $stat['value'] }}</strong>
                    <small>{{ $stat['label'] }}</small>
                </div>
            </div>
        @endforeach
    </div>
</section>
