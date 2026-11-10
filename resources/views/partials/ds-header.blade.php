@php
    $dsNav = [
        ['label' => __('site.nav.home'), 'url' => url('/'), 'active' => request()->is('/')],
        ['label' => __('site.nav.services'), 'url' => route('public.services.index'), 'active' => request()->routeIs('public.services.*')],
        ['label' => __('site.nav.forms'), 'url' => route('public.forms.index'), 'active' => request()->routeIs('public.forms.*')],
        ['label' => __('site.nav.work'), 'url' => route('work'), 'active' => request()->routeIs('work')],
        ['label' => __('site.nav.track'), 'url' => route('public.track.form'), 'active' => request()->routeIs('public.track.*')],
        ['label' => __('site.nav.about'), 'url' => url('/about'), 'active' => request()->is('about*')],
        ['label' => __('site.nav.contact'), 'url' => route('public.contact.show'), 'active' => request()->routeIs('public.contact.*')],
    ];

    // Kiswahili is the default; the switch always points at the *other* language.
    $otherLocale = app()->getLocale() === 'sw' ? 'en' : 'sw';
    $otherLabel = $otherLocale === 'sw' ? 'SW' : 'EN';
@endphp

<header class="ds-header">
    <div class="ds-container ds-header-inner">
        @include('partials.ds-logo')

        <nav class="ds-nav" aria-label="Main">
            @foreach ($dsNav as $link)
                <a href="{{ $link['url'] }}" class="{{ $link['active'] ? 'is-active' : '' }}" @if($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="ds-header-actions">
            <a href="{{ route('locale.switch', $otherLocale) }}" class="ds-lang-switch"
               hreflang="{{ $otherLocale }}" aria-label="{{ __('site.common.language') }}: {{ strtoupper($otherLocale) }}">
                {{ $otherLabel }}
            </a>
            <a href="{{ route('public.contact.show') }}" class="ds-btn ds-btn-primary ds-btn-sm ds-header-cta">
                {{ __('site.nav.quote') }} <i data-lucide="arrow-right"></i>
            </a>
            <button type="button" class="ds-menu-btn" data-menu-open aria-label="Open menu">
                <i data-lucide="menu"></i>
            </button>
        </div>
    </div>
</header>

{{-- Mobile slide-out menu --}}
<div class="ds-drawer-backdrop" data-menu-close></div>
<aside class="ds-drawer" aria-label="Mobile menu">
    <div class="ds-drawer-head">
        @include('partials.ds-logo')
        <button type="button" class="ds-menu-btn" data-menu-close aria-label="Close menu" style="display:grid">
            <i data-lucide="x"></i>
        </button>
    </div>
    <nav>
        @foreach ($dsNav as $link)
            <a href="{{ $link['url'] }}" class="{{ $link['active'] ? 'is-active' : '' }}">{{ $link['label'] }}</a>
        @endforeach
    </nav>
    @auth
        <a href="{{ route('profile.edit') }}" class="ds-btn ds-btn-soft ds-btn-block" style="margin-bottom:10px">
            <i data-lucide="user-round"></i> {{ __('site.nav.profile') }}
        </a>
    @endauth
    <a href="{{ route('locale.switch', $otherLocale) }}" class="ds-btn ds-btn-soft ds-btn-block" style="margin-bottom:10px"
       hreflang="{{ $otherLocale }}">
        <i data-lucide="languages"></i> {{ __('site.common.language') }} · {{ strtoupper($otherLocale) }}
    </a>
    <a href="{{ route('public.contact.show') }}" class="ds-btn ds-btn-primary ds-btn-block">
        {{ __('site.nav.quote') }} <i data-lucide="arrow-right"></i>
    </a>
</aside>
