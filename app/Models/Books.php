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

    public function scopeReadCount($query)
    {
        return $query->orderby('read_count', 'desc')->limit(15)->get();
    }

    public function scopeNameList($query)
    {
        return $query->orderby('name')->get();
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(
            'isbn',
            'LIKE',
            '%' . $search . '%'
        )
            ->orWhere('name', 'LIKE', '%' . $search . '%')
            ->orderBy('id', 'desc');
    }

    public function scopeISBN($query, $isbn)
    {
        return $query->where('isbn', $isbn);
    }
}
