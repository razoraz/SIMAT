<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessAuditLog extends Model
{
    protected $table = 'access_audit_logs';

    protected $fillable = [
        'user_id',
        'actor_id',
        'action_type',
        'summary',
        'old_permissions',
        'new_permissions',
        'old_role',
        'new_role',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_permissions' => 'array',
        'new_permissions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
