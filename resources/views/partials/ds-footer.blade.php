<footer class="ds-footer">
    <div class="ds-container ds-footer-grid">
        <div class="ds-footer-about">
            @include('partials.ds-logo', ['light' => true])
            <p>Your trusted digital partner in Tanzania. Web, software, creative and IT solutions that help you grow.</p>
        </div>
        <div>
            <h4>Pages</h4>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ url('/about') }}">About</a></li>
                <li><a href="{{ route('work') }}">Work</a></li>
                <li><a href="{{ route('public.services.index') }}">Services</a></li>
                <li><a href="{{ route('public.track.form') }}">Track an application</a></li>
                <li><a href="{{ route('public.contact.show') }}">Contact</a></li>
            </ul>
        </div>
        <div>
            <h4>Services</h4>
            <ul>
                <li>Web Development</li>
                <li>Software Development</li>
                <li>Digital Marketing</li>
                <li>IT Consultancy</li>
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
