<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    protected $primaryKey = 'loanId';

    protected $fillable = [
        'memberId', 'bookId', 'librarianId',
        'issueDate', 'dueDate', 'returnDate', 'status', 'renewalCount',
    ];

    protected $casts = [
        'issueDate'  => 'date',
        'dueDate'    => 'date',
        'returnDate' => 'date',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'memberId');
    }

    public function book()
    {
        return $this->belongsTo(Book::class, 'bookId', 'bookId');
    }

    public function librarian()
    {
        return $this->belongsTo(Librarian::class, 'librarianId');
    }

    public function fine()
    {
        return $this->hasOne(Fine::class, 'loanId', 'loanId');
    }

    public function reservation()
    {
        return $this->hasOne(Reservation::class, 'loanId', 'loanId');
    }

    public function isOverdue(): bool
    {
        return $this->returnDate === null && Carbon::today()->isAfter($this->dueDate);
    }

    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) return 0;
        return $this->dueDate->diffInDays(Carbon::today());
    }

    public function renew(): bool
    {
        if ($this->renewalCount >= 2) return false; // max 2 renewals
        $this->dueDate = $this->dueDate->addDays(14);
        $this->renewalCount++;
        $this->save();
        return true;
    }
}
