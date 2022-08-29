<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBook extends Model
{
    use HasFactory;
    protected $table = 'user_books';
    protected $fillable = ['user_id','book_id','status','comment'];
    

    public function scopeUserBook($query,$id)
    {
        return $query->where('user_id', $id);
    }
}
