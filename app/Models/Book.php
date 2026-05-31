<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $primaryKey = 'bookId';

    protected $fillable = [
        'isbn', 'title', 'publishedYear',
        'totalCopies', 'availableCopies', 'location', 'status',
    ];

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'book_author', 'bookId', 'authorId');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category', 'bookId', 'categoryId');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class, 'bookId', 'bookId');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'bookId', 'bookId');
    }

    public function isAvailable(): bool
    {
        return $this->availableCopies > 0;
    }

    public function getAuthorNamesAttribute(): string
    {
        return $this->authors->map(fn($a) => $a->fullName)->join(', ');
    }
}
