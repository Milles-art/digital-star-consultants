@extends('layouts.app', [
    'title' => __('site.nav.contact'),
    'metaDescription' => 'Contact Digital Star Consultants in Dar es Salaam, Tanzania for custom software development, digital systems, and technology consulting.'
])

@section('content')
@php
    $locale = app()->getLocale();
    $isSw = $locale === 'sw';
@endphp

{{-- ========================================================================= --}}
{{-- CONTACT HERO                                                              --}}
{{-- ========================================================================= --}}
<section class="border-b border-line bg-gradient-to-b from-[#F2F6FB] via-[#F8FAFD] to-white py-14 lg:py-20">
    <div class="shell">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-line shadow-xs mb-5">
                <span class="h-2 w-2 rounded-full bg-yellow"></span>
                <span class="text-[11px] font-black uppercase tracking-[0.18em] text-navy">{{ __('site.nav.contact') }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-ink tracking-tight">
                {{ $isSw ? 'Wasiliana na Wahandisi Wetu' : 'Get in Touch with Our Team' }}
            </h1>
            <p class="mt-4 text-sm sm:text-base text-muted leading-relaxed">
                {{ $isSw ? 'Una mradi wa programu, mfumo wa wavuti, au unahitaji ushauri wa kidijitali? Tutumie ujumbe, piga simu, au fika ofisini kwetu Mbagala, Dar es Salaam.' : 'Discuss your software engineering goals, digital systems architecture, or operational consulting with our team in Dar es Salaam.' }}
            </p>
        </div>
    </div>
</section>

{{-- ========================================================================= --}}
{{-- CONTACT FORM & DIRECT CHANNELS                                            --}}
{{-- ========================================================================= --}}
<section class="py-16 sm:py-20 bg-canvas">
    <div class="shell">
        <div class="grid gap-12 lg:grid-cols-[1.3fr_0.7fr] lg:items-start">
            
            {{-- Inbound Project Inquiry Form --}}
            <div class="rounded-3xl border border-line bg-white p-8 sm:p-12 shadow-xs">
                
                @if (session('success'))
                    <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-6 mb-6 text-emerald-900 text-xs shadow-xs">
                        <span class="font-bold block mb-1 text-sm">{{ $isSw ? 'Ujumbe Umetumwa!' : 'Message Sent Successfully!' }}</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (isset($errors) && $errors->any())
                    <div class="rounded-2xl bg-red-50 border border-red-200 p-6 mb-6 text-red-900 text-xs shadow-xs">
                        <span class="font-bold block mb-1">Please fix the following issues:</span>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h2 class="text-2xl font-black text-ink mb-1">
                    {{ $isSw ? 'Tuma Ujumbe au Ombi la Mradi' : 'Send an Inquiry or Project Scope' }}
                </h2>
                <p class="text-xs text-muted mb-8">
                    {{ $isSw ? 'Timu yetu ya wahandisi itakujibu ndani ya masaa 24 ya kazi.' : 'Our engineering team will review your specifications and get back to you promptly.' }}
                </p>

                <form action="{{ route('public.contact.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-navy mb-2">
                                {{ $isSw ? 'Jina Lako' : 'Your Name' }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">
                        </div>

                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-navy mb-2">
                                {{ $isSw ? 'Barua Pepe' : 'Email Address' }} <span class="text-red-500">*</span>
                            </label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="name@company.com"
                                   class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-navy mb-2">
                                {{ $isSw ? 'Namba ya Simu' : 'Phone Number' }}
                            </label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="07XX XXX XXX"
                                   class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">
                        </div>

                        <div>
                            <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-navy mb-2">
                                {{ $isSw ? 'Mada / Kichwa cha Habari' : 'Subject' }}
                            </label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" placeholder="e.g. Custom Web Application Architecture"
                                   class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="message" class="block text-xs font-bold uppercase tracking-wider text-navy mb-2">
                            {{ $isSw ? 'Ujumbe au Maelezo ya Mradi' : 'Project Requirements / Message' }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" name="message" rows="5" required placeholder="{{ $isSw ? 'Eleza malengo yako au maswali uliyo nayo...' : 'Describe what you are looking to engineer or solve...' }}"
                                  class="w-full rounded-2xl border border-line bg-surface px-4 py-3 text-xs font-semibold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none">{{ old('message') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="button-primary !py-4 !px-8 !text-xs font-black justify-center w-full sm:w-auto">
                            <span>{{ $isSw ? 'Tuma Ujumbe' : 'Send Message' }}</span>
                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Direct Advisory Sidebar --}}
            <div class="space-y-6">
                
                {{-- Office Location Card --}}
                <div class="rounded-3xl border border-line bg-white p-7 shadow-xs space-y-3">
                    <span class="text-xs font-black uppercase tracking-wider text-yellow block">Studio Location</span>
                    <h3 class="text-lg font-black text-navy">Mbagala · Dar es Salaam</h3>
                    <p class="text-xs text-muted leading-relaxed">
                        Near Puma Petrol Station<br>
                        Dar es Salaam, Tanzania
                    </p>
                    <p class="text-[11px] text-muted pt-3 border-t border-line">
                        Operating Hours: Monday – Saturday (08:00 – 18:00 EAT)
                    </p>
                </div>

                {{-- Direct Lines Card --}}
                <div class="rounded-3xl border border-line bg-white p-7 shadow-xs space-y-4">
                    <span class="text-xs font-black uppercase tracking-wider text-blue block">Direct Engineering Lines</span>
                    
                    <div class="space-y-2.5 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-muted">Direct Line 1:</span>
                            <a href="tel:+255783257716" class="font-bold text-navy hover:text-blue">0783 257 716</a>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-muted">Direct Line 2:</span>
                            <a href="tel:+255754931751" class="font-bold text-navy hover:text-blue">0754 931 751</a>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-line">
                        <a href="https://wa.me/255783257716" class="wa-btn !py-3 !px-5 !text-xs w-full justify-center text-center font-bold" target="_blank" rel="noopener">
                            <span>Chat on WhatsApp</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>
@endsection
