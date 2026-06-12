<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class DeviceConnection extends Model
{
    protected $fillable = [
        'user_id',
        'device_name',
        'device_type',
        'serial_number',
        'manufacturer',
        'model',
        'connection_type',
        'com_port',
        'baud_rate',
        'data_bits',
        'parity',
        'stop_bits',
        'data_format',
        'calibration_data',
        'is_active',
        'last_connected_at',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'baud_rate' => 'integer',
        'data_bits' => 'integer',
        'stop_bits' => 'integer',
        'last_connected_at' => 'datetime',
        'calibration_data' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function growthMeasurements(): HasMany
    {
        return $this->hasMany(GrowthMeasurement::class, 'device_id', 'serial_number');
    }

    // Accessors
    public function getDeviceTypeLabelAttribute(): string
    {
        $labels = [
            'weight_scale' => 'Digital Weight Scale',
            'height_rod' => 'Digital Height Rod',
            'muac_tape' => 'MUAC Tape',
            'infantometer' => 'Infantometer',
            'multi_function' => 'Multi-function Device',
        ];
        
        return $labels[$this->device_type] ?? ucfirst(str_replace('_', ' ', $this->device_type));
    }

    public function getConnectionStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }
        
        if (!$this->last_connected_at) {
            return 'Never Connected';
        }
        
        $lastConnected = Carbon::parse($this->last_connected_at);
        $minutesAgo = $lastConnected->diffInMinutes();
        
        if ($minutesAgo < 5) {
            return 'Connected';
        } elseif ($minutesAgo < 60) {
            return "Disconnected ({$minutesAgo} min ago)";
        } else {
            $hoursAgo = $lastConnected->diffInHours();
            return "Disconnected ({$hoursAgo} hours ago)";
        }
    }

    public function getSerialConfigAttribute(): string
    {
        return "{$this->parity},{$this->data_bits},{$this->stop_bits}";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeByType($query, string $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    public function scopeWeightScales($query)
    {
        return $query->where('device_type', 'weight_scale');
    }

    public function scopeHeightRods($query)
    {
        return $query->where('device_type', 'height_rod');
    }

    public function scopeRecentlyConnected($query, int $minutes = 60)
    {
        return $query->where('last_connected_at', '>=', Carbon::now()->subMinutes($minutes));
    }

    // Methods
    public function markAsConnected(): void
    {
        $this->update([
            'last_connected_at' => Carbon::now(),
        ]);
    }

    public function getCalibrationOffset(): float
    {
        if (!$this->calibration_data || !isset($this->calibration_data['offset'])) {
            return 0.0;
        }
        
        return floatval($this->calibration_data['offset']);
    }

    public function getCalibrationFactor(): float
    {
        if (!$this->calibration_data || !isset($this->calibration_data['factor'])) {
            return 1.0;
        }
        
        return floatval($this->calibration_data['factor']);
    }

    /**
     * Apply calibration to raw measurement
     */
    public function applyCalibration(float $rawValue): float
    {
        $offset = $this->getCalibrationOffset();
        $factor = $this->getCalibrationFactor();
        
        return ($rawValue + $offset) * $factor;
    }

    /**
     * Parse data from device based on data format
     */
    public function parseDeviceData(string $rawData): ?array
    {
        // Common patterns for digital scales
        $patterns = [
            // Pattern: "12.345 kg" or "12.345kg"
            '/(\d+\.?\d*)\s*kg/i' => ['value' => 1, 'unit' => 'kg'],
            // Pattern: "123.4 cm" or "123.4cm"
            '/(\d+\.?\d*)\s*cm/i' => ['value' => 1, 'unit' => 'cm'],
            // Pattern: "ST,12.345" (some scales use comma separator)
            '/ST,(\d+\.?\d*)/' => ['value' => 1, 'unit' => 'kg'],
            // Pattern: "12.345" (just a number)
            '/^(\d+\.?\d*)$/' => ['value' => 1, 'unit' => 'unknown'],
        ];

        foreach ($patterns as $pattern => $groups) {
            if (preg_match($pattern, $rawData, $matches)) {
                $value = floatval($matches[$groups['value']]);
                $unit = $groups['unit'];
                
                // Apply calibration
                $calibratedValue = $this->applyCalibration($value);
                
                return [
                    'raw_value' => $value,
                    'calibrated_value' => $calibratedValue,
                    'unit' => $unit,
                    'raw_data' => $rawData,
                    'device_id' => $this->serial_number,
                ];
            }
        }

        return null;
    }
}