<?php

namespace App\Services;

use App\Models\WHOGrowthStandard;
use App\Models\GrowthMeasurement;
use App\Models\Child;
use Carbon\Carbon;

class WHOGrowthService
{
    /**
     * Calculate age in months from date of birth
     */
    public function calculateAgeInMonths(string $dateOfBirth): int
    {
        $birthDate = Carbon::parse($dateOfBirth);
        $now = Carbon::now();
        
        $ageInMonths = ($now->year - $birthDate->year) * 12 + ($now->month - $birthDate->month);
        
        if ($now->day < $birthDate->day) {
            $ageInMonths--;
        }
        
        return max(0, min($ageInMonths, 60)); // WHO standards go up to 60 months
    }

    /**
     * Get the appropriate WHO growth standard for a child
     */
    public function getWHOStandard(string $sex, int $ageInMonths): ?WHOGrowthStandard
    {
        $standard = WHOGrowthStandard::where('sex', $sex)
            ->where('age_in_months', $ageInMonths)
            ->first();

        // If no exact match, find the closest available age
        if (!$standard) {
            $standard = WHOGrowthStandard::where('sex', $sex)
                ->orderByRaw('ABS(age_in_months - ' . intval($ageInMonths) . ')')
                ->first();
        }

        return $standard;
    }

    /**
     * Calculate Z-scores for a growth measurement
     */
    public function calculateZScores(GrowthMeasurement $measurement, Child $child): array
    {
        $ageInMonths = $this->calculateAgeInMonths($child->date_of_birth);
        $standard = $this->getWHOStandard($child->sex, $ageInMonths);
        
        if (!$standard) {
            // Allow saving measurement even without WHO standards for Z-score calculation
            return [
                'success' => true,
                'message' => 'Measurement saved (no WHO growth standard available for age ' . $ageInMonths . ' months)',
                'z_scores_calculated' => false,
            ];
        }

        $zScores = [
            'age_in_months' => $ageInMonths,
            'standard' => $standard,
        ];

        // Calculate weight-for-age Z-score
        if ($measurement->weight) {
            $waz = $standard->calculateWeightForAgeZScore($measurement->weight);
            if ($waz !== null) {
                $zScores['weight_for_age_zscore'] = $waz;
                $zScores['weight_interpretation'] = WHOGrowthStandard::interpretWeightForAgeZScore($waz);
                $measurement->weight_for_age_zscore = $waz;
            }
        }

        // Calculate height-for-age Z-score
        if ($measurement->height) {
            $haz = $standard->calculateHeightForAgeZScore($measurement->height);
            if ($haz !== null) {
                $zScores['height_for_age_zscore'] = $haz;
                $zScores['height_interpretation'] = WHOGrowthStandard::interpretHeightForAgeZScore($haz);
                $measurement->height_for_age_zscore = $haz;
            }
        }

        // Calculate BMI if weight and height are available
        if ($measurement->weight && $measurement->height) {
            $heightInMeters = $measurement->height / 100;
            $bmi = $measurement->weight / ($heightInMeters * $heightInMeters);
            $measurement->bmi = round($bmi, 2);
            
            $bmiZ = $standard->calculateBmiForAgeZScore($bmi);
            if ($bmiZ !== null) {
                $zScores['bmi_for_age_zscore'] = $bmiZ;
                $measurement->bmi_for_age_zscore = $bmiZ;
            }
        }

        // Calculate weight-for-height Z-score
        if ($measurement->weight && $measurement->height) {
            $whz = $standard->calculateWeightForHeightZScore($measurement->weight, $measurement->height);
            if ($whz !== null) {
                $zScores['weight_for_height_zscore'] = $whz;
                $zScores['wasting_interpretation'] = WHOGrowthStandard::interpretWeightForHeightZScore($whz);
                $measurement->weight_for_height_zscore = $whz;
            }
        }

        // Set nutritional status based on Z-scores
        if (isset($zScores['weight_interpretation'])) {
            $measurement->nutritional_status = $zScores['weight_interpretation']['status'];
        }

        if (isset($zScores['height_interpretation'])) {
            $measurement->stunting_status = $zScores['height_interpretation']['status'];
        }

        // Map interpretation status to database ENUM-compatible values
        if (isset($zScores['wasting_interpretation'])) {
            $wastingStatus = $zScores['wasting_interpretation']['status'];
            // Map to ENUM values: severe, moderate, normal
            $wastingEnumMap = [
                'severe_wasting' => 'severe',
                'moderate_wasting' => 'moderate',
                'normal' => 'normal',
                'overweight' => 'normal',
                'obese' => 'normal',
            ];
            $measurement->wasting_status = $wastingEnumMap[$wastingStatus] ?? 'normal';
        }

        $zScores['success'] = true;
        $zScores['message'] = 'Z-scores calculated successfully';

        return $zScores;
    }

