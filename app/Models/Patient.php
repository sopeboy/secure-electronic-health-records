<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'gender', 'date_of_birth', 'phone', 'address',
    ];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(function (Patient $patient) {
            // Temporary unique value until the id exists.
            $patient->patient_number ??= 'TMP-' . uniqid('', true);
        });

        // Give every new patient a number like SEHRS-000001.
        static::created(function (Patient $patient) {
            if (str_starts_with($patient->patient_number, 'TMP-')) {
                $patient->patient_number = 'SEHRS-' . str_pad((string) $patient->id, 6, '0', STR_PAD_LEFT);
                $patient->saveQuietly();
            }
        });
    }

    /**
     * Only the patients this user may see in lists and searches.
     * Admin and lab staff: all patients. Doctors and nurses: assigned patients only.
     * Usage: Patient::visibleTo($request->user())->paginate(15)
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->hasRole(Role::ADMIN, Role::LAB)) {
            return $query;
        }

        if ($user->hasRole(Role::DOCTOR, Role::NURSE)) {
            return $query->whereHas('assignedStaff', fn (Builder $q) => $q->where('users.id', $user->id));
        }

        return $query->whereRaw('1 = 0');   // no role → nothing
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /** Doctors and nurses assigned to this patient. */
    public function assignedStaff(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'patient_assignments')->withTimestamps();
    }

    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class)->latest();
    }

    public function vitals(): HasMany
    {
        return $this->hasMany(Vital::class)->latest();
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class)->latest();
    }

    public function labResults(): HasMany
    {
        return $this->hasMany(LabResult::class)->latest();
    }
}
