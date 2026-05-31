<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'categoryId';

    protected $fillable = ['name', 'description', 'parentCategoryId'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parentCategoryId', 'categoryId');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parentCategoryId', 'categoryId');
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_category', 'categoryId', 'bookId');
    }
}
