@php
    $steps = $steps ?? [
        ['title' => 'Discuss your needs', 'copy' => 'We listen to understand your goals and requirements.', 'icon' => 'messages-square'],
        ['title' => 'Get a proposal', 'copy' => 'We provide a clear plan and quotation.', 'icon' => 'file-text'],
        ['title' => 'We build & deliver', 'copy' => 'Our team brings your solution to life.', 'icon' => 'rocket'],
        ['title' => 'Ongoing support', 'copy' => 'We ensure your success with continuous support.', 'icon' => 'life-buoy'],
    ];
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
