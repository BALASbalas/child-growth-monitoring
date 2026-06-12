<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class GrowthMeasurement extends Model
{
    protected $fillable = [
        'child_id',
        'user_id',
        'measurement_date',
        'weight',
        'height',
        'head_circumference',
        'mid_upper_arm_circumference',
        'temperature',
        'age_in_months',
        'weight_for_age_zscore',
        'height_for_age_zscore',
        'weight_for_height_zscore',
        'bmi',
        'bmi_for_age_zscore',
        'nutritional_status',
        'stunting_status',
        'wasting_status',
        'clinical_notes',
        'is_from_device',
        'device_id',
    ];

    protected $casts = [
        'is_from_device' => 'boolean',
        'weight' => 'decimal:3',
        'height' => 'decimal:2',
        'head_circumference' => 'decimal:2',
        'mid_upper_arm_circumference' => 'decimal:2',
        'temperature' => 'decimal:1',
        'weight_for_age_zscore' => 'decimal:2',
        'height_for_age_zscore' => 'decimal:2',
        'weight_for_height_zscore' => 'decimal:2',
        'bmi' => 'decimal:2',
        'bmi_for_age_zscore' => 'decimal:2',
        'measurement_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function child(): BelongsTo
    {
        return $this->belongsTo(Child::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getWeightKgAttribute(): ?string
    {
        if (!$this->weight) {
            return null;
        }
        return number_format($this->weight, 3) . ' kg';
    }

    public function getHeightCmAttribute(): ?string
    {
        if (!$this->height) {
            return null;
        }
        return number_format($this->height, 1) . ' cm';
    }

    public function getBmiCategoryAttribute(): ?string
    {
        if (!$this->bmi) {
            return null;
        }
        
        // For adults, but we'll use WHO child standards via z-scores
        if ($this->bmi < 18.5) return 'Underweight';
        if ($this->bmi < 25) return 'Normal';
        if ($this->bmi < 30) return 'Overweight';
        return 'Obese';
    }

    public function getZScoreInterpretationAttribute(): array
    {
        $interpretations = [];
        
        if ($this->weight_for_age_zscore !== null) {
            $interpretations['waz'] = $this->interpretZScore($this->weight_for_age_zscore, 'Weight-for-age');
        }
        
        if ($this->height_for_age_zscore !== null) {
            $interpretations['haz'] = $this->interpretZScore($this->height_for_age_zscore, 'Height-for-age');
        }
        
        if ($this->weight_for_height_zscore !== null) {
            $interpretations['whz'] = $this->interpretZScore($this->weight_for_height_zscore, 'Weight-for-height');
        }
        
        return $interpretations;
    }

    private function interpretZScore(float $zscore, string $parameter): string
    {
        if ($zscore < -3) {
            return "{$parameter}: Severe malnutrition (Z < -3)";
        } elseif ($zscore < -2) {
            return "{$parameter}: Moderate malnutrition (-3 ≤ Z < -2)";
        } elseif ($zscore < 1) {
            return "{$parameter}: Normal (-2 ≤ Z < 1)";
        } elseif ($zscore < 2) {
            return "{$parameter}: Possible risk of overweight (1 ≤ Z < 2)";
        } elseif ($zscore < 3) {
            return "{$parameter}: Overweight (2 ≤ Z < 3)";
        } else {
            return "{$parameter}: Obese (Z ≥ 3)";
        }
    }

    // Scopes
    public function scopeFromDate($query, $date)
    {
        return $query->where('measurement_date', '>=', $date);
    }

    public function scopeUntilDate($query, $date)
    {
        return $query->where('measurement_date', '<=', $date);
    }

    public function scopeDeviceMeasurements($query)
    {
        return $query->where('is_from_device', true);
    }

    public function scopeManualMeasurements($query)
    {
        return $query->where('is_from_device', false);
    }

    public function scopeWithAbnormalZScores($query)
    {
        return $query->where(function($q) {
            $q->where('weight_for_age_zscore', '<', -2)
              ->orWhere('height_for_age_zscore', '<', -2)
              ->orWhere('weight_for_height_zscore', '<', -2)
              ->orWhere('weight_for_age_zscore', '>', 2)
              ->orWhere('height_for_age_zscore', '>', 2)
              ->orWhere('weight_for_height_zscore', '>', 2);
        });
    }

    // Calculate BMI if weight and height are provided
    public function calculateBmi(): ?float
    {
        if (!$this->weight || !$this->height) {
            return null;
        }
        
        $heightInMeters = $this->height / 100;
        $this->bmi = round($this->weight / ($heightInMeters * $heightInMeters), 2);
        
        return $this->bmi;
    }
}