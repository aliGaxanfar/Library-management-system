<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $primaryKey = 'notifId';

    protected $fillable = [
        'memberId', 'type', 'title', 'message',
        'isRead', 'deliveredAt', 'channel',
    ];

    protected $casts = [
        'isRead'      => 'boolean',
        'deliveredAt' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'memberId');
    }
}
