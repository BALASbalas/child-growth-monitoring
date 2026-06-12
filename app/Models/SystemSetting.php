<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
    ];

    protected $casts = [
        'value' => 'string',
    ];

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return $default;
        }

        $value = $setting->value;

        return match ($setting->type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, mixed $value, ?string $type = null, ?string $group = null, ?string $label = null): bool
    {
        $data = ['value' => is_array($value) ? json_encode($value) : (string) $value];

        if ($type) {
            $data['type'] = $type;
        }
        if ($group) {
            $data['group'] = $group;
        }
        if ($label) {
            $data['label'] = $label;
        }

        return static::updateOrCreate(['key' => $key], $data) ? true : false;
    }
}