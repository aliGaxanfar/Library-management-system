<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $primaryKey = 'fineId';

    protected $fillable = [
        'loanId', 'memberId', 'amount', 'reason',
        'issuedDate', 'paidDate', 'status',
    ];

    protected $casts = [
        'issuedDate' => 'date',
        'paidDate'   => 'date',
        'amount'     => 'decimal:2',
    ];

    const DAILY_RATE = 0.50; // £0.50 per day overdue

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'loanId', 'loanId');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'memberId');
    }

    public static function calculateAmount(int $daysOverdue): float
    {
        return round($daysOverdue * self::DAILY_RATE, 2);
    }
}
