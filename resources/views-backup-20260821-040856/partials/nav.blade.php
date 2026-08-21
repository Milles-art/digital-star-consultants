<nav class="bg-white/80 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-9 h-9 bg-gradient-to-br from-amber-400 to-amber-600 rounded-lg flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </div>
                <span class="font-display text-xl font-bold text-slate-900 tracking-tight">Digital Star</span>
            </a>
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors {{ request()->routeIs('home') ? 'text-slate-900 bg-slate-100' : '' }}">Home</a>
                <a href="{{ route('public.services.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors {{ request()->routeIs('public.services.*') ? 'text-slate-900 bg-slate-100' : '' }}">Services</a>
                <a href="{{ route('public.submissions.track', ['reference' => 'demo-ref']) }}" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Track</a>
            </div>
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @php $dashboardRoute = auth()->user()->isManagement() ? 'admin.dashboard' : 'staff.submissions'; @endphp
                    <a href="{{ route($dashboardRoute) }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">@csrf<button type="submit" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-500 hover:text-rose-600 transition-colors">Sign out</button></form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Sign in</a>
                    <a href="{{ route('public.services.index') }}" class="px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-sm hover:shadow">Start a request</a>
                @endauth
            </div>
            <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-100 bg-white">
        <div class="px-4 py-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Home</a>
            <a href="{{ route('public.services.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Services</a>
            <a href="{{ route('public.submissions.track', ['reference' => 'demo-ref']) }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Track request</a>
            @auth
                @php $dash = auth()->user()->isManagement() ? 'admin.dashboard' : 'staff.submissions'; @endphp
                <a href="{{ route($dash) }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50">Sign in</a>
            @endauth
        </div>
    </div>
</nav>
