<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'json',
        'new_values' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log an activity.
     */
    public static function log(string $action, ?string $description = null, ?Model $model = null, ?array $oldValues = null, ?array $newValues = null): self
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Scope a query to only include logs for a specific action.
     */
    public function scopeOfAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to only include logs for a specific model.
     */
    public function scopeOfModel($query, string $modelType, ?int $modelId = null)
    {
        $query->where('model_type', $modelType);
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        return $query;
    }

    /**
     * Scope a query to search logs.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
              ->orWhere('action', 'like', "%{$search}%")
              ->orWhere('ip_address', 'like', "%{$search}%");
        });
    }

    /**
     * Get the action label for display.
     */
    public function getActionLabelAttribute(): string
    {
        $labels = [
            'create' => 'Created',
            'update' => 'Updated',
            'delete' => 'Deleted',
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'export' => 'Exported',
            'backup' => 'Backup Created',
            'restore' => 'Restored',
            'toggle_status' => 'Status Changed',
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Get the action color for display.
     */
    public function getActionColorAttribute(): string
    {
        $colors = [
            'create' => 'bg-emerald-100 text-emerald-800',
            'update' => 'bg-blue-100 text-blue-800',
            'delete' => 'bg-red-100 text-red-800',
            'login' => 'bg-green-100 text-green-800',
            'logout' => 'bg-gray-100 text-gray-800',
            'export' => 'bg-purple-100 text-purple-800',
            'backup' => 'bg-amber-100 text-amber-800',
            'restore' => 'bg-orange-100 text-orange-800',
            'toggle_status' => 'bg-yellow-100 text-yellow-800',
        ];

        return $colors[$this->action] ?? 'bg-gray-100 text-gray-800';
    }
}