@if (session('success'))
    <div class="shell pt-6"><div role="status" class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-900"><span class="mt-0.5 font-bold">✓</span><p>{{ session('success') }}</p></div></div>
@endif
@if (session('error'))
    <div class="shell pt-6"><div role="alert" class="flex items-start gap-3 rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-900"><span class="mt-0.5 font-bold">!</span><p>{{ session('error') }}</p></div></div>
@endif
@if ($errors->any())
    <div class="shell pt-6"><div role="alert" class="rounded-2xl border border-red-200 bg-red-50 px-4 py-4 text-sm text-red-900"><p class="font-bold">Please check the highlighted fields.</p></div></div>
@endif
