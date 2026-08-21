@extends('layouts.public')

@section('title', 'Professional Consulting Made Simple')

@section('content')
    @php($featuredServices = \App\Models\Service::featured()->limit(6)->get())
    <section class="overflow-hidden bg-slate-950 text-white">
        <div class="mx-auto grid max-w-7xl gap-12 px-4 py-24 sm:px-6 lg:grid-cols-2 lg:items-center lg:px-8 lg:py-32">
            <div>
                <span class="mb-6 inline-flex rounded-full bg-blue-500/10 px-4 py-2 text-sm font-semibold text-blue-300 ring-1 ring-inset ring-blue-400/20">Your growth, our expertise</span>
                <h1 class="max-w-2xl text-4xl font-black tracking-tight sm:text-6xl">Professional Consulting Made Simple</h1>
                <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">Practical business consulting that helps you make confident decisions, complete applications, and move your goals forward.</p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <a href="{{ route('services.index') }}" class="btn-primary px-6 py-3 font-semibold">Browse Services</a>
                    <a href="{{ route('submissions.track') }}" class="btn-secondary border-slate-700 bg-transparent px-6 py-3 font-semibold text-white">Track Application</a>
                </div>
            </div>
            <div class="relative hidden lg:block">
                <div class="absolute -inset-10 rounded-full bg-blue-600/20 blur-3xl"></div>
                <div class="relative rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl">
                    <div class="mb-8 flex items-center justify-between"><span class="text-sm text-slate-400">Consulting dashboard</span><span class="h-3 w-3 rounded-full bg-emerald-400"></span></div>
                    <div class="space-y-4">
                        <div class="h-4 w-3/4 rounded bg-blue-500/70"></div><div class="h-4 w-1/2 rounded bg-white/10"></div>
                        <div class="grid grid-cols-3 gap-3 pt-5"><div class="h-24 rounded-xl bg-white/10"></div><div class="h-24 rounded-xl bg-blue-500/30"></div><div class="h-24 rounded-xl bg-white/10"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="font-semibold text-blue-600">What we offer</p><h2 class="mt-2 text-3xl font-bold text-slate-900">Featured services</h2></div><a href="{{ route('services.index') }}" class="font-semibold text-blue-600 hover:text-blue-700">View all services →</a></div>
        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($featuredServices as $service)
                <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                    <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><x-dynamic-icon name="briefcase" class="h-5 w-5" /></div>
                    <h3 class="text-lg font-bold text-slate-900">{{ $service->name }}</h3><p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $service->short_description }}</p>
                    <div class="mt-6 flex items-center justify-between border-t border-slate-100 pt-4"><span class="font-bold text-slate-900">TZS {{ number_format($service->price) }}</span><a href="{{ route('services.show', $service->slug) }}" class="text-sm font-semibold text-blue-600">Learn More →</a></div>
                </article>
            @empty
                <p class="text-slate-600 md:col-span-3">Our services are being updated. Please check back soon.</p>
            @endforelse
        </div>
    </section>

    <section class="bg-slate-50 py-20"><div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"><div class="text-center"><p class="font-semibold text-blue-600">Simple process</p><h2 class="mt-2 text-3xl font-bold text-slate-900">How it works</h2></div><div class="mt-12 grid gap-8 md:grid-cols-3">@foreach([['01','Choose Service','Find the consulting service that fits your needs.'],['02','Submit Details','Tell us about your goals and upload your documents.'],['03','Track Progress','Follow your application from submission to completion.']] as $step)<div class="text-center"><div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-600 font-bold text-white">{{ $step[0] }}</div><h3 class="mt-5 font-bold text-slate-900">{{ $step[1] }}</h3><p class="mt-2 text-sm leading-6 text-slate-600">{{ $step[2] }}</p></div>@endforeach</div></div></section>
    <section class="bg-blue-600 text-white"><div class="mx-auto flex max-w-7xl flex-col items-start justify-between gap-6 px-4 py-16 sm:px-6 md:flex-row md:items-center lg:px-8"><div><h2 class="text-3xl font-bold">Ready to Get Started?</h2><p class="mt-2 text-blue-100">Turn your next big idea into a clear, actionable plan.</p></div><a href="{{ route('services.index') }}" class="rounded-lg bg-white px-6 py-3 font-semibold text-blue-700 hover:bg-blue-50">Explore Services</a></div></section>
@endsection