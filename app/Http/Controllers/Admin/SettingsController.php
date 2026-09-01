<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private const DEFAULTS = [
        'company.name' => ['value' => 'Digital Star Consultants', 'group' => 'business'],
        'company.tagline' => ['value' => 'Digital services. Simplified.', 'group' => 'business'],
        'company.email' => ['value' => 'hello@digitalstar.co.tz', 'group' => 'business'],
        'company.phone' => ['value' => '+255 700 000 000', 'group' => 'business'],
        'company.whatsapp' => ['value' => '+255 700 000 000', 'group' => 'business'],
        'company.address' => ['value' => 'Dar es Salaam, Tanzania', 'group' => 'business'],
        'company.hours' => ['value' => 'Mon–Fri, 08:00–17:00', 'group' => 'business'],
        'operations.currency' => ['value' => 'TZS', 'group' => 'operations'],
        'operations.reference_prefix' => ['value' => 'DSC', 'group' => 'operations'],
        'operations.customer_uploads_enabled' => ['value' => true, 'group' => 'operations', 'type' => 'boolean'],
        'notifications.new_submission' => ['value' => true, 'group' => 'notifications', 'type' => 'boolean'],
        'notifications.status_change' => ['value' => true, 'group' => 'notifications', 'type' => 'boolean'],
        'notifications.assignment' => ['value' => true, 'group' => 'notifications', 'type' => 'boolean'],
        'public.show_phone' => ['value' => true, 'group' => 'public', 'type' => 'boolean'],
        'public.show_address' => ['value' => true, 'group' => 'public', 'type' => 'boolean'],
        'public.show_business_hours' => ['value' => true, 'group' => 'public', 'type' => 'boolean'],
    ];

    public function index(): View
    {
        $this->ensureDefaults();

        $settings = Setting::query()->get()->keyBy('key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $this->ensureDefaults();

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:150'],
            'company_tagline' => ['nullable', 'string', 'max:180'],
            'company_email' => ['nullable', 'email', 'max:150'],
            'company_phone' => ['nullable', 'string', 'max:40'],
            'company_whatsapp' => ['nullable', 'string', 'max:40'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'company_hours' => ['nullable', 'string', 'max:120'],
            'currency' => ['required', 'string', 'size:3'],
            'reference_prefix' => ['required', 'string', 'alpha_dash', 'max:12'],
            'customer_uploads_enabled' => ['nullable', 'boolean'],
            'notify_new_submission' => ['nullable', 'boolean'],
            'notify_status_change' => ['nullable', 'boolean'],
            'notify_assignment' => ['nullable', 'boolean'],
            'show_phone' => ['nullable', 'boolean'],
            'show_address' => ['nullable', 'boolean'],
            'show_business_hours' => ['nullable', 'boolean'],
        ]);

        Setting::put('company.name', $validated['company_name'], 'business');
        Setting::put('company.tagline', $validated['company_tagline'] ?? '', 'business');
        Setting::put('company.email', $validated['company_email'] ?? '', 'business');
        Setting::put('company.phone', $validated['company_phone'] ?? '', 'business');
        Setting::put('company.whatsapp', $validated['company_whatsapp'] ?? '', 'business');
        Setting::put('company.address', $validated['company_address'] ?? '', 'business');
        Setting::put('company.hours', $validated['company_hours'] ?? '', 'business');
        Setting::put('operations.currency', strtoupper($validated['currency']), 'operations');
        Setting::put('operations.reference_prefix', strtoupper($validated['reference_prefix']), 'operations');
        Setting::put('operations.customer_uploads_enabled', (bool) ($validated['customer_uploads_enabled'] ?? false), 'operations', 'boolean');
        Setting::put('notifications.new_submission', (bool) ($validated['notify_new_submission'] ?? false), 'notifications', 'boolean');
        Setting::put('notifications.status_change', (bool) ($validated['notify_status_change'] ?? false), 'notifications', 'boolean');
        Setting::put('notifications.assignment', (bool) ($validated['notify_assignment'] ?? false), 'notifications', 'boolean');
        Setting::put('public.show_phone', (bool) ($validated['show_phone'] ?? false), 'public', 'boolean');
        Setting::put('public.show_address', (bool) ($validated['show_address'] ?? false), 'public', 'boolean');
        Setting::put('public.show_business_hours', (bool) ($validated['show_business_hours'] ?? false), 'public', 'boolean');

        return back()->with('success', 'System settings saved successfully.');
    }

    private function ensureDefaults(): void
    {
        foreach (self::DEFAULTS as $key => $definition) {
            if (Setting::query()->where('key', $key)->exists()) {
                continue;
            }

            Setting::put(
                $key,
                $definition['value'],
                $definition['group'],
                $definition['type'] ?? null,
            );
        }
    }
}
