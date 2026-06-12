<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Carbon\Carbon;

class Child extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'unique_id',
        'date_of_birth',
        'sex',
        'gestational_age_weeks',
        'birth_weight',
        'birth_length',
        'birth_head_circumference',
        'mother_name',
        'mother_phone',
        'father_name',
        'father_phone',
        'guardian_name',
        'guardian_phone',
        'address',
        'location',
        'district',
        'region',
        'medical_history',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date:Y-m-d',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function growthMeasurements(): HasMany
    {
        return $this->hasMany(GrowthMeasurement::class);
    }

    public function immunizations(): HasMany
    {
        return $this->hasMany(Immunization::class);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        $name = $this->first_name . ' ' . $this->last_name;
        if ($this->middle_name) {
            $name = $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
        }
        return $name;
    }

    public function getAgeInMonthsAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        
        $birthDate = Carbon::parse($this->date_of_birth);
        $now = Carbon::now();
        
        // Use Carbon's built-in diffInMonths for accurate calculation
        // This properly handles day-level precision
        return max(0, $birthDate->diffInMonths($now));
    }

    public function getAgeInYearsAttribute(): ?float
    {
        if (!$this->date_of_birth) {
            return null;
        }
        
        $birthDate = Carbon::parse($this->date_of_birth);
        
        // Use Carbon's built-in age calculation for accurate years
        return $birthDate->diffInYears(Carbon::now());
    }

    public function getAgeStringAttribute(): string
    {
        $months = $this->age_in_months;
        
        if ($months === null) {
            return 'Unknown age';
        }
        
        if ($months < 1) {
            $days = Carbon::now()->diffInDays(Carbon::parse($this->date_of_birth));
            return "{$days} days";
        }
        
        if ($months >= 24) {
            $years = floor($months / 12);
            $remainingMonths = $months % 12;
            if ($remainingMonths > 0) {
                return "{$years} year(s) and {$remainingMonths} month(s)";
            }
            return "{$years} year(s)";
        }
        
        return "{$months} month(s)";
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMale($query)
    {
        return $query->where('sex', 'male');
    }

    public function scopeFemale($query)
    {
        return $query->where('sex', 'female');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('middle_name', 'like', "%{$search}%")
              ->orWhere('unique_id', 'like', "%{$search}%");
        });
    }
}