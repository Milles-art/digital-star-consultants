@extends('layouts.app', [
    'title' => __('site.nav.track'),
    'metaDescription' => 'Track your service request status in real time with your Digital Star Consultants reference number.'
])

@section('content')
@php
    $locale = app()->getLocale();
    $isSw = $locale === 'sw';
@endphp

{{-- ========================================================================= --}}
{{-- TRACK REFERENCE LOOKUP                                                    --}}
{{-- ========================================================================= --}}
<section class="border-b border-line bg-gradient-to-b from-[#F2F6FB] via-[#F8FAFD] to-white py-14 lg:py-20">
    <div class="shell max-w-2xl text-center">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-line shadow-xs mb-5">
            <span class="h-2 w-2 rounded-full bg-yellow"></span>
            <span class="text-[11px] font-black uppercase tracking-[0.18em] text-navy">{{ __('site.nav.track') }}</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-ink tracking-tight">
            {{ $isSw ? 'Fuatilia Ombi Lako' : 'Track Your Request Status' }}
        </h1>
        <p class="mt-3 text-sm text-muted leading-relaxed max-w-lg mx-auto">
            {{ $isSw ? 'Weka namba ya kumbukumbu (mf. DSC-2026-XXXXXX) uliyopewa wakati wa kuwasilisha ombi lako.' : 'Enter the unique reference code (e.g. DSC-2026-XXXXXX) generated when your service request was submitted.' }}
        </p>

        {{-- Lookup Form --}}
        <div class="mt-10 rounded-3xl border border-line bg-white p-8 sm:p-10 shadow-sm text-left">
            <form id="track-form" onsubmit="event.preventDefault(); const val = document.getElementById('ref-input').value.trim(); if(val) window.location.href = '{{ url('/track/status') }}/' + encodeURIComponent(val);" class="space-y-5">
                <div>
                    <label for="ref-input" class="block text-xs font-bold uppercase tracking-wider text-navy mb-2">
                        {{ $isSw ? 'Namba ya Kumbukumbu' : 'Reference Number' }}
                    </label>
                    <input type="text" id="ref-input" required placeholder="e.g. DSC-2026-XXXXXX" value="{{ request('q') }}"
                           class="w-full rounded-2xl border border-line bg-surface px-5 py-3.5 text-sm font-bold text-ink placeholder:text-muted/60 focus:border-blue focus:ring-2 focus:ring-blue/10 outline-none uppercase font-mono">
                </div>

                <button type="submit" class="button-primary !py-4 !px-8 !text-xs font-black w-full justify-center">
                    <span>{{ $isSw ? 'Kagua Hali ya Ombi' : 'Check Live Status' }}</span>
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </button>
            </form>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 text-left text-xs text-muted">
            <div class="rounded-2xl border border-line bg-[#F8FAFD] p-5">
                <span class="font-bold text-ink block mb-1">No Account Required</span>
                <p>Status lookups are immediate using only your unique reference number.</p>
            </div>
            <div class="rounded-2xl border border-line bg-[#F8FAFD] p-5">
                <span class="font-bold text-ink block mb-1">Need Direct Support?</span>
                <p>Contact our support engineers on WhatsApp at <a href="https://wa.me/255783257716" class="text-blue font-bold">0783 257 716</a>.</p>
            </div>
        </div>
    </div>
</section>
@endsection
