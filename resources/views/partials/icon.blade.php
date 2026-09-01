@php
    $paths = [
        'government' => 'M4 10h16M5 10v9h14v-9M8 10V7l4-3 4 3v3M8 19v-4h8v4',
        'passport' => 'M6 3h10l2 2v16H6zM9 8h6M9 12h6M9 16h4',
        'jobs' => 'M5 7h14v12H5zM9 7V5h6v2M5 11h14',
        'education' => 'M3 8l9-5 9 5-9 5zM7 11v4l5 3 5-3v-4M20 9v6',
        'tax' => 'M6 3h9l3 3v15H6zM9 10h6M9 14h6M9 18h4',
        'travel' => 'M3 12l18-7-7 18-3-8zM11 15l6-6',
        'forms' => 'M6 3h12v18H6zM9 8h6M9 12h6M9 16h4',
        'business' => 'M4 21V6l8-3 8 3v15M8 21v-8h8v8M8 8h2M14 8h2',
        'printing' => 'M6 8V3h12v5M6 17H4v-7h16v7h-2M7 14h10v7H7z',
        'branding' => 'M5 19l3-8 7-7 4 4-7 7-8 4zM13 7l4 4',
        'stationery' => 'M6 3h12v18H6zM9 8h6M9 12h6M9 16h4',
        'website' => 'M4 5h16v14H4zM4 9h16M8 7h.01M11 7h.01M14 7h.01',
        'mobile' => 'M7 2h10v20H7zM10 5h4M11 19h2',
        'it' => 'M5 5h14v10H5zM3 19h18M9 15v4M15 15v4',
        'support' => 'M4 12a8 8 0 0116 0v5a2 2 0 01-2 2h-2v-6h4M4 13v4a2 2 0 002 2h2v-6H4',
        'security' => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6zM9 12l2 2 4-4',
        'default' => 'M12 3l2.2 6.7H21l-5.5 4 2.1 6.8-5.6-4-5.6 4 2.1-6.8-5.5-4h6.8z',
    ];
    $iconKey = $iconKey ?? 'default';
    $path = $paths[$iconKey] ?? $paths['default'];
@endphp
<svg class="ds-svg-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="{{ $path }}" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
