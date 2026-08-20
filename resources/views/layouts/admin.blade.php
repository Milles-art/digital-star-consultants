<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', 'Workspace | Digital Star Consultants')</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	@stack('head')
</head>
<body class="min-h-screen bg-mist-100 font-sans text-ink-900 antialiased">
	<div class="min-h-screen lg:flex" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
		<div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-ink-950/50 lg:hidden"></div>

		<aside class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-ink-900 text-white transition-transform duration-200 lg:translate-x-0" :class="sidebarOpen && 'translate-x-0'">
			<div class="flex items-center justify-between border-b border-white/10 px-6 py-6">
				<a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
					<span class="grid h-10 w-10 place-items-center rounded-xl bg-accent-400 font-display text-lg font-bold text-ink-900">DS</span>
					<span class="leading-none"><strong class="block font-display text-base">Digital Star</strong><small class="mt-1 block text-[10px] font-bold uppercase tracking-[.18em] text-brand-300">Operations</small></span>
				</a>
				<button type="button" @click="sidebarOpen = false" class="grid h-9 w-9 place-items-center rounded-lg text-white/60 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Close navigation">&times;</button>
			</div>

			<nav class="flex-1 space-y-7 overflow-y-auto px-4 py-6" aria-label="Workspace navigation">
				<div>
					<p class="px-3 text-[10px] font-bold uppercase tracking-[.2em] text-white/35">Workspace</p>
					<div class="mt-3 space-y-1">
						@foreach(auth()->user()->isManagement() ? [['Dashboard','admin.dashboard','⌂'],['Submissions','admin.submissions.index','↗'],['Services','admin.services.index','✦'],['Categories','admin.categories.index','◈']] : [['Dashboard','staff.dashboard','⌂'],['My submissions','staff.submissions','↗']] as [$label, $route, $icon])
							<a href="{{ route($route) }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs($route) ? 'bg-brand-500 text-white shadow-lg shadow-brand-950/30' : 'text-white/65 hover:bg-white/10 hover:text-white' }}">
								<span class="grid h-7 w-7 place-items-center rounded-lg bg-white/10 text-xs">{{ $icon }}</span>{{ $label }}
							</a>
						@endforeach
					</div>
				</div>
				@if(auth()->user()->isManagement())
				<div>
					<p class="px-3 text-[10px] font-bold uppercase tracking-[.2em] text-white/35">Insights &amp; team</p>
					<div class="mt-3 space-y-1">
						@foreach([['Users','admin.users.index','◎'],['Reports','admin.reports.overview','▥']] as [$label, $route, $icon])
							<a href="{{ route($route) }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition {{ request()->routeIs($route) ? 'bg-brand-500 text-white shadow-lg shadow-brand-950/30' : 'text-white/65 hover:bg-white/10 hover:text-white' }}">
								<span class="grid h-7 w-7 place-items-center rounded-lg bg-white/10 text-xs">{{ $icon }}</span>{{ $label }}
							</a>
						@endforeach
					</div>
				</div>
				@endif
			</nav>

			<div class="border-t border-white/10 p-4">
				<a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold text-accent-400 hover:bg-white/10"><span class="grid h-7 w-7 place-items-center rounded-lg bg-accent-400/10">←</span>View public site</a>
			</div>
		</aside>

		<div class="flex min-h-screen flex-1 flex-col lg:ml-72">
			<header class="sticky top-0 z-20 flex items-center justify-between border-b border-mist-200 bg-white/90 px-5 py-4 backdrop-blur sm:px-8">
				<div class="flex items-center gap-4">
					<button type="button" @click="sidebarOpen = true" class="grid h-10 w-10 place-items-center rounded-xl border border-mist-200 text-ink-700 hover:bg-mist-100 lg:hidden" aria-label="Open navigation">☰</button>
					<div><p class="text-[10px] font-bold uppercase tracking-[.2em] text-brand-600">Operations center</p><h1 class="mt-1 font-display text-xl font-bold text-ink-900">@yield('heading', 'Dashboard')</h1></div>
				</div>
				@auth
					<div class="flex items-center gap-3">
						<div class="hidden text-right sm:block"><p class="text-sm font-bold text-ink-900">{{ auth()->user()->name }}</p><p class="text-xs capitalize text-ink-500">{{ auth()->user()->role }}</p></div>
						<span class="grid h-10 w-10 place-items-center rounded-full bg-brand-100 font-display font-bold text-brand-700">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
						<form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="hidden text-sm font-semibold text-ink-500 hover:text-red-600 sm:block">Log out</button></form>
					</div>
				@endauth
			</header>

			<main class="w-full flex-1 px-5 py-7 sm:px-8 sm:py-10">
				<div class="mx-auto max-w-7xl">
					@include('partials.alerts')
					@yield('content')
				</div>
			</main>
			<footer class="border-t border-mist-200 bg-white px-5 py-5 text-xs text-ink-400 sm:px-8"><div class="mx-auto flex max-w-7xl justify-between gap-4"><span>Digital Star Consultants</span><span>Operations workspace</span></div></footer>
		</div>
	</div>
	<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
	@stack('scripts')
</body>
</html>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta name="csrf-token" content="{{ csrf_token() }}"><title>@yield('title', 'Admin | Digital Star Consultants')</title><script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif'],display:['Plus Jakarta Sans','system-ui','sans-serif']},colors:{brand:{600:'#1d4ed8',700:'#173eae'},accent:{400:'#facc15'},ink:{900:'#0b1220'},mist:{50:'#f8fafd',100:'#f3f6fc',200:'#e6ecf7'}}}}}</script></head><body class="min-h-screen bg-mist-50 font-sans text-ink-900"><div class="min-h-screen lg:flex"><aside class="w-full bg-ink-900 text-white lg:fixed lg:inset-y-0 lg:w-64"><div class="flex items-center justify-between px-5 py-5"><a href="{{ route('admin.dashboard') }}" class="font-display text-lg font-extrabold">Digital Star <span class="text-accent-400">/ Admin</span></a></div><nav class="flex gap-1 overflow-x-auto px-3 pb-4 lg:block lg:space-y-1 lg:overflow-visible">@foreach([['Dashboard','admin.dashboard'],['Submissions','admin.submissions.index'],['Services','admin.services.index'],['Categories','admin.categories.index'],['Users','admin.users.index'],['Reports','admin.reports.overview']] as [$label,$route])<a href="{{ route($route) }}" class="block whitespace-nowrap rounded-lg px-3 py-2.5 text-sm font-semibold text-slate-300 hover:bg-white/10 hover:text-white">{{ $label }}</a>@endforeach<a href="{{ route('home') }}" class="mt-2 block whitespace-nowrap rounded-lg px-3 py-2.5 text-sm font-semibold text-accent-400 hover:bg-white/10">View public site</a></nav></aside><div class="flex-1 lg:ml-64"><header class="flex items-center justify-between border-b border-mist-200 bg-white px-5 py-4 sm:px-8"><div><p class="text-xs font-bold uppercase tracking-[.18em] text-brand-600">Operations</p><h1 class="mt-1 font-display text-xl font-extrabold">@yield('heading', 'Dashboard')</h1></div><div class="flex items-center gap-4 text-sm"><span class="hidden text-slate-500 sm:inline">{{ auth()->user()->name }}</span><form method="POST" action="{{ route('logout') }}">@csrf<button class="font-semibold text-red-600" type="submit">Log out</button></form></div></header><main class="p-5 sm:p-8">@include('partials.alerts')@yield('content')</main></div></div></body></html>
