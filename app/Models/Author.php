<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $primaryKey = 'authorId';

    protected $fillable = ['firstName', 'lastName', 'nationality', 'bio'];

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_author', 'authorId', 'bookId');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->firstName} {$this->lastName}";
    }
}
