@extends('layouts.app', [
    'title' => 'Status: ' . $reference,
    'metaDescription' => 'Live tracking status for submission ' . $reference . ' with Digital Star Consultants.'
])

@section('content')
@php
    $locale = app()->getLocale();
    $isSw = $locale === 'sw';
@endphp

{{-- ========================================================================= --}}
{{-- TRACK STATUS DISPLAY                                                      --}}
{{-- ========================================================================= --}}
<section class="border-b border-line bg-gradient-to-b from-[#F2F6FB] via-[#F8FAFD] to-white py-14 lg:py-20">
    <div class="shell max-w-3xl">
        
        <div class="flex items-center gap-2 text-xs font-bold text-muted mb-5">
            <a href="{{ route('public.track.form') }}" class="hover:text-blue transition-colors">{{ __('site.nav.track') }}</a>
            <span>/</span>
            <span class="text-navy font-mono font-bold">{{ $reference }}</span>
        </div>

        @if (! $submission)
            {{-- Reference Not Found State --}}
            <div class="rounded-3xl border border-line bg-white p-8 sm:p-14 text-center shadow-xs">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-amber-50 text-amber-600 mx-auto mb-5 font-black border border-amber-200">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-ink">
                    {{ $isSw ? 'Namba ya Kumbukumbu Haikupatikana' : 'Reference Number Not Found' }}
                </h1>
                <p class="mt-2 text-xs sm:text-sm text-muted max-w-md mx-auto leading-relaxed">
                    {{ $isSw ? 'Hatukuweza kupata ombi linalolingana na namba hii: ' : 'No active service submission was found matching reference number: ' }}
                    <span class="font-bold text-navy font-mono">{{ $reference }}</span>
                </p>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a href="{{ route('public.track.form') }}" class="button-secondary !py-3 !px-6 !text-xs font-bold">
                        <span>{{ $isSw ? 'Jaribu Namba Nyingine' : 'Try Another Reference' }}</span>
                    </a>
                    <a href="https://wa.me/255783257716" class="wa-btn !py-3 !px-6 !text-xs font-bold" target="_blank" rel="noopener">
                        <span>WhatsApp Support</span>
                    </a>
                </div>
            </div>
        @else
            {{-- Valid Submission Live Status Card --}}
            <div class="rounded-3xl border border-line bg-white p-8 sm:p-12 shadow-sm space-y-8">
                
                {{-- Header & Live Status Badge --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-line pb-6">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-muted block mb-1">Reference Number</span>
                        <h1 class="text-2xl sm:text-3xl font-mono font-black text-navy">{{ $submission->reference_number }}</h1>
                    </div>

                    <div>
                        @php
                            $status = $submission->status;
                            $statusClasses = match($status) {
                                'completed' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                                'in_progress' => 'bg-blue-50 text-blue-800 border-blue-300',
                                'rejected' => 'bg-red-50 text-red-800 border-red-300',
                                default => 'bg-amber-50 text-amber-800 border-amber-300',
                            };
                        @endphp
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full border text-xs font-bold {{ $statusClasses }}">
                            <span class="h-2 w-2 rounded-full bg-current"></span>
                            <span>{{ $submission->status_label ?? ucfirst($status) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Submission Metadata Grid --}}
                <div class="grid gap-6 sm:grid-cols-2 text-xs">
                    <div>
                        <span class="font-bold uppercase tracking-wider text-muted block mb-1">Service Requested</span>
                        <span class="text-sm font-black text-ink">{{ $submission->service->name ?? 'General Request' }}</span>
                    </div>

                    <div>
                        <span class="font-bold uppercase tracking-wider text-muted block mb-1">Submission Timestamp</span>
                        <span class="text-sm font-bold text-ink">{{ $submission->created_at->format('M d, Y · H:i') }} EAT</span>
                    </div>

                    <div>
                        <span class="font-bold uppercase tracking-wider text-muted block mb-1">Client Name</span>
                        <span class="text-sm font-semibold text-ink">{{ $submission->customer_name }}</span>
                    </div>

                    <div>
                        <span class="font-bold uppercase tracking-wider text-muted block mb-1">Contact Phone</span>
                        <span class="text-sm font-semibold text-ink">{{ $submission->customer_phone }}</span>
                    </div>
                </div>

                {{-- Staff Status Notes --}}
                @if ($submission->staff_notes)
                    <div class="rounded-2xl bg-[#F8FAFD] border border-line p-6 text-xs space-y-2">
                        <span class="font-black uppercase tracking-wider text-navy block">Status Update & Engineering Notes:</span>
                        <p class="text-muted leading-relaxed">{{ $submission->staff_notes }}</p>
                    </div>
                @endif

                {{-- Action Support Footer --}}
                <div class="border-t border-line pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <a href="{{ route('public.track.form') }}" class="text-xs font-bold text-navy hover:text-blue transition-colors">
                        &larr; {{ $isSw ? 'Fuatilia Ombi Lingine' : 'Track Another Reference' }}
                    </a>

                    <a href="https://wa.me/255783257716?text={{ urlencode('Inquiry regarding reference ' . $submission->reference_number) }}" 
                       class="wa-btn !py-2.5 !px-5 !text-xs justify-center font-bold" target="_blank" rel="noopener">
                        <span>Inquire on WhatsApp</span>
                    </a>
                </div>

            </div>
        @endif

    </div>
</section>
@endsection
