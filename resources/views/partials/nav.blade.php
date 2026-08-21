<nav x-data="{ open: false }" class="border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-3" aria-label="Digital Star Consultants home">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-sm font-extrabold tracking-tight text-white shadow-lg shadow-blue-600/20">DSC</span>
            <span class="hidden text-sm font-bold tracking-wide text-slate-900 sm:block">Digital Star Consultants</span>
        </a>

        <div class="hidden items-center gap-8 md:flex">
            <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 transition hover:text-blue-600">Home</a>
            <a href="{{ route('services.index') }}" class="text-sm font-medium text-slate-600 transition hover:text-blue-600">Services</a>
            <a href="{{ route('submissions.track') }}" class="text-sm font-medium text-slate-600 transition hover:text-blue-600">Track</a>
            <a href="{{ route('contact') }}" class="text-sm font-medium text-slate-600 transition hover:text-blue-600">Contact</a>
        </div>

        <div class="hidden items-center gap-3 md:flex">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary inline-flex items-center px-4 py-2 text-sm font-semibold">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-primary inline-flex items-center px-4 py-2 text-sm font-semibold">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-secondary inline-flex items-center px-4 py-2 text-sm font-semibold">Login</a>
                <a href="{{ route('register') }}" class="btn-primary inline-flex items-center px-4 py-2 text-sm font-semibold">Get Started</a>
            @endauth
        </div>

        <button type="button" @click="open = !open" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 md:hidden" :aria-expanded="open.toString()" aria-label="Toggle navigation">
            <x-dynamic-icon name="menu" class="h-6 w-6" x-show="!open" />
            <x-dynamic-icon name="x" class="h-6 w-6" x-show="open" />
        </button>
    </div>

    <div x-cloak x-show="open" x-transition class="border-t border-slate-100 px-4 pb-5 pt-3 md:hidden">
        <div class="space-y-1">
            <a @click="open = false" href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Home</a>
            <a @click="open = false" href="{{ route('services.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Services</a>
            <a @click="open = false" href="{{ route('submissions.track') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Track</a>
            <a @click="open = false" href="{{ route('contact') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Contact</a>
        </div>
        <div class="mt-4 flex gap-3">
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn-secondary flex-1 px-4 py-2 text-center text-sm font-semibold">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="btn-primary w-full px-4 py-2 text-sm font-semibold">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-secondary flex-1 px-4 py-2 text-center text-sm font-semibold">Login</a>
                <a href="{{ route('register') }}" class="btn-primary flex-1 px-4 py-2 text-center text-sm font-semibold">Get Started</a>
            @endauth
        </div>
    </div>
</nav>