    /**
     * Get growth chart data for a child
     */
    public function getGrowthChartData(Child $child, string $parameter = 'weight'): array
    {
        $measurements = $child->growthMeasurements()
            ->orderBy('measurement_date')
            ->get();

        $ageInMonths = $this->calculateAgeInMonths($child->date_of_birth);
        $standard = $this->getWHOStandard($child->sex, $ageInMonths);

        $chartData = [
            'measurements' => [],
            'who_standards' => [],
            'child_info' => [
                'name' => $child->full_name,
                'sex' => $child->sex,
                'date_of_birth' => $child->date_of_birth,
                'current_age_months' => $ageInMonths,
            ],
        ];

        foreach ($measurements as $measurement) {
            $measurementAge = $this->calculateAgeInMonths($measurement->measurement_date);
            $chartData['measurements'][] = [
                'date' => $measurement->measurement_date,
                'age_months' => $measurementAge,
                'value' => $measurement->{$parameter},
                'z_score' => $measurement->{"{$parameter}_for_age_zscore"},
            ];
        }

        if ($standard) {
            $chartData['who_standards'] = [
                'median' => $standard->{"{$parameter}_median"},
                'minus_3sd' => $standard->{"{$parameter}_minus_3sd"},
                'minus_2sd' => $standard->{"{$parameter}_minus_2sd"},
                'minus_1sd' => $standard->{"{$parameter}_minus_1sd"},
                'plus_1sd' => $standard->{"{$parameter}_plus_1sd"},
                'plus_2sd' => $standard->{"{$parameter}_plus_2sd"},
                'plus_3sd' => $standard->{"{$parameter}_plus_3sd"},
            ];
        }

        return $chartData;
    }

    /**
     * Get growth velocity (rate of change)
     */
    public function calculateGrowthVelocity(Child $child, string $parameter = 'height', int $monthsBack = 6): ?array
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subMonths($monthsBack);

        $measurements = $child->growthMeasurements()
            ->whereBetween('measurement_date', [$startDate, $endDate])
            ->whereNotNull($parameter)
            ->orderBy('measurement_date')
            ->get();

        if ($measurements->count() < 2) {
            return null;
        }

        $firstMeasurement = $measurements->first();
        $lastMeasurement = $measurements->last();

        $valueChange = $lastMeasurement->{$parameter} - $firstMeasurement->{$parameter};
        $timeChange = Carbon::parse($firstMeasurement->measurement_date)->diffInMonths($lastMeasurement->measurement_date);

        if ($timeChange == 0) {
            return null;
        }

        $velocity = $valueChange / $timeChange;

        return [
            'parameter' => $parameter,
            'start_value' => $firstMeasurement->{$parameter},
            'end_value' => $lastMeasurement->{$parameter},
            'value_change' => $valueChange,
            'time_months' => $timeChange,
            'velocity_per_month' => round($velocity, 3),
            'start_date' => $firstMeasurement->measurement_date,
            'end_date' => $lastMeasurement->measurement_date,
        ];
    }

    /**
     * Check for alarming growth patterns
     */
    public function checkAlarmingPatterns(Child $child): array
    {
        $alerts = [];
        $recentMeasurements = $child->growthMeasurements()
            ->orderBy('measurement_date', 'desc')
            ->limit(3)
            ->get();

        if ($recentMeasurements->count() < 2) {
            return $alerts;
        }

        $latest = $recentMeasurements->first();
        $previous = $recentMeasurements->get(1);

        // Check for significant weight loss
        if ($latest->weight && $previous->weight) {
            $weightChange = $latest->weight - $previous->weight;
            if ($weightChange < -0.5) { // More than 500g loss
                $alerts[] = [
                    'type' => 'weight_loss',
                    'severity' => 'high',
                    'message' => 'Significant weight loss detected: ' . number_format(abs($weightChange), 3) . ' kg',
                    'value' => $weightChange,
                ];
            }
        }

        // Check for declining Z-scores
        if ($latest->weight_for_age_zscore && $previous->weight_for_age_zscore) {
            $zScoreChange = $latest->weight_for_age_zscore - $previous->weight_for_age_zscore;
            if ($zScoreChange < -0.5) {
                $alerts[] = [
                    'type' => 'declining_waz',
                    'severity' => 'medium',
                    'message' => 'Weight-for-age Z-score declining',
                    'value' => $zScoreChange,
                ];
            }
        }

        // Check for severe malnutrition indicators
        if ($latest->weight_for_age_zscore < -3) {
            $alerts[] = [
                'type' => 'severe_underweight',
                'severity' => 'critical',
                'message' => 'Severe underweight (WAZ < -3)',
                'value' => $latest->weight_for_age_zscore,
            ];
        }

        if ($latest->height_for_age_zscore < -3) {
            $alerts[] = [
                'type' => 'severe_stunting',
                'severity' => 'critical',
                'message' => 'Severe stunting (HAZ < -3)',
                'value' => $latest->height_for_age_zscore,
            ];
        }

        return $alerts;
    }
}