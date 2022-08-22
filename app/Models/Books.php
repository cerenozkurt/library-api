<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Books extends Model
{
    use HasFactory;
    protected $table = 'books';
    protected $fillable = [
        'isbn',
        'name',
        'page_count',
        'publisher_id',
        'category_id',
        'author_id',
        'read_count'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_books', 'book_id', 'user_id');
    }
    public function categorys()
    {
        return $this->belongsTo(Category::class);
    }
    public function authors()
    {
        return $this->belongsTo(Author::class);
    }
    
}
