<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable = [
        'userId', 'memberId', 'membershipType',
        'joinDate', 'expiryDate', 'totalFinesDue',
    ];

    protected $casts = [
        'joinDate'   => 'date',
        'expiryDate' => 'date',
        'totalFinesDue' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'userId');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'memberId');
    }

    public function fines()
    {
        return $this->hasMany(Fine::class, 'memberId');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'memberId');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'memberId');
    }

    public function activeLoans()
    {
        return $this->loans()->whereIn('status', ['ACTIVE', 'OVERDUE']);
    }

    public function pendingFines()
    {
        return $this->fines()->where('status', 'PENDING');
    }
}
