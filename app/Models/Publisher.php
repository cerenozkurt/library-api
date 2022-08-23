<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publisher extends Model
{
    use HasFactory;
    protected $table = 'publishers';
    protected $fillable = ['name'];

    
    public function books()
    {
        return $this->hasMany(Books::class);
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
