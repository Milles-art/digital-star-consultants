@extends('layouts.public')

@section('title', 'Our Services')

@section('content')
    @php($categories = \App\Models\ServiceCategory::active()->get())
    <section class="bg-slate-950 px-4 py-16 text-white sm:px-6 lg:px-8"><div class="mx-auto max-w-7xl"><p class="font-semibold text-blue-300">Expert support for every step</p><h1 class="mt-3 text-4xl font-black">Our Services</h1><p class="mt-4 max-w-2xl text-slate-300">Choose a service and let our consultants help you move forward with clarity.</p></div></section>
    <section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex flex-wrap gap-2"><a href="{{ route('services.index') }}" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white">All</a>@foreach($categories as $category)<a href="{{ route('services.index', ['category' => $category->slug]) }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 hover:border-blue-600 hover:text-blue-600">{{ $category->name }}</a>@endforeach</div>
        @php($services = \App\Models\Service::active()->get())
        <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($services as $service)
                <article class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm"><div class="flex items-start justify-between gap-4"><div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><x-dynamic-icon name="sparkles" class="h-5 w-5" /></div>@if($service->is_featured)<span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">Featured</span>@endif</div><h2 class="mt-6 text-xl font-bold text-slate-900">{{ $service->name }}</h2><p class="mt-2 flex-1 text-sm leading-6 text-slate-600">{{ $service->short_description }}</p><div class="mt-6 space-y-2 border-t border-slate-100 pt-4 text-sm text-slate-500"><div class="flex justify-between"><span>Processing time</span><span class="font-medium text-slate-800">{{ $service->processing_time ?? 'Varies' }}</span></div><div class="flex justify-between"><span>Price</span><span class="font-bold text-slate-900">TZS {{ number_format($service->price) }}</span></div></div><a href="{{ route('services.show', $service->slug) }}" class="btn-primary mt-6 w-full px-4 py-3 text-center text-sm font-semibold">Apply Now</a></article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-12 text-center md:col-span-2 lg:col-span-3"><h2 class="font-bold text-slate-900">No services available</h2><p class="mt-2 text-sm text-slate-600">Please check back soon for our latest consulting services.</p></div>
            @endforelse
        </div>
    </section>
@endsection