<footer class="ds-footer">
    <div class="ds-container ds-footer-grid">
        <div class="ds-footer-about">
            @include('partials.ds-logo', ['light' => true])
            <p>{{ __('site.footer.about') }}</p>
        </div>
        <div>
            <h4>{{ __('site.footer.pages_title') }}</h4>
            <ul>
                <li><a href="{{ url('/') }}">{{ __('site.nav.home') }}</a></li>
                <li><a href="{{ route('public.services.index') }}">{{ __('site.nav.services') }}</a></li>
                <li><a href="{{ route('public.forms.index') }}">{{ __('site.nav.forms') }}</a></li>
                <li><a href="{{ route('work') }}">{{ __('site.nav.work') }}</a></li>
                <li><a href="{{ route('public.track.form') }}">{{ __('site.footer.track_link') }}</a></li>
                <li><a href="{{ url('/about') }}">{{ __('site.nav.about') }}</a></li>
                <li><a href="{{ route('public.contact.show') }}">{{ __('site.nav.contact') }}</a></li>
            </ul>
        </div>
        <div>
            <h4>{{ __('site.footer.services_title') }}</h4>
            <ul>
                @foreach (__('site.footer.categories') as $slug => $label)
                    <li><a href="{{ route('public.services.index', ['category' => $slug]) }}">{{ $label }}</a></li>
                @endforeach
            </ul>
        </div>
        <div>
            <h4>{{ __('site.footer.contact_title') }}</h4>
            <ul class="ds-footer-contact">
                <li><i data-lucide="mail"></i><a href="mailto:hello@digitalstar.co.tz">hello@digitalstar.co.tz</a></li>
                <li><i data-lucide="map-pin"></i>{{ __('site.footer.country') }}</li>
                <li><i data-lucide="clock"></i>{{ __('site.footer.hours') }}</li>
            </ul>
        </div>
    </div>
    <div class="ds-footer-bottom">
        <p class="ds-container">&copy; {{ date('Y') }} Digital Star Consultants. {{ __('site.footer.rights') }}</p>
    </div>
</footer>
