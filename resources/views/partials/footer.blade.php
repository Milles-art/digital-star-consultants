<footer class="bg-ink text-white">
    <div class="shell grid gap-12 py-16 md:grid-cols-[1.3fr_.85fr_.85fr_.85fr] md:py-24">
        <div>
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-yellow text-ink"><span class="h-4 w-4 rotate-45 border-2 border-ink"></span></span>
                <span class="text-sm font-bold">Digital Star Consultants</span>
            </a>
            <p class="mt-6 max-w-sm text-sm text-slate-300">Practical digital services for the moments that matter — delivered with clarity, care, and momentum across 12 countries.</p>
            <div class="mt-6 flex items-center gap-3">
                <span class="rounded-full bg-white/8 px-3 py-1.5 text-xs font-semibold text-slate-300">🌐 EN · FR · AR · ES</span>
            </div>
        </div>
        <div>
            <p class="eyebrow-dark">Services</p>
            <div class="mt-5 space-y-3 text-sm text-slate-300">
                <a class="block hover:text-white" href="{{ route('public.services.index') }}">All services</a>
                <a class="block hover:text-white" href="{{ route('public.services.index') }}">Government</a>
                <a class="block hover:text-white" href="{{ route('public.services.index') }}">Business</a>
                <a class="block hover:text-white" href="{{ route('public.services.index') }}">Digital</a>
                <a class="block hover:text-white" href="{{ route('public.services.index') }}">Consulting</a>
            </div>
        </div>
        <div>
            <p class="eyebrow-dark">Company</p>
            <div class="mt-5 space-y-3 text-sm text-slate-300">
                <a class="block hover:text-white" href="{{ route('home') }}#why">About us</a>
                <a class="block hover:text-white" href="{{ route('home') }}#industries">Industries</a>
                <a class="block hover:text-white" href="{{ url('/track') }}">Track a request</a>
                @if (Route::has('public.contact.show'))<a class="block hover:text-white" href="{{ route('public.contact.show') }}">Contact</a>@else<a class="block hover:text-white" href="{{ url('/contact') }}">Contact</a>@endif
            </div>
        </div>
        <div>
            <p class="eyebrow-dark">Get moving</p>
            <p class="mt-5 text-sm leading-7 text-slate-300">Tell us what you need. We'll help you find the clearest next step.</p>
            <a class="mt-5 inline-flex items-center gap-2 text-sm font-bold text-yellow hover:text-white" href="{{ route('public.services.index') }}">Browse services <span aria-hidden="true">↗</span></a>
            <div class="mt-6 space-y-1 text-xs text-slate-400">
                <p>✉ hello@digitalstar.consulting</p>
                <p>☎ +1 (800) 555-0142</p>
                <p>● Mon–Fri, 8:00–18:00</p>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="shell flex flex-col gap-3 py-6 text-xs text-slate-400 sm:flex-row sm:items-center sm:justify-between">
            <span>© {{ date('Y') }} Digital Star Consultants. All rights reserved.</span>
            <div class="flex items-center gap-5">
                <a class="hover:text-white" href="#">Privacy</a>
                <a class="hover:text-white" href="#">Terms</a>
                <span>Clear answers. Better outcomes.</span>
            </div>
        </div>
    </div>
</footer>
