<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::query()->where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return $setting->castValue();
    }

    public static function put(string $key, mixed $value, string $group = 'general', ?string $type = null): static
    {
        $resolvedType = $type ?? match (true) {
            is_bool($value) => 'boolean',
            is_int($value) => 'integer',
            is_float($value) => 'float',
            is_array($value) || is_object($value) => 'json',
            default => 'string',
        };

        $storedValue = match ($resolvedType) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            default => (string) $value,
        };

        return static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue, 'type' => $resolvedType, 'group' => $group],
        );
    }

    public function castValue(): mixed
    {
        return match ($this->type) {
            'boolean' => in_array(strtolower((string) $this->value), ['1', 'true', 'yes', 'on'], true),
            'integer' => (int) $this->value,
            'float' => (float) $this->value,
            'json' => json_decode((string) $this->value, true) ?? [],
            default => $this->value,
        };
    }
}
