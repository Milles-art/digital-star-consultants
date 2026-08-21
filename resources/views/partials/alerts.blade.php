@if(session('success') || session('status') || session('message'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 p-4 fade-in flex items-start gap-3">
            <svg class="w-5 h-5 text-emerald-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm text-emerald-800 font-medium">{{ session('success') ?? session('status') ?? session('message') }}</div>
            <button onclick="this.closest('.fade-in').remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    </div>
@endif
@if(session('error'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="rounded-xl bg-rose-50 border border-rose-200 p-4 fade-in flex items-start gap-3">
            <svg class="w-5 h-5 text-rose-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm text-rose-800 font-medium">{{ session('error') }}</div>
            <button onclick="this.closest('.fade-in').remove()" class="ml-auto text-rose-400 hover:text-rose-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
    </div>
@endif
@if($errors->any())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 fade-in">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span class="text-sm font-semibold text-amber-800">Please fix the following:</span>
            </div>
            <ul class="ml-7 space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-amber-700">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
