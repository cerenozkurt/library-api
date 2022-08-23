<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    use HasFactory;
    protected $table = 'authors';
    protected $fillable = ['name','read_count'];
    

    public function books()
    {
        return $this->hasMany(Books::class);
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
        return $query->where('name', 'LIKE', '%' . $search . '%')->orderBy('id', 'desc');
    }

    public function scopeName($query, $name)
    {
        return $query->where('name', $name);
    }

    

}


