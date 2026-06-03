<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $primaryKey = 'logId';
    public $timestamps = false; // immutable, no updated_at

    protected $fillable = [
        'performedBy', 'action', 'tableName',
        'recordId', 'performedAt', 'ipAddress', 'changes',
    ];

    protected $casts = [
        'performedAt' => 'datetime',
        'changes'     => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'performedBy', 'userId');
    }

    /**
     * Static helper to log any action.
     */
    public static function record(int $userId, string $action, string $table, int $recordId, array $changes = [], ?string $ip = null): void
    {
        static::create([
            'performedBy' => $userId,
            'action'      => $action,
            'tableName'   => $table,
            'recordId'    => $recordId,
            'performedAt' => now(),
            'ipAddress'   => $ip,
            'changes'     => $changes,
        ]);
    }
}
