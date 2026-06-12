<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Immunization extends Model
{
    protected $fillable = [
        'child_id',
        'user_id',
        'immunization_schedule_id',
        'vaccine_name',
        'vaccine_type',
        'dose_number',
        'batch_number',
        'date_administered',
        'next_due_date',
        'status',
        'site',
        'route',
        'dose_volume',
        'adverse_reactions',
        'notes',
        'health_facility',
        'health_worker_name',
        'administered_by',
    ];

    protected $casts = [
        'dose_volume' => 'decimal:2',
        'date_administered' => 'datetime',
        'next_due_date' => 'datetime',
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

    public function immunizationSchedule(): BelongsTo
    {
        return $this->belongsTo(ImmunizationSchedule::class);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'scheduled' => 'Scheduled',
            'administered' => 'Administered',
            'missed' => 'Missed',
            'cancelled' => 'Cancelled',
        ];
        
        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute(): string
    {
        $colors = [
            'scheduled' => 'blue',
            'administered' => 'green',
            'missed' => 'red',
            'cancelled' => 'gray',
        ];
        
        return $colors[$this->status] ?? 'gray';
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'administered' || $this->status === 'cancelled') {
            return false;
        }
        
        if (!$this->next_due_date) {
            return false;
        }
        
        return Carbon::parse($this->next_due_date)->isPast();
    }

    public function getIsDueAttribute(): bool
    {
        if ($this->status !== 'scheduled') {
            return false;
        }
        
        if (!$this->next_due_date) {
            return false;
        }
        
        $dueDate = Carbon::parse($this->next_due_date);
        $today = Carbon::today();
        
        // Due if today is within 7 days of due date
        return $dueDate->between($today->copy()->subDays(7), $today->copy()->addDays(7));
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeAdministered($query)
    {
        return $query->where('status', 'administered');
    }

    public function scopeMissed($query)
    {
        return $query->where('status', 'missed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'scheduled')
                     ->where('next_due_date', '<', Carbon::today());
    }

    public function scopeDue($query)
    {
        $today = Carbon::today();
        $weekFromNow = $today->copy()->addDays(7);
        $weekAgo = $today->copy()->subDays(7);
        
        return $query->where('status', 'scheduled')
                     ->whereBetween('next_due_date', [$weekAgo, $weekFromNow]);
    }

    public function scopeUpcoming($query, $days = 30)
    {
        $today = Carbon::today();
        $futureDate = $today->copy()->addDays($days);
        
        return $query->where('status', 'scheduled')
                     ->whereBetween('next_due_date', [$today, $futureDate]);
    }

    public function scopeByVaccine($query, $vaccineName)
    {
        return $query->where('vaccine_name', $vaccineName);
    }
}