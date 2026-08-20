<footer class="mt-20 border-t border-ink-200 bg-ink-950 text-ink-300">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-3 lg:px-8">
        <div>
            <div class="font-display text-lg font-semibold text-white">
                Digital Star <span class="text-accent-400">Consultants</span>
            </div>
            <p class="mt-3 max-w-sm text-sm leading-relaxed text-ink-400">
                Professional support for digital, business, and government services — clear process, reliable delivery.
            </p>
        </div>

        <div>
            <h3 class="text-sm font-semibold uppercase tracking-wider text-white">Quick links</h3>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('public.services.index') }}" class="hover:text-accent-400">All services</a></li>
                <li><a href="{{ url('/contact') }}" class="hover:text-accent-400">Contact us</a></li>
                <li><a href="{{ url('/track') }}" class="hover:text-accent-400">Track a request</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-semibold uppercase tracking-wider text-white">Staff</h3>
            <ul class="mt-3 space-y-2 text-sm">
                <li><a href="{{ route('login') }}" class="hover:text-accent-400">Staff login</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-4 text-xs text-ink-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <span>&copy; {{ date('Y') }} Digital Star Consultants. All rights reserved.</span>
            <span>Built for reliable service delivery.</span>
        </div>
    </div>
</footer>
