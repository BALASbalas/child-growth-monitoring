<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'facility_name',
        'license_number',
        'location',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function children()
    {
        return $this->hasMany(Child::class);
    }

    // Role check methods
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isNurse(): bool
    {
        return $this->role === 'nurse';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    public function isGuardian(): bool
    {
        // Alias for isParent() - Guardian and Parent are the same role
        return $this->isParent();
    }

    public function isHealthcareWorker(): bool
    {
        return in_array($this->role, ['admin', 'nurse', 'doctor']);
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function getRoleLabelAttribute(): string
    {
        $labels = [
            'admin' => 'Administrator',
            'nurse' => 'Nurse',
            'doctor' => 'Doctor',
            'parent' => 'Parent',
        ];

        return $labels[$this->role] ?? ucfirst($this->role);
    }
}