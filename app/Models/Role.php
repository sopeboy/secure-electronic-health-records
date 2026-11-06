<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const ADMIN  = 'admin';
    public const DOCTOR = 'doctor';
    public const NURSE  = 'nurse';
    public const LAB    = 'lab';

    protected $fillable = ['name', 'label', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
