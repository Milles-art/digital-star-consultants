@php
    $steps = $steps ?? array_map(fn ($s) => ['title' => $s['title'], 'copy' => $s['copy'], 'icon' => $s['icon']], __('site.common.proc_steps'));
@endphp
<section class="ds-section ds-section-tint">
    <div class="ds-container">
        @include('partials.ds-heading', ['kicker' => $kicker, 'title' => $title, 'copy' => $copy, 'center' => true])
        <div class="ds-steps">
            <div class="ds-steps-line" aria-hidden="true"></div>
            @foreach ($steps as $step)
                <article class="ds-step ds-reveal" style="--d:{{ $loop->index * 0.1 }}s">
                    <span class="ds-step-icon">
                        <i data-lucide="{{ $step['icon'] ?? 'circle-check-big' }}"></i>
                        <span class="ds-step-num">{{ $loop->iteration }}</span>
                    </span>
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['copy'] ?? ($step['desc'] ?? '') }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
