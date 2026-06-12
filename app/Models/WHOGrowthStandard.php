<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WHOGrowthStandard extends Model
{
    protected $table = 'who_growth_standards';

    protected $fillable = [
        'sex',
        'age_in_months',
        'age_in_days',
        'weight_median',
        'weight_l',
        'weight_m',
        'weight_s',
        'weight_minus_3sd',
        'weight_minus_2sd',
        'weight_minus_1sd',
        'weight_plus_1sd',
        'weight_plus_2sd',
        'weight_plus_3sd',
        'height_median',
        'height_l',
        'height_m',
        'height_s',
        'height_minus_3sd',
        'height_minus_2sd',
        'height_minus_1sd',
        'height_plus_1sd',
        'height_plus_2sd',
        'height_plus_3sd',
        'bmi_median',
        'bmi_l',
        'bmi_m',
        'bmi_s',
        'bmi_minus_3sd',
        'bmi_minus_2sd',
        'bmi_minus_1sd',
        'bmi_plus_1sd',
        'bmi_plus_2sd',
        'bmi_plus_3sd',
        'head_circumference_median',
        'head_circumference_l',
        'head_circumference_m',
        'head_circumference_s',
        'head_circumference_minus_3sd',
        'head_circumference_minus_2sd',
        'head_circumference_minus_1sd',
        'head_circumference_plus_1sd',
        'head_circumference_plus_2sd',
        'head_circumference_plus_3sd',
    ];

    protected $casts = [
        'age_in_months' => 'integer',
        'age_in_days' => 'integer',
    ];

    /**
     * Calculate Z-score using the LMS method
     * Z = ((X/M)^L - 1) / (L * S)
     * 
     * @param float $measurement The actual measurement value
     * @param string $parameter The parameter type (weight, height, bmi, head_circumference)
     * @return float|null The calculated Z-score
     */
    public function calculateZScore(float $measurement, string $parameter): ?float
    {
        $l = $this->{"{$parameter}_l"};
        $m = $this->{"{$parameter}_m"};
        $s = $this->{"{$parameter}_s"};
        
        if ($l == 0 || $m == 0 || $s == 0) {
            return null;
        }
        
        // Using the LMS method: Z = ((X/M)^L - 1) / (L * S)
        $zScore = (pow($measurement / $m, $l) - 1) / ($l * $s);
        
        return round($zScore, 2);
    }

    /**
     * Calculate weight-for-age Z-score
     */
    public function calculateWeightForAgeZScore(float $weight): ?float
    {
        return $this->calculateZScore($weight, 'weight');
    }

    /**
     * Calculate height-for-age Z-score
     */
    public function calculateHeightForAgeZScore(float $height): ?float
    {
        return $this->calculateZScore($height, 'height');
    }

    /**
     * Calculate BMI-for-age Z-score
     */
    public function calculateBmiForAgeZScore(float $bmi): ?float
    {
        return $this->calculateZScore($bmi, 'bmi');
    }

    /**
     * Calculate weight-for-height Z-score (using height as the reference)
     * This is a simplified version - in practice, you'd need a separate table
     */
    public function calculateWeightForHeightZScore(float $weight, float $height): ?float
    {
        // For weight-for-height, we need to find the median weight for this height
        // This is a simplified calculation
        $expectedBmi = $this->bmi_median;
        $expectedWeight = $expectedBmi * ($height / 100) * ($height / 100);
        
        if ($expectedWeight == 0) {
            return null;
        }
        
        $zScore = ($weight - $expectedWeight) / ($expectedWeight * $this->weight_s);
        
        return round($zScore, 2);
    }

    /**
     * Interpret the nutritional status based on Z-scores
     */
    public static function interpretWeightForAgeZScore(float $zScore): array
    {
        if ($zScore < -3) {
            return [
                'status' => 'severe_underweight',
                'label' => 'Severe Underweight',
                'description' => 'Severely underweight for age (Z < -3)',
                'color' => 'red',
            ];
        } elseif ($zScore < -2) {
            return [
                'status' => 'moderate_underweight',
                'label' => 'Moderately Underweight',
                'description' => 'Moderately underweight for age (-3 ≤ Z < -2)',
                'color' => 'orange',
            ];
        } elseif ($zScore < 1) {
            return [
                'status' => 'normal',
                'label' => 'Normal',
                'description' => 'Normal weight for age (-2 ≤ Z < 1)',
                'color' => 'green',
            ];
        } elseif ($zScore < 2) {
            return [
                'status' => 'normal',
                'label' => 'Possible Risk',
                'description' => 'Possible risk of overweight (1 ≤ Z < 2)',
                'color' => 'yellow',
            ];
        } elseif ($zScore < 3) {
            return [
                'status' => 'overweight',
                'label' => 'Overweight',
                'description' => 'Overweight for age (2 ≤ Z < 3)',
                'color' => 'orange',
            ];
        } else {
            return [
                'status' => 'obese',
                'label' => 'Obese',
                'description' => 'Obese for age (Z ≥ 3)',
                'color' => 'red',
            ];
        }
    }

    public static function interpretHeightForAgeZScore(float $zScore): array
    {
        if ($zScore < -3) {
            return [
                'status' => 'severe',
                'label' => 'Severe Stunting',
                'description' => 'Severe stunting (Z < -3)',
                'color' => 'red',
            ];
        } elseif ($zScore < -2) {
            return [
                'status' => 'moderate',
                'label' => 'Moderate Stunting',
                'description' => 'Moderate stunting (-3 ≤ Z < -2)',
                'color' => 'orange',
            ];
        } else {
            return [
                'status' => 'normal',
                'label' => 'Normal',
                'description' => 'Normal height for age (Z ≥ -2)',
                'color' => 'green',
            ];
        }
    }

    public static function interpretWeightForHeightZScore(float $zScore): array
    {
        if ($zScore < -3) {
            return [
                'status' => 'severe_wasting',
                'label' => 'Severe Wasting',
                'description' => 'Severe wasting (Z < -3)',
                'color' => 'red',
            ];
        } elseif ($zScore < -2) {
            return [
                'status' => 'moderate_wasting',
                'label' => 'Moderate Wasting',
                'description' => 'Moderate wasting (-3 ≤ Z < -2)',
                'color' => 'orange',
            ];
        } elseif ($zScore < 1) {
            return [
                'status' => 'normal',
                'label' => 'Normal',
                'description' => 'Normal weight for height (-2 ≤ Z < 1)',
                'color' => 'green',
            ];
        } elseif ($zScore < 2) {
            return [
                'status' => 'normal',
                'label' => 'Possible Risk',
                'description' => 'Possible risk of overweight (1 ≤ Z < 2)',
                'color' => 'yellow',
            ];
        } elseif ($zScore < 3) {
            return [
                'status' => 'overweight',
                'label' => 'Overweight',
                'description' => 'Overweight for height (2 ≤ Z < 3)',
                'color' => 'orange',
            ];
        } else {
            return [
                'status' => 'obese',
                'label' => 'Obese',
                'description' => 'Obese for height (Z ≥ 3)',
                'color' => 'red',
            ];
        }
    }

    // Scopes
    public function scopeMale($query)
    {
        return $query->where('sex', 'male');
    }

    public function scopeFemale($query)
    {
        return $query->where('sex', 'female');
    }

    public function scopeForAgeInMonths($query, int $ageInMonths)
    {
        return $query->where('age_in_months', $ageInMonths);
    }

    public function scopeForAgeRange($query, int $minMonths, int $maxMonths)
    {
        return $query->whereBetween('age_in_months', [$minMonths, $maxMonths]);
    }
}