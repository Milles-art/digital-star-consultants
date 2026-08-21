<footer class="bg-slate-900 text-slate-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 lg:gap-12">
            <div class="lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <span class="font-display text-lg font-bold text-white">Digital Star</span>
                </a>
                <p class="text-sm text-slate-400 leading-relaxed mb-4">Practical digital services for the moments that matter — delivered with clarity, care, and momentum across 12 countries.</p>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="px-2 py-1 rounded bg-slate-800">EN</span>
                    <span class="px-2 py-1 rounded bg-slate-800">FR</span>
                    <span class="px-2 py-1 rounded bg-slate-800">AR</span>
                    <span class="px-2 py-1 rounded bg-slate-800">ES</span>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Services</h3>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('public.services.index') }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">All services</a></li>
                    <li><a href="{{ route('public.services.index', ['category' => 'government']) }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">Government</a></li>
                    <li><a href="{{ route('public.services.index', ['category' => 'business']) }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">Business</a></li>
                    <li><a href="{{ route('public.services.index', ['category' => 'digital']) }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">Digital</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Company</h3>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('home') }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">About us</a></li>
                    <li><a href="{{ route('public.submissions.track', ['reference' => 'demo-ref']) }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">Track a request</a></li>
                    <li><a href="{{ route('public.services.index') }}" class="text-sm text-slate-400 hover:text-amber-400 transition-colors">Industries</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider mb-4">Get in touch</h3>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span class="text-sm text-slate-400">hello@digitalstar.consulting</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span class="text-sm text-slate-400">+1 (800) 555-0142</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm text-slate-400">Mon–Fri, 8:00–18:00</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-slate-800 mt-10 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-xs text-slate-500">&copy; {{ date('Y') }} Digital Star Consultants. All rights reserved.</p>
            <div class="flex items-center gap-6">
                <a href="#" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">Privacy</a>
                <a href="#" class="text-xs text-slate-500 hover:text-slate-300 transition-colors">Terms</a>
            </div>
        </div>
    </div>
</footer>
