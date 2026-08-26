@extends('layouts.app', ['title' => __('site.track.title')])

@section('content')
<section class="border-b border-line bg-gradient-to-b from-sky/50 to-paper">
    <div class="shell py-14">
        <p class="eyebrow">{{ __('site.track.eyebrow') }}</p>
        <h1 class="section-title mt-2 text-ink">{{ __('site.track.title') }}</h1>
    </div>
</section>

<section class="shell py-14">
    <div class="mx-auto max-w-lg">
        @if (!$submission)
            <div class="rounded-3xl border border-red-200 bg-red-50 p-6 text-center">
                <p class="font-bold text-red-800">{{ __('site.track.not_found') }}</p>
                <p class="mt-2 text-sm text-red-700">{{ $reference }}</p>
                <a href="{{ route('public.track.form') }}" class="button-secondary mt-6 inline-flex">{{ __('site.common.back') }}</a>
            </div>
        @else
            <div class="rounded-3xl border border-line bg-white p-6 shadow-sm sm:p-8">
                <p class="text-[11px] font-bold uppercase tracking-wider text-muted">{{ __('site.track.reference') }}</p>
                <p class="mt-1 text-2xl font-black tracking-wide text-ink">{{ $submission->reference_number }}</p>

                <dl class="mt-8 space-y-4 text-sm">
                    <div class="flex justify-between gap-4 border-b border-line pb-3">
                        <dt class="font-bold text-muted">{{ __('site.track.status') }}</dt>
                        <dd class="font-black text-ink">{{ $submission->status_label ?? \App\Models\Submission::statusLabel($submission->status) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-line pb-3">
                        <dt class="font-bold text-muted">{{ __('site.track.service') }}</dt>
                        <dd class="font-black text-ink text-right">{{ $submission->service->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4 border-b border-line pb-3">
                        <dt class="font-bold text-muted">{{ __('site.track.submitted') }}</dt>
                        <dd class="font-black text-ink">{{ $submission->created_at?->format('Y-m-d H:i') }}</dd>
                    </div>
                    @if ($submission->completed_at)
                        <div class="flex justify-between gap-4">
                            <dt class="font-bold text-muted">{{ __('site.track.completed') }}</dt>
                            <dd class="font-black text-ink">{{ $submission->completed_at->format('Y-m-d H:i') }}</dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-8 flex flex-wrap gap-2">
                    <a href="{{ route('public.track.form') }}" class="button-secondary">{{ __('site.track.btn') }}</a>
                    <a href="https://wa.me/255783257716?text={{ urlencode('Ref: '.$submission->reference_number) }}" class="wa-btn" target="_blank" rel="noopener">WhatsApp</a>
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
