<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $primaryKey = 'userId';

    protected $fillable = [
        'fullName', 'email', 'passwordHash', 'role', 'isActive',
    ];

    protected $hidden = ['passwordHash', 'remember_token'];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    // Tell Laravel where the real password column is
    public function getAuthPassword(): string
    {
        return $this->passwordHash;
    }

    // Allow Auth::user()->password to work too
    public function getPasswordAttribute(): string
    {
        return $this->passwordHash;
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'userId', 'userId');
    }

    public function librarian()
    {
        return $this->hasOne(Librarian::class, 'userId', 'userId');
    }

    public function member()
    {
        return $this->hasOne(Member::class, 'userId', 'userId');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'performedBy', 'userId');
    }

    public function isAdmin(): bool     { return $this->role === 'ADMIN'; }
    public function isLibrarian(): bool { return $this->role === 'LIBRARIAN'; }
    public function isMember(): bool    { return $this->role === 'MEMBER'; }
}