@extends('layouts.app', [
    'title' => 'Home',
    'metaDescription' => 'Digital Star Consultants in Mbagala, Dar es Salaam — IT, printing, design, stationery and tech services. Submit a request online and track it with a reference number.',
])

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden border-b border-line bg-gradient-to-b from-sky/60 to-paper">
        <div class="shell grid items-center gap-12 py-16 lg:grid-cols-[1.15fr_0.85fr] lg:py-24">
            <div>
                <p class="eyebrow">Mbagala · Dar es Salaam</p>
                <h1 class="display mt-4 text-ink">
                    Practical digital help for real work.
                </h1>
                <p class="mt-6 max-w-xl text-lg text-muted">
                    Browse our services, submit a request with your documents, and track progress with a private reference number — no account required.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('public.services.index') }}" class="button-primary">Browse services</a>
                    <a href="#how-it-works" class="button-secondary">See how it works</a>
                </div>
                <dl class="mt-12 grid max-w-lg grid-cols-3 gap-4">
                    <div class="rounded-2xl border border-line bg-white/80 p-4 text-center shadow-sm">
                        <dt class="text-[11px] font-bold uppercase tracking-wider text-muted">Submit</dt>
                        <dd class="mt-1 text-sm font-black text-ink">Online</dd>
                    </div>
                    <div class="rounded-2xl border border-line bg-white/80 p-4 text-center shadow-sm">
                        <dt class="text-[11px] font-bold uppercase tracking-wider text-muted">Track</dt>
                        <dd class="mt-1 text-sm font-black text-ink">By reference</dd>
                    </div>
                    <div class="rounded-2xl border border-line bg-white/80 p-4 text-center shadow-sm">
                        <dt class="text-[11px] font-bold uppercase tracking-wider text-muted">Support</dt>
                        <dd class="mt-1 text-sm font-black text-ink">Local team</dd>
                    </div>
                </dl>
            </div>

            <div class="relative">
                <div class="rounded-[28px] border border-line bg-white p-6 shadow-xl shadow-ink/5">
                    <p class="eyebrow">Quick track</p>
                    <h2 class="mt-2 text-2xl font-black text-ink">Already applied?</h2>
                    <p class="mt-2 text-sm text-muted">Enter the reference number you received when you submitted.</p>
                    <form id="home-track-form" class="mt-6 space-y-3" action="#" method="get">
                        <label class="block text-xs font-bold uppercase tracking-wider text-muted" for="track-ref">Reference number</label>
                        <input
                            id="track-ref"
                            name="reference"
                            type="text"
                            required
                            placeholder="e.g. DSC-2026-XXXX"
                            class="w-full rounded-2xl border border-line bg-paper px-4 py-3.5 text-sm font-semibold text-ink outline-none transition focus:border-blue focus:ring-4 focus:ring-blue/10"
                        >
                        <button type="submit" class="button-primary w-full">Check status</button>
                    </form>
                    <div id="home-track-result" class="mt-4 hidden rounded-2xl border border-line bg-paper p-4 text-sm"></div>
                </div>
            </div>
        </div>
    </section>

    {{-- Categories / services preview --}}
    <section class="shell py-16 lg:py-20">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="eyebrow">What we offer</p>
                <h2 class="section-title mt-2 text-ink">Services built around your needs</h2>
            </div>
            <a href="{{ route('public.services.index') }}" class="button-secondary shrink-0">View all services</a>
        </div>

        @if ($categories->isEmpty())
            <div class="mt-10 rounded-3xl border border-dashed border-line bg-surface px-6 py-16 text-center">
                <p class="text-muted font-semibold">Services are being prepared. Please check back soon.</p>
            </div>
        @else
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    @php
                        $serviceCount = $category->services->count() + $category->children->sum(fn ($c) => $c->services->count());
                    @endphp
                    <a
                        href="{{ route('public.services.index', ['category' => $category->slug]) }}"
                        class="group rounded-3xl border border-line bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-blue/30 hover:shadow-lg hover:shadow-blue/5"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-sky text-lg font-black text-blue">
                                {{ strtoupper(mb_substr($category->name, 0, 1)) }}
                            </span>
                            <span class="rounded-full bg-paper px-3 py-1 text-[11px] font-bold uppercase tracking-wider text-muted">
                                {{ $serviceCount }} {{ Str::plural('service', $serviceCount) }}
                            </span>
                        </div>
                        <h3 class="mt-5 text-xl font-black text-ink group-hover:text-blue">{{ $category->name }}</h3>
                        @if ($category->description)
                            <p class="mt-2 line-clamp-3 text-sm text-muted">{{ $category->description }}</p>
                        @endif
                        <p class="mt-5 text-sm font-bold text-blue">Explore →</p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="border-y border-line bg-surface">
        <div class="shell py-16 lg:py-20">
            <p class="eyebrow">Simple process</p>
            <h2 class="section-title mt-2 text-ink">How it works</h2>
            <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($steps as $step)
                    <div class="rounded-3xl border border-line bg-white p-6 shadow-sm">
                        <span class="text-3xl font-black text-yellow">{{ $step['n'] }}</span>
                        <h3 class="mt-4 text-lg font-black text-ink">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm text-muted">{{ $step['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Contact --}}
    <section id="contact" class="shell py-16 lg:py-20">
        <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
            <div>
                <p class="eyebrow">Get in touch</p>
                <h2 class="section-title mt-2 text-ink">Send us a message</h2>
                <p class="mt-4 max-w-md text-muted">
                    Questions about a service, pricing, or documents? Leave a message and the team will respond.
                </p>
                <ul class="mt-8 space-y-3 text-sm text-muted">
                    <li class="flex items-center gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-sky font-bold text-blue">📍</span>
                        Mbagala, Dar es Salaam, Tanzania
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-sky font-bold text-blue">⏱</span>
                        Mon–Sat · typical business hours
                    </li>
                </ul>
            </div>

            <form
                id="contact-form"
                class="rounded-3xl border border-line bg-white p-6 shadow-sm sm:p-8"
                method="POST"
                action="{{ route('public.contact.store') }}"
            >
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="contact-name">Name</label>
                        <input id="contact-name" name="name" type="text" required maxlength="120"
                            class="w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm font-semibold text-ink outline-none focus:border-blue focus:ring-4 focus:ring-blue/10"
                            placeholder="Your full name">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="contact-email">Email</label>
                        <input id="contact-email" name="email" type="email" required maxlength="180"
                            class="w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm font-semibold text-ink outline-none focus:border-blue focus:ring-4 focus:ring-blue/10"
                            placeholder="you@example.com">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-muted" for="contact-message">Message</label>
                    <textarea id="contact-message" name="message" required rows="5" maxlength="2000"
                        class="w-full rounded-2xl border border-line bg-paper px-4 py-3 text-sm font-semibold text-ink outline-none focus:border-blue focus:ring-4 focus:ring-blue/10"
                        placeholder="How can we help?"></textarea>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <button type="submit" class="button-primary" data-contact-submit>Send message</button>
                    <p id="contact-feedback" class="text-sm font-semibold text-muted"></p>
                </div>
            </form>
        </div>
    </section>

    @push('scripts')
    <script>
        document.getElementById('home-track-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const ref = document.getElementById('track-ref').value.trim();
            const box = document.getElementById('home-track-result');
            if (!ref) return;
            box.classList.remove('hidden');
            box.innerHTML = '<p class="text-muted font-semibold">Checking…</p>';
            try {
                const res = await fetch(`/track/${encodeURIComponent(ref)}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const json = await res.json();
                if (!res.ok) {
                    box.innerHTML = `<p class="font-bold text-red-700">${json.message || 'Submission not found.'}</p>`;
                    return;
                }
                const d = json.data;
                box.innerHTML = `
                    <p class="font-black text-ink">${d.service_name || 'Service'}</p>
                    <p class="mt-1 text-muted">Status: <strong class="text-ink">${d.status_label || d.status}</strong></p>
                    <p class="mt-1 text-muted">Reference: <strong class="text-ink">${d.reference_number}</strong></p>
                    <p class="mt-1 text-muted">Submitted: ${d.created_at || '—'}</p>
                `;
            } catch {
                box.innerHTML = '<p class="font-bold text-red-700">Could not check status. Please try again.</p>';
            }
        });

        document.getElementById('contact-form')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const btn = form.querySelector('[data-contact-submit]');
            const feedback = document.getElementById('contact-feedback');
            const original = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Sending…';
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
                    feedback.textContent = json.message || Object.values(json.errors || {}).flat()[0] || 'Could not send message.';
                    feedback.className = 'text-sm font-semibold text-red-700';
                    return;
                }
                feedback.textContent = json.message || 'Message sent. We will get back to you soon.';
                feedback.className = 'text-sm font-semibold text-emerald-700';
                form.reset();
            } catch {
                feedback.textContent = 'Network error. Please try again.';
                feedback.className = 'text-sm font-semibold text-red-700';
            } finally {
                btn.disabled = false;
                btn.textContent = original;
            }
        });
    </script>
    @endpush
@endsection
