<?php

namespace App\Support;

final class GovernmentRequirementEvidence
{
    private static ?array $register = null;

    public static function forService(string $slug): ?array
    {
        if (self::$register === null) {
            $path = resource_path('data/government-service-verification.json');
            self::$register = is_file($path) ? json_decode(file_get_contents($path), true, flags: JSON_THROW_ON_ERROR) : [];
        }
        // A later catalogue revision must not silently inherit today's audit claim.
        if ((self::$register['catalogue_version'] ?? null) !== GovernmentServiceCatalogue::VERSION) return null;
        $entry = self::$register['services'][$slug] ?? null;

        return $entry ? ['checked_on' => self::$register['checked_on'], ...$entry] : null;
    }
}
