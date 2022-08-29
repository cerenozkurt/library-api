<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookQuotes extends Model
{
    use HasFactory;
    protected $table = 'book_quotes';
    protected $fillable = [
        'user_id',
        'book_id',
        'quotes',
        'title',
        'page'
    ];
}
