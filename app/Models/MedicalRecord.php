<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalRecord extends Model
{
    protected $fillable = ['patient_id', 'diagnosis', 'notes'];

    protected function casts(): array
    {
        // Encrypted at rest with APP_KEY (AES-256-CBC). Unreadable in the database.
        return [
            'diagnosis' => 'encrypted',
            'notes'     => 'encrypted',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
