<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Librarian extends Model
{
    protected $fillable = ['userId', 'employeeId', 'department'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'userId');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'librarianId');
    }
}
