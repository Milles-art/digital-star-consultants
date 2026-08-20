@php
    $servicesUrl = route('public.services.index');
    $trackUrl    = url('/track');
    $contactUrl  = Route::has('public.contact.show') ? route('public.contact.show') : url('/contact');
@endphp

<footer class="mt-auto border-t border-white/10 bg-[color:var(--color-brand-950)] text-white/70">
    <div class="shell py-14 md:py-16">
        <div class="grid gap-10 md:grid-cols-12">

            <div class="md:col-span-5">
                <div class="flex items-center gap-2.5 text-white">
                    <span aria-hidden="true" class="flex h-9 w-9 items-center justify-center rounded-xl bg-[color:var(--color-accent-400)] text-[color:var(--color-brand-950)]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="currentColor" aria-hidden="true">
                            <path d="M12 2.5l2.6 6.1 6.6.55-5 4.32 1.5 6.43L12 16.5l-5.7 3.4 1.5-6.43-5-4.32 6.6-.55L12 2.5z"/>
                        </svg>
                    </span>
                    <span class="text-lg font-bold tracking-tight">Digital Star Consultants</span>
                </div>
                <p class="mt-4 max-w-sm text-sm leading-relaxed">
                    Technology consulting and streamlined government &amp; business service requests —
                    submitted online, tracked transparently, delivered on time.
                </p>
            </div>

            <nav class="md:col-span-3" aria-label="Footer navigation">
                <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-white">Quick links</h2>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a class="transition-colors hover:text-[color:var(--color-accent-300)]" href="{{ route('home') }}">Home</a></li>
                    <li><a class="transition-colors hover:text-[color:var(--color-accent-300)]" href="{{ $servicesUrl }}">Services</a></li>
                    <li><a class="transition-colors hover:text-[color:var(--color-accent-300)]" href="{{ $trackUrl }}">Track request</a></li>
                    <li><a class="transition-colors hover:text-[color:var(--color-accent-300)]" href="{{ $contactUrl }}">Contact</a></li>
                </ul>
            </nav>

            <div class="md:col-span-4">
                <h2 class="text-xs font-bold uppercase tracking-[0.14em] text-white">Get started</h2>
                <p class="mt-4 text-sm leading-relaxed">
                    No account needed. Pick a service, submit your details, and receive a reference number instantly.
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <a href="{{ $servicesUrl }}" class="btn btn-sm btn-accent">Browse services</a>
                    <a href="{{ $trackUrl }}" class="btn btn-sm btn-ghost border border-white/20 text-white hover:bg-white/10">Track request</a>
                </div>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-start justify-between gap-3 border-t border-white/10 pt-6 text-xs sm:flex-row sm:items-center">
            <p>&copy; {{ date('Y') }} Digital Star Consultants. All rights reserved.</p>
            @guest
                <a href="{{ route('login') }}" class="transition-colors hover:text-[color:var(--color-accent-300)]">Staff login</a>
            @endguest
        </div>
    </div>
</footer>
