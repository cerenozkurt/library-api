<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function books()
    {
        return $this->belongsToMany(Books::class, 'user_books', 'book_id', 'user_id');
    }

    public function role()
    {
        return $this->belongsTo(UserRoles::class);
    }

    public function scopeEmail($query, $email)
    {
        return $query->where('email', $email);
    }

    public function scopeNameList($query)
    {
        return $query->orderby('name')->get();
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', '%' . $search . '%')->orderBy('id', 'desc');
    }

    public function scopeLibrarianList($query)
    {
        return $query->where('role_id', '=', '2')->get();
    }
}
