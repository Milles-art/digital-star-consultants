@php
    $flashes = [
        'success' => ['tone' => 'success', 'label' => 'Success'],
        'status'  => ['tone' => 'success', 'label' => 'Status'],
        'info'    => ['tone' => 'info',    'label' => 'Information'],
        'warning' => ['tone' => 'warning', 'label' => 'Warning'],
        'error'   => ['tone' => 'error',   'label' => 'Error'],
        'danger'  => ['tone' => 'error',   'label' => 'Error'],
    ];

    $toneClasses = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
        'info'    => 'border-[color:var(--color-brand-200)] bg-[color:var(--color-brand-50)] text-[color:var(--color-brand-900)]',
        'warning' => 'border-[color:var(--color-accent-200)] bg-[color:var(--color-accent-50)] text-[color:var(--color-accent-700)]',
        'error'   => 'border-red-200 bg-red-50 text-red-900',
    ];

    $active = [];
    foreach ($flashes as $key => $meta) {
        if (session()->has($key)) {
            $active[] = ['tone' => $meta['tone'], 'label' => $meta['label'], 'message' => session($key)];
        }
    }
@endphp

@if (count($active) || $errors->any())
    <div class="shell pt-4">
        @foreach ($active as $item)
            <div class="mb-3 rounded-xl border px-4 py-3 text-sm {{ $toneClasses[$item['tone']] ?? $toneClasses['info'] }}" role="status">
                <span class="font-semibold">{{ $item['label'] }}:</span> {{ $item['message'] }}
            </div>
        @endforeach

        @if ($errors->any())
            <div class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">
                <p class="font-semibold">Please fix the following:</p>
                <ul class="mt-1 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
