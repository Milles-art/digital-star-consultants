@extends('layouts.digitalstar')

@section('title', $content['card']['title'] . ' | Digital Star Consultants')
@section('description', \Illuminate\Support\Str::limit(strip_tags($content['card']['summary']), 155))

@push('head')
    <link rel="stylesheet" href="{{ asset('css/form-print.css') }}?v={{ @filemtime(public_path('css/form-print.css')) }}">
@endpush

@section('hero-crumbs')
    <a href="{{ route('home') }}">{{ __('site.nav.home') }}</a>
    <i data-lucide="chevron-right"></i>
    <a href="{{ route('public.forms.index') }}">{{ __('site.nav.forms') }}</a>
    <i data-lucide="chevron-right"></i>
    <span aria-current="page">{{ $content['card']['title'] }}</span>
@endsection

@section('hero-actions')
    <button type="button" class="ds-btn ds-btn-mint" data-print-form>
        <i data-lucide="printer"></i> {{ __('forms.common.print') }}
    </button>
    <a href="{{ route('public.forms.index') }}" class="ds-btn ds-btn-ghost-light">{{ __('forms.common.back') }}</a>
@endsection

@section('content')
    <section class="ds-section" style="padding-top:32px">
        <div class="ds-container">

            <div class="pf-toolbar ds-reveal">
                <p class="pf-hint"><i data-lucide="info"></i> {{ __('forms.common.download_hint') }}</p>
                <div class="pf-toolbar-actions">
                    <a class="ds-btn ds-btn-soft" href="{{ route('public.services.index') }}">
                        {{ __('forms.index.services_link') }}
                    </a>
                    <button type="button" class="ds-btn ds-btn-primary" data-print-form>
                        <i data-lucide="printer"></i> {{ __('forms.common.print') }}
                    </button>
                </div>
            </div>

            {{-- ================= PRINTED A4 SHEET ================= --}}
            <article class="pf-page ds-reveal" style="--d:.08s">

                <header class="pf-head">
                    <div class="pf-brand">
                        @include('partials.ds-logo')
                    </div>
                    <h1 class="pf-title">{{ $content['title'] }}</h1>
                </header>

                <div class="pf-contact-bar">
                    @foreach ($content['office'] as $item)
                        <span><i data-lucide="{{ $item['icon'] }}"></i> {{ $item['text'] }}</span>
                    @endforeach
                </div>

                @foreach ($content['sections'] as $section)
                    <section class="pf-section">
                        <h2 class="pf-section-head">{{ $section['heading'] }}</h2>
                        @foreach ($section['blocks'] as $block)
                            @include('forms.partials.block', ['block' => $block])
                        @endforeach
                    </section>
                @endforeach

                @if (! empty($content['footer_box']))
                    <section class="pf-footer-box">
                        <div class="pf-footer-main">
                            <p class="pf-footer-title">{{ $content['footer_box']['title'] }}</p>

                            <div class="pf-footer-grid">
                                <div>
                                    <span class="pf-chip">{{ $content['footer_box']['fee_label'] }}</span>
                                    <p class="pf-footer-fee">
                                        <strong>{{ $content['footer_box']['fee'] }}</strong>
                                    </p>
                                </div>
                                <div>
                                    <small>{{ $content['footer_box']['contact_label'] }}</small>
                                    <ul class="pf-phones">
                                        @foreach ($content['footer_box']['phones'] as $phone)
                                            <li><a href="tel:{{ str_replace(' ', '', $phone) }}">{{ $phone }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="pf-footer-seal" aria-hidden="true">
                            <i data-lucide="globe-2"></i>
                            <small>PASIPOTI</small>
                        </div>
                    </section>

                    <p class="pf-banner">{{ $content['footer_box']['banner'] }}</p>
                @endif

                <footer class="pf-sheet-foot">
                    <span>{{ __('forms.common.footer_tagline') }}</span>
                    <span>Digital Star Consultants · Mbagala Rangi 3, Dar es Salaam</span>
                </footer>
            </article>
            {{-- ================= /PRINTED A4 SHEET ================= --}}

        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-print-form]').forEach((btn) => {
            btn.addEventListener('click', () => window.print());
        });
    });
</script>
@endpush
