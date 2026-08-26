@extends('layouts.app', ['title' => __('site.track.title')])

@section('content')
<section class="border-b border-line bg-gradient-to-b from-sky/50 to-paper">
    <div class="shell py-14">
        <p class="eyebrow reveal">{{ __('site.track.eyebrow') }}</p>
        <h1 class="section-title reveal mt-2 text-ink">{{ __('site.track.title') }}</h1>
        <p class="reveal mt-4 max-w-xl text-muted">{{ __('site.track.lead') }}</p>
    </div>
</section>

<section class="shell py-14">
    <div class="reveal mx-auto max-w-md rounded-3xl border border-line bg-white p-6 shadow-sm sm:p-8">
        <form id="track-form" method="GET" action="#">
            <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="reference">{{ __('site.track.label') }}</label>
            <input id="reference" name="reference" type="text" required
                value="{{ request('q') }}"
                placeholder="{{ __('site.track.placeholder') }}"
                class="w-full rounded-2xl border border-line bg-paper px-4 py-3.5 text-sm font-semibold outline-none focus:border-blue focus:ring-4 focus:ring-blue/10">
            <button type="submit" class="button-primary mt-4 w-full">{{ __('site.track.btn') }}</button>
        </form>
        <div id="track-result" class="mt-6 hidden"></div>
    </div>
</section>

@push('scripts')
<script>
document.getElementById('track-form')?.addEventListener('submit', (e) => {
    e.preventDefault();
    const ref = document.getElementById('reference').value.trim();
    if (!ref) return;
    window.location.href = `/track/status/${encodeURIComponent(ref)}`;
});
// Auto-redirect if ?q= came from home
(function () {
    const q = new URLSearchParams(window.location.search).get('q');
    if (q && q.trim()) {
        window.location.replace(`/track/status/${encodeURIComponent(q.trim())}`);
    }
})();
</script>
@endpush
@endsection
