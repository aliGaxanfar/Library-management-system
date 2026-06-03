<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reservation extends Model
{
    protected $primaryKey = 'reservationId';

    protected $fillable = [
        'memberId', 'bookId', 'loanId',
        'reservationDate', 'expiryDate', 'status', 'queuePosition',
    ];

    protected $casts = [
        'reservationDate' => 'datetime',
        'expiryDate'      => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'memberId');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'bookId', 'bookId');
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loanId', 'loanId');
    }

    public function isExpired(): bool
    {
        return Carbon::now()->isAfter($this->expiryDate);
    }

    public function confirm(): void
    {
        $this->status = 'CONFIRMED';
        $this->save();
    }

    public function cancel(): void
    {
        $this->status = 'CANCELLED';
        $this->save();
    }
}
