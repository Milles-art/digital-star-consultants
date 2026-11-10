{{-- Floating stats bar. Params: $stats (optional: value, label, icon), $compact (optional, smaller numbers for long values like prices) --}}
@php
    $stats = $stats ?? [
        ['value' => '100+', 'label' => __('site.common.stat_projects'), 'icon' => 'trophy'],
        ['value' => '50+', 'label' => __('site.common.stat_clients'), 'icon' => 'smile'],
        ['value' => '4+', 'label' => __('site.common.stat_years'), 'icon' => 'clock'],
        ['value' => '24/7', 'label' => __('site.common.stat_support'), 'icon' => 'headphones'],
    ];
@endphp
<section class="ds-stats-wrap" aria-label="{{ __('site.common.stats_aria') }}">
    <div class="ds-stats ds-reveal {{ !empty($compact) ? 'ds-stats-compact' : '' }}">
        @foreach ($stats as $stat)
            <div class="ds-stat">
                <span class="ds-icon-box"><i data-lucide="{{ $stat['icon'] }}"></i></span>
                <div style="min-width:0">
                    <strong data-count="{{ $stat['value'] }}">{{ $stat['value'] }}</strong>
                    <small>{{ $stat['label'] }}</small>
                </div>
            </div>
        @endforeach
    </div>
</section>
