@extends('layouts.app', ['title' => __('site.nav.contact')])

@section('content')
<section class="border-b border-line bg-gradient-to-b from-sky/50 to-paper">
    <div class="shell py-14">
        <p class="eyebrow reveal">{{ __('site.contact.eyebrow') }}</p>
        <h1 class="section-title reveal mt-2 text-ink">{{ __('site.contact.title') }}</h1>
        <p class="reveal mt-4 max-w-xl text-muted">{{ __('site.contact.lead') }}</p>
    </div>
</section>

<section class="shell py-14">
    <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr]">
        <div class="reveal space-y-6">
            <div class="rounded-3xl border border-line bg-white p-6 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-blue">{{ __('site.contact.phones') }}</p>
                <ul class="mt-4 space-y-3">
                    <li>
                        <a href="tel:+255783257716" class="text-lg font-black text-ink hover:text-blue">0783 257 716</a>
                        <div class="mt-1 flex gap-2">
                            <a href="https://wa.me/255783257716" class="wa-btn" target="_blank" rel="noopener">WhatsApp</a>
                        </div>
                    </li>
                    <li class="border-t border-line pt-3">
                        <a href="tel:+255754931751" class="text-lg font-black text-ink hover:text-blue">0754 931 751</a>
                        <div class="mt-1 flex gap-2">
                            <a href="https://wa.me/255754931751" class="wa-btn" target="_blank" rel="noopener">WhatsApp</a>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="rounded-3xl border border-line bg-ink p-6 text-white">
                <p class="text-[11px] font-bold uppercase tracking-wider text-yellow">{{ __('site.contact.visit') }}</p>
                <p class="mt-3 font-semibold leading-relaxed">{{ __('site.contact.address') }}</p>
            </div>
        </div>

        <form id="contact-form" method="POST" action="{{ route('public.contact.store') }}"
              class="reveal rounded-3xl border border-line bg-white p-6 shadow-sm sm:p-8">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="name">{{ __('site.contact.name') }}</label>
                    <input id="name" name="name" type="text" required maxlength="255"
                        class="w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm font-semibold outline-none focus:border-blue focus:ring-4 focus:ring-blue/10">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="email">{{ __('site.contact.email') }}</label>
                    <input id="email" name="email" type="email" required maxlength="255"
                        class="w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm font-semibold outline-none focus:border-blue focus:ring-4 focus:ring-blue/10">
                </div>
            </div>
            <div class="mt-4">
                <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="message">{{ __('site.contact.message') }}</label>
                <textarea id="message" name="message" required rows="5" maxlength="5000"
                    placeholder="{{ __('site.contact.message_ph') }}"
                    class="w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm font-semibold outline-none focus:border-blue focus:ring-4 focus:ring-blue/10"></textarea>
            </div>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <button type="submit" class="button-primary" data-contact-submit>{{ __('site.contact.send') }}</button>
                <p id="contact-feedback" class="text-sm font-semibold text-muted"></p>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
document.getElementById('contact-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('[data-contact-submit]');
    const feedback = document.getElementById('contact-feedback');
    const original = btn.textContent;
    btn.disabled = true;
    btn.textContent = @json(__('site.contact.sending'));
    feedback.textContent = '';
    try {
        const res = await fetch(form.action, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: new FormData(form),
        });
        const json = await res.json().catch(() => ({}));
        if (!res.ok) {
            feedback.textContent = json.message || Object.values(json.errors || {}).flat()[0] || @json(__('site.common.error'));
            feedback.className = 'text-sm font-semibold text-red-700';
            return;
        }
        feedback.textContent = json.message || @json(__('site.contact.success'));
        feedback.className = 'text-sm font-semibold text-emerald-700';
        form.reset();
    } catch {
        feedback.textContent = @json(__('site.common.error'));
        feedback.className = 'text-sm font-semibold text-red-700';
    } finally {
        btn.disabled = false;
        btn.textContent = original;
    }
});
</script>
@endpush
@endsection
