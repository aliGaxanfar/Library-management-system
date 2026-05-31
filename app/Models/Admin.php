<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = ['userId', 'adminLevel'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId', 'userId');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'adminId');
    }
}
