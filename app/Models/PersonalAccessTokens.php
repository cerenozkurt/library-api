<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;


class PersonalAccessTokens extends Model
{
    use HasFactory, Prunable;

    public function prunable()
    {
        return static::where('last_used_at', '<=', now()->subMonths(5));
    }
}
