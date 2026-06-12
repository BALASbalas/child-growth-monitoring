<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImmunizationSchedule extends Model
{
    protected $fillable = [
        'name',
        'vaccine_name',
        'vaccine_type',
        'due_age_weeks',
        'due_age_months',
        'priority_order',
        'route',
        'dose_volume',
        'description',
        'contraindications',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'dose_volume' => 'decimal:2',
    ];

    // Relationships
    public function immunizations(): HasMany
    {
        return $this->hasMany(Immunization::class);
    }

    // Accessors
    public function getDueAgeStringAttribute(): string
    {
        if ($this->due_age_weeks !== null && $this->due_age_weeks > 0) {
            return "{$this->due_age_weeks} week(s)";
        }
        
        if ($this->due_age_months !== null && $this->due_age_months > 0) {
            if ($this->due_age_months >= 12) {
                $years = floor($this->due_age_months / 12);
                $remainingMonths = $this->due_age_months % 12;
                if ($remainingMonths > 0) {
                    return "{$years} year(s) and {$remainingMonths} month(s)";
                }
                return "{$years} year(s)";
            }
            return "{$this->due_age_months} month(s)";
        }
        
        return 'At birth';
    }

    public function getFullDescriptionAttribute(): string
    {
        $description = "{$this->vaccine_name}";
        
        if ($this->vaccine_type) {
            $description .= " ({$this->vaccine_type})";
        }
        
        $description .= " - {$this->due_age_string}";
        
        return $description;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByVaccine($query, $vaccineName)
    {
        return $query->where('vaccine_name', $vaccineName);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('priority_order')->orderBy('due_age_weeks')->orderBy('due_age_months');
    }

    public function scopeDueAtAge($query, $ageInMonths)
    {
        return $query->where(function($q) use ($ageInMonths) {
            $q->where('due_age_months', $ageInMonths)
              ->orWhere('due_age_weeks', round($ageInMonths * 4.33)); // Approximate weeks
        });
    }

    // Static methods for common vaccine schedules
    public static function getStandardSchedules()
    {
        return [
            [
                'name' => 'BCG at birth',
                'vaccine_name' => 'BCG',
                'vaccine_type' => 'BCG',
                'due_age_weeks' => 0,
                'due_age_months' => 0,
                'priority_order' => 1,
                'route' => 'SC',
                'dose_volume' => 0.05,
                'description' => 'Bacillus Calmette-Guérin vaccine against tuberculosis',
                'contraindications' => 'HIV-positive child, immunodeficiency',
            ],
            [
                'name' => 'OPV 0 at birth',
                'vaccine_name' => 'OPV',
                'vaccine_type' => 'Oral Polio Vaccine',
                'due_age_weeks' => 0,
                'due_age_months' => 0,
                'priority_order' => 2,
                'route' => 'Oral',
                'dose_volume' => 2,
                'description' => 'Oral Polio Vaccine birth dose',
                'contraindications' => 'None',
            ],
            [
                'name' => 'HepB at birth',
                'vaccine_name' => 'HepB',
                'vaccine_type' => 'Hepatitis B',
                'due_age_weeks' => 0,
                'due_age_months' => 0,
                'priority_order' => 3,
                'route' => 'IM',
                'dose_volume' => 0.5,
                'description' => 'Hepatitis B birth dose',
                'contraindications' => 'Severe allergic reaction to previous dose',
            ],
            [
                'name' => 'Pentavalent 1 at 6 weeks',
                'vaccine_name' => 'Pentavalent',
                'vaccine_type' => 'DTP-HepB-Hib',
                'due_age_weeks' => 6,
                'due_age_months' => 1.5,
                'priority_order' => 4,
                'route' => 'IM',
                'dose_volume' => 0.5,
                'description' => 'First dose of Pentavalent (Diphtheria, Pertussis, Tetanus, Hepatitis B, Haemophilus influenzae type b)',
                'contraindications' => 'Severe reaction to previous dose',
            ],
            [
                'name' => 'OPV 1 at 6 weeks',
                'vaccine_name' => 'OPV',
                'vaccine_type' => 'Oral Polio Vaccine',
                'due_age_weeks' => 6,
                'due_age_months' => 1.5,
                'priority_order' => 5,
                'route' => 'Oral',
                'dose_volume' => 2,
                'description' => 'First dose of Oral Polio Vaccine',
                'contraindications' => 'None',
            ],
            [
                'name' => 'PCV 1 at 6 weeks',
                'vaccine_name' => 'PCV',
                'vaccine_type' => 'Pneumococcal Conjugate Vaccine',
                'due_age_weeks' => 6,
                'due_age_months' => 1.5,
                'priority_order' => 6,
                'route' => 'IM',
                'dose_volume' => 0.5,
                'description' => 'First dose of Pneumococcal Conjugate Vaccine',
                'contraindications' => 'Severe reaction to previous dose',
            ],
            [
                'name' => 'Rotavirus 1 at 6 weeks',
                'vaccine_name' => 'Rotavirus',
                'vaccine_type' => 'Rotavirus Vaccine',
                'due_age_weeks' => 6,
                'due_age_months' => 1.5,
                'priority_order' => 7,
                'route' => 'Oral',
                'dose_volume' => 1.5,
                'description' => 'First dose of Rotavirus Vaccine',
                'contraindications' => 'History of intussusception, severe immunodeficiency',
            ],
            [
                'name' => 'Pentavalent 2 at 10 weeks',
                'vaccine_name' => 'Pentavalent',
                'vaccine_type' => 'DTP-HepB-Hib',
                'due_age_weeks' => 10,
                'due_age_months' => 2.5,
                'priority_order' => 8,
                'route' => 'IM',
                'dose_volume' => 0.5,
                'description' => 'Second dose of Pentavalent',
                'contraindications' => 'Severe reaction to previous dose',
            ],
            [
                'name' => 'OPV 2 at 10 weeks',
                'vaccine_name' => 'OPV',
                'vaccine_type' => 'Oral Polio Vaccine',
                'due_age_weeks' => 10,
                'due_age_months' => 2.5,
                'priority_order' => 9,
                'route' => 'Oral',
                'dose_volume' => 2,
                'description' => 'Second dose of Oral Polio Vaccine',
                'contraindications' => 'None',
            ],
            [
                'name' => 'PCV 2 at 10 weeks',
                'vaccine_name' => 'PCV',
                'vaccine_type' => 'Pneumococcal Conjugate Vaccine',
                'due_age_weeks' => 10,
                'due_age_months' => 2.5,
                'priority_order' => 10,
                'route' => 'IM',
                'dose_volume' => 0.5,
                'description' => 'Second dose of Pneumococcal Conjugate Vaccine',
                'contraindications' => 'Severe reaction to previous dose',
            ],
            [
                'name' => 'Rotavirus 2 at 10 weeks',
                'vaccine_name' => 'Rotavirus',
                'vaccine_type' => 'Rotavirus Vaccine',
                'due_age_weeks' => 10,
                'due_age_months' => 2.5,
                'priority_order' => 11,
                'route' => 'Oral',
                'dose_volume' => 1.5,
                'description' => 'Second dose of Rotavirus Vaccine',
                'contraindications' => 'History of intussusception, severe immunodeficiency',
            ],
            [
                'name' => 'Pentavalent 3 at 14 weeks',
                'vaccine_name' => 'Pentavalent',
                'vaccine_type' => 'DTP-HepB-Hib',
                'due_age_weeks' => 14,
                'due_age_months' => 3.5,
                'priority_order' => 12,
                'route' => 'IM',
                'dose_volume' => 0.5,
                'description' => 'Third dose of Pentavalent',
                'contraindications' => 'Severe reaction to previous dose',
            ],
            [
                'name' => 'OPV 3 at 14 weeks',
                'vaccine_name' => 'OPV',
                'vaccine_type' => 'Oral Polio Vaccine',
                'due_age_weeks' => 14,
                'due_age_months' => 3.5,
                'priority_order' => 13,
                'route' => 'Oral',
                'dose_volume' => 2,
                'description' => 'Third dose of Oral Polio Vaccine',
                'contraindications' => 'None',
            ],
            [
                'name' => 'IPV at 14 weeks',
                'vaccine_name' => 'IPV',
                'vaccine_type' => 'Inactivated Polio Vaccine',
                'due_age_weeks' => 14,
                'due_age_months' => 3.5,
                'priority_order' => 14,
                'route' => 'IM',
                'dose_volume' => 0.5,
                'description' => 'Inactivated Polio Vaccine',
                'contraindications' => 'Severe allergic reaction to previous dose',
            ],
            [
                'name' => 'PCV 3 at 14 weeks',
                'vaccine_name' => 'PCV',
                'vaccine_type' => 'Pneumococcal Conjugate Vaccine',
                'due_age_weeks' => 14,
                'due_age_months' => 3.5,
                'priority_order' => 15,
                'route' => 'IM',
                'dose_volume' => 0.5,
                'description' => 'Third dose of Pneumococcal Conjugate Vaccine',
                'contraindications' => 'Severe reaction to previous dose',
            ],
            [
                'name' => 'Measles 1 at 9 months',
                'vaccine_name' => 'Measles',
                'vaccine_type' => 'Measles-Rubella',
                'due_age_weeks' => 39,
                'due_age_months' => 9,
                'priority_order' => 16,
                'route' => 'SC',
                'dose_volume' => 0.5,
                'description' => 'First dose of Measles-Rubella vaccine',
                'contraindications' => 'Pregnancy, severe immunodeficiency',
            ],
            [
                'name' => 'Vitamin A at 9 months',
                'vaccine_name' => 'Vitamin A',
                'vaccine_type' => 'Vitamin A Supplement',
                'due_age_weeks' => 39,
                'due_age_months' => 9,
                'priority_order' => 17,
                'route' => 'Oral',
                'dose_volume' => 100000,
                'description' => 'Vitamin A supplementation (100,000 IU)',
                'contraindications' => 'None',
            ],
            [
                'name' => 'Measles 2 at 15 months',
                'vaccine_name' => 'Measles',
                'vaccine_type' => 'Measles-Rubella',
                'due_age_weeks' => 65,
                'due_age_months' => 15,
                'priority_order' => 18,
                'route' => 'SC',
                'dose_volume' => 0.5,
                'description' => 'Second dose of Measles-Rubella vaccine',
                'contraindications' => 'Pregnancy, severe immunodeficiency',
            ],
            [
                'name' => 'Vitamin A at 15 months',
                'vaccine_name' => 'Vitamin A',
                'vaccine_type' => 'Vitamin A Supplement',
                'due_age_weeks' => 65,
                'due_age_months' => 15,
                'priority_order' => 19,
                'route' => 'Oral',
                'dose_volume' => 200000,
                'description' => 'Vitamin A supplementation (200,000 IU)',
                'contraindications' => 'None',
            ],
        ];
    }
}