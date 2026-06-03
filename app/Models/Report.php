<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $primaryKey = 'reportId';

    protected $fillable = [
        'adminId', 'title', 'type', 'generatedAt', 'format', 'parameters',
    ];

    protected $casts = [
        'generatedAt' => 'datetime',
        'parameters'  => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'adminId');
    }
}
