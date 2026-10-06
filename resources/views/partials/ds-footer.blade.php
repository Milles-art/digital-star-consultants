<footer class="ds-footer">
    <div class="ds-container ds-footer-grid">
        <div class="ds-footer-about">
            @include('partials.ds-logo', ['light' => true])
            <p>Your trusted digital partner in Tanzania. Help with applications, business services, printing, branding and technology.</p>
        </div>
        <div>
            <h4>Pages</h4>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('public.services.index') }}">Services</a></li>
                <li><a href="{{ route('work') }}">Our Work</a></li>
                <li><a href="{{ route('public.track.form') }}">Track an application</a></li>
                <li><a href="{{ url('/about') }}">About</a></li>
                <li><a href="{{ route('public.contact.show') }}">Contact</a></li>
            </ul>
        </div>
        <div>
            <h4>Services</h4>
            <ul>
                <li>Government & Online Services</li>
                <li>Business Services</li>
                <li>Printing, Branding & Stationery</li>
                <li>IT & Technology</li>
            </ul>
        </div>
        <div>
            <h4>Contact</h4>
            <ul class="ds-footer-contact">
                <li><i data-lucide="mail"></i><a href="mailto:hello@digitalstar.co.tz">hello@digitalstar.co.tz</a></li>
                <li><i data-lucide="map-pin"></i>Dar es Salaam, Tanzania</li>
                <li><i data-lucide="clock"></i>Mon - Sat, 8AM - 6PM</li>
            </ul>
        </div>
    </div>
    <div class="ds-footer-bottom">
        <p class="ds-container">&copy; {{ date('Y') }} Digital Star Consultants. All rights reserved.</p>
    </div>
</footer>
