<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    public const STATUS_ACTIVE    = 'active';
    public const STATUS_SUSPENDED = 'suspended';

    // role_id and status are NOT fillable, so a user can never change their own
    // role or status through a form (mass-assignment protection). Admin code sets them explicitly.
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /** Patients this doctor/nurse is assigned to care for. */
    public function assignedPatients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'patient_assignments')->withTimestamps();
    }

    /** Usage: $user->hasRole('doctor') or $user->hasRole('doctor', 'nurse') */
    public function hasRole(string ...$names): bool
    {
        return $this->role !== null && in_array($this->role->name, $names, true);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isAssignedTo(Patient $patient): bool
    {
        return $this->assignedPatients()->whereKey($patient->getKey())->exists();
    }
}
