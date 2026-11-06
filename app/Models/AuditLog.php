<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use LogicException;

/**
 * Append-only audit trail. The app can insert rows but never change or delete them.
 * Only WHO did WHAT to WHICH record is stored — never diagnoses, results or passwords.
 */
class AuditLog extends Model
{
    public const UPDATED_AT = null;   // table has created_at only

    protected $fillable = [
        'actor_id', 'action', 'resource_type', 'resource_id', 'ip_address', 'user_agent',
    ];

    protected static function booted(): void
    {
        static::updating(fn () => throw new LogicException('Audit logs are append-only.'));
        static::deleting(fn () => throw new LogicException('Audit logs are append-only.'));
    }

    /**
     * AuditLog::record('viewed', $patient);
     * AuditLog::record('login_failed', $userOrNull);
     */
    public static function record(string $action, ?Model $resource = null, ?User $actor = null): self
    {
        $request = request();

        return static::create([
            'actor_id'      => $actor?->id ?? Auth::id(),
            'action'        => $action,
            'resource_type' => $resource ? class_basename($resource) : null,
            'resource_id'   => $resource?->getKey(),
            'ip_address'    => $request?->ip(),
            'user_agent'    => Str::limit((string) $request?->userAgent(), 250, ''),
        ]);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
