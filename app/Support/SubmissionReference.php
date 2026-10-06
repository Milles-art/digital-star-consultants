<?php

namespace App\Support;

use App\Models\Setting;

final class SubmissionReference
{
    // Accept historic prefixes as well as the current setting.
    public const PATTERN = '[A-Za-z0-9]{1,12}-[0-9]{8}-[A-Za-z0-9]{6}';

    public static function prefix(): string
    {
        $prefix = strtoupper((string) Setting::get('operations.reference_prefix', 'DSC'));
        return substr(preg_replace('/[^A-Z0-9]+/', '', $prefix) ?: 'DSC', 0, 12);
    }

    public static function example(): string
    {
        return self::prefix().'-'.now()->format('Ymd').'-ABC123';
    }
}